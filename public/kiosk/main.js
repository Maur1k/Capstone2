// Optimized 3D Wayfinding Kiosk with memory management and performance improvements

const API_BASE = (window && window.KIOSK_API_BASE) ? window.KIOSK_API_BASE : `${location.origin}/api`;
const FORCE_GLB = (window && window.KIOSK_FORCE_GLB) ? true : false;
const DRACO_DECODER_PATH = (window && window.KIOSK_DRACO_PATH) ? window.KIOSK_DRACO_PATH : 'https://www.gstatic.com/draco/v1/decoders/';
const GLB_SCALE = (window && window.KIOSK_GLB_SCALE) ? Number(window.KIOSK_GLB_SCALE) : 1.0;
const TARGET_MODEL_EXTENT = 600;

// Performance monitoring
const PERFORMANCE_MONITOR = {
    startTime: performance.now(),
    memoryUsage: [],
    frameCount: 0,
    
    logMemory() {
        if (performance.memory) {
            this.memoryUsage.push({
                timestamp: Date.now(),
                used: performance.memory.usedJSHeapSize,
                total: performance.memory.totalJSHeapSize,
                limit: performance.memory.jsHeapSizeLimit
            });
            
            // Keep only last 100 measurements
            if (this.memoryUsage.length > 100) {
                this.memoryUsage.shift();
            }
        }
    },
    
    getMemoryStats() {
        if (this.memoryUsage.length === 0) return null;
        const latest = this.memoryUsage[this.memoryUsage.length - 1];
        return {
            used: Math.round(latest.used / 1024 / 1024) + 'MB',
            total: Math.round(latest.total / 1024 / 1024) + 'MB',
            limit: Math.round(latest.limit / 1024 / 1024) + 'MB'
        };
    }
};

// Three.js objects with memory management
let scene, camera, renderer, raycaster, pointer;
let isPanning = false, lastPan = { x: 0, y: 0 };
let minZoom = 0.3, maxZoom = 5.0; // Increased max zoom to accommodate 1.6x default
let orbit = null;
let floorPlane = null, poiGroup = null, routeLine = null;
let mapExtents = { width: 1200, height: 800 };
let isModelActive = false;
let groundY = 0;

let currentFloor = null;
let currentLocation = null;
let currentDestination = null;
let allFloors = [];
let allPois = []; // POIs across all floors for global search
let pois = [];
let activePois = [];

function setActivePois(poiArray) {
  if (Array.isArray(poiArray)) {
    activePois = poiArray;
  } else if (poiArray && Array.isArray(poiArray.data)) {
    activePois = poiArray.data;
  } else {
    activePois = [];
  }
}

function getActivePois() {
  return Array.isArray(activePois) ? activePois : [];
}

// Memory management
const MEMORY_MANAGER = {
    disposedObjects: new Set(),
    
    disposeObject(obj) {
        if (!obj || this.disposedObjects.has(obj)) return;
        
        if (obj.geometry) {
            obj.geometry.dispose();
        }
        
        if (obj.material) {
            if (Array.isArray(obj.material)) {
                obj.material.forEach(mat => this.disposeMaterial(mat));
            } else {
                this.disposeMaterial(obj.material);
            }
        }
        
        if (obj.texture) {
            obj.texture.dispose();
        }
        
        this.disposedObjects.add(obj);
    },
    
    disposeMaterial(material) {
        if (!material) return;
        
        Object.keys(material).forEach(key => {
            const value = material[key];
            if (value && value.isTexture) {
                value.dispose();
            }
        });
        
        material.dispose();
    },
    
    clearScene() {
        if (!scene) return;
        
        // Dispose all meshes
        scene.traverse(obj => {
            if (obj.isMesh || obj.isSprite) {
                this.disposeObject(obj);
            }
        });
        
        // Clear groups
        if (poiGroup) {
            poiGroup.clear();
        }
        
        // Remove route line
        if (routeLine) {
            this.disposeObject(routeLine);
            if (scene) scene.remove(routeLine);
            routeLine = null;
        }
        
        // Remove floor plane
        if (floorPlane) {
            this.disposeObject(floorPlane);
            if (scene) scene.remove(floorPlane);
            floorPlane = null;
        }
    }
};

startWhenReady();

// Hardcoded POI data for immediate functionality
// NOTE: This function is intentionally a no-op by default so the kiosk
//       still works even if hardcoded POIs are not configured.
//       If you want immediate POIs without a backend/database, you can
//       add them here using the same structure as server POIs.
function populateHardcodedPOIs(){
  console.warn('[KIOSK] populateHardcodedPOIs() called, but no hardcoded POIs are configured. This is a no-op.');
}

function startWhenReady(){
  if (window && window.THREE) {
    init();
    loadFloors();
    // Also populate with hardcoded POIs immediately
    setTimeout(() => {
      populateHardcodedPOIs();
    }, 1000);
  } else {
    setTimeout(startWhenReady, 50);
  }
}

function init(){
  // toolbar buttons (optional / may not exist in current layout)
  const btnIn = document.getElementById('btn-zoom-in');
  const btnOut = document.getElementById('btn-zoom-out');
  const btnBack = document.getElementById('btn-back');
  if (btnIn) btnIn.addEventListener('click', ()=>console.log('zoom in'));
  if (btnOut) btnOut.addEventListener('click', ()=>console.log('zoom out'));
  if (btnBack) btnBack.addEventListener('click', ()=>console.log('back'));
  
  // floor selection
  const floorSel = document.getElementById('floor-select');
  if (floorSel) floorSel.addEventListener('change', onFloorChange);
  else console.warn('floor-select not found');
  
  // destination selection
  const destSel = document.getElementById('location-select');
  if (destSel) destSel.addEventListener('change', onLocationChange);
  else console.warn('location-select not found');
  
  // zoom controls
  const zoomInBtn = document.getElementById('zoom-in');
  const zoomOutBtn = document.getElementById('zoom-out');
  if (zoomInBtn) zoomInBtn.addEventListener('click', zoomIn);
  if (zoomOutBtn) zoomOutBtn.addEventListener('click', zoomOut);

  // Initialize minimal 3D scene if container is present
  const container = document.getElementById('canvas-container');
  if (container && window.THREE) {
    scene = new THREE.Scene();
    const w = container.clientWidth || 800;
    const h = container.clientHeight || 600;
    camera = new THREE.OrthographicCamera(w / -2, w / 2, h / 2, h / -2, 0.1, 10000);
    camera.position.set(0, 0, 1000);
    camera.up.set(0, 1, 0);
    camera.lookAt(0, 0, 0);

    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(w, h);
    renderer.setClearColor(0x000000, 0); // transparent to show page background
    if (renderer.shadowMap) {
      renderer.shadowMap.enabled = true;
      renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    }
    if ('outputColorSpace' in renderer) {
      renderer.outputColorSpace = THREE.SRGBColorSpace;
    } else if ('outputEncoding' in renderer) {
      renderer.outputEncoding = THREE.sRGBEncoding;
    }
    if ('toneMapping' in renderer && THREE.ACESFilmicToneMapping) {
      renderer.toneMapping = THREE.ACESFilmicToneMapping;
      renderer.toneMappingExposure = 1.0;
    }
    if (renderer.setPixelRatio) renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    container.innerHTML = '';
    container.appendChild(renderer.domElement);

    const ambient = new THREE.AmbientLight(0xffffff, 0.35);
    scene.add(ambient);
    const hemi = new THREE.HemisphereLight(0xffffff, 0x444444, 0.6);
    hemi.position.set(0, 200, 0);
    scene.add(hemi);
    const dir = new THREE.DirectionalLight(0xffffff, 0.9);
    dir.position.set(300, 400, 200);
    dir.castShadow = true;
    if (dir.shadow) {
      dir.shadow.mapSize.set(2048, 2048);
      dir.shadow.camera.near = 0.5;
      dir.shadow.camera.far = 5000;
      dir.shadow.camera.left = -1000;
      dir.shadow.camera.right = 1000;
      dir.shadow.camera.top = 1000;
      dir.shadow.camera.bottom = -1000;
    }
    scene.add(dir);

    raycaster = new THREE.Raycaster();
    pointer = new THREE.Vector2();

    poiGroup = new THREE.Group();
    scene.add(poiGroup);

    window.addEventListener('resize', onWindowResize);
    renderer.domElement.addEventListener('mousedown', onMouseDown);
    window.addEventListener('mousemove', onMouseMove);
    window.addEventListener('mouseup', onMouseUp);
    renderer.domElement.addEventListener('wheel', onWheel, { passive: false });

    // start render loop
    animate();

    // Preconfigure DRACO path if available (optional)
    if (window.THREE && THREE.DRACOLoader) {
      const tmpDraco = new THREE.DRACOLoader();
      tmpDraco.setDecoderPath('/kiosk/libs/draco/'); // place decoder files here if using Draco
      // Not attached directly; used later when constructing GLTFLoader
      window.__dracoPathConfigured = true;
    }
  } else {
    console.warn('3D container or THREE not available; map will be skipped.');
  }
}
// Dynamically load a script if not already present
function loadExternalScript(url){
  return new Promise((resolve, reject) => {
    const el = document.createElement('script');
    el.src = url;
    el.crossOrigin = 'anonymous';
    el.referrerPolicy = 'no-referrer';
    el.onload = () => resolve(url);
    el.onerror = (e) => reject(e);
    document.head.appendChild(el);
  });
}

async function loadAnyScript(urls){
  let lastErr;
  for (const url of urls) {
    try {
      await loadExternalScript(url);
      console.log('[KIOSK] Loaded script:', url);
      return true;
    } catch (e) {
      lastErr = e;
      console.warn('[KIOSK] Failed loading script:', url, e);
    }
  }
  throw lastErr || new Error('Failed to load scripts');
}


function onWindowResize(){
  if (!renderer || !camera) return;
  const container = document.getElementById('canvas-container');
  const w = container.clientWidth, h = container.clientHeight;
  camera.left = -w/2; camera.right = w/2; camera.top = h/2; camera.bottom = -h/2;
  camera.updateProjectionMatrix();
  renderer.setSize(w,h);
}

// Load all floors for dropdown with enhanced error handling
async function loadFloors(){
  try {
    showLoadingState('Loading floors...');
    console.log('Loading floors from:', `${API_BASE}/floors`);
    
    const res = await fetch(`${API_BASE}/floors`, {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    
    console.log('API Response status:', res.status);
    
    if (!res.ok) {
      throw new Error(`HTTP error! status: ${res.status}`);
    }
    
    const responseData = await res.json();
    
    if (!responseData.success) {
      throw new Error(responseData.message || 'Failed to load floors');
    }
    
    // Use all floors returned by API
    allFloors = Array.isArray(responseData.data) ? responseData.data : [];
    console.log('Loaded floors:', allFloors);

    // Build global POI list for cross-floor search
    allPois = [];
    allFloors.forEach(floor => {
      const floorLabel = `${floor.building} - ${floor.name}`;
      const floorPois = Array.isArray(floor.pois?.data) ? floor.pois.data : (Array.isArray(floor.pois) ? floor.pois : []);
      floorPois.forEach(poi => {
        allPois.push({
          ...poi,
          floorLabel,
        });
      });
    });
    console.log('[KIOSK] Global POI list built with', allPois.length, 'POIs');
    
    const floorSelect = document.getElementById('floor-select');
    if (!floorSelect) {
      console.warn('Floor select element not found, skipping floor dropdown population');
      return;
    }
    floorSelect.innerHTML = '<option value="">Select a floor</option>';
    
    const seen = new Set();
    allFloors.forEach(floor => {
      const display = `${floor.building} - ${floor.name}`;
      const key = display.trim().toLowerCase();
      if (seen.has(key)) return; // de-duplicate identical display names
      seen.add(key);
      const option = document.createElement('option');
      option.value = floor.id;
      option.textContent = display;
      floorSelect.appendChild(option);
    });
    
    // Auto-select first floor if available
    if (allFloors.length > 0) {
      floorSelect.value = allFloors[0].id;
      loadFloorAndPois(allFloors[0].id);
    }
    
    hideLoadingState();
  } catch (error) {
    console.error('Error loading floors:', error);
    showErrorState('Failed to load floors: ' + error.message);
    const floorSelect = document.getElementById('floor-select');
    if (floorSelect) {
    floorSelect.innerHTML = '<option value="">Error loading floors</option>';
    }
  }
}

async function loadFloorAndPois(floorId){
  try {
    console.log('[KIOSK] Loading floor:', floorId);
    
    // Clear wayfinding routes first
    clearWayfindingRoute();
    clearRoute();
    
    // Clear ALL existing floor models before loading new floor
    if (scene) {
      const modelsToRemove = [];
      
      // Find all floor models in the scene
      scene.children.forEach(child => {
        if (child.userData && child.userData.type === 'floorModel') {
          modelsToRemove.push(child);
        }
      });
      
      // Remove floor models
      modelsToRemove.forEach(model => {
        scene.remove(model);
        // Recursively dispose of all geometries and materials
        model.traverse(child => {
          if (child.geometry) child.geometry.dispose();
          if (child.material) {
            if (Array.isArray(child.material)) {
              child.material.forEach(mat => mat.dispose());
            } else {
              child.material.dispose();
            }
          }
        });
      });
      
      // Dispose old OrbitControls if they exist and reset camera
      if (orbit) {
        orbit.dispose();
        orbit = null;
        console.log('[KIOSK] Disposed old OrbitControls');
      }
      
      // Reset camera to default position and rotation before loading new floor
      if (camera) {
        camera.position.set(0, 0, 1000);
        camera.up.set(0, 1, 0);
        camera.lookAt(0, 0, 0);
        if (camera.zoom !== undefined) {
          camera.zoom = 1.0;
          camera.updateProjectionMatrix();
        }
        console.log('[KIOSK] Reset camera to default position');
      }
      
      // Clear POI group and recreate it
      if (poiGroup) {
        // Dispose all POI objects before clearing
        poiGroup.traverse(child => {
          if (child.geometry) child.geometry.dispose();
          if (child.material) {
            if (Array.isArray(child.material)) {
              child.material.forEach(mat => mat.dispose());
            } else {
              child.material.dispose();
            }
          }
        });
        scene.remove(poiGroup);
      }
      
      // Create new POI group
      poiGroup = new THREE.Group();
      scene.add(poiGroup);
      pois = [];
      
      console.log('[KIOSK] Cleared', modelsToRemove.length, 'existing floor models and recreated POI group');
    }
    
    // fetch floor details
    const res = await fetch(`${API_BASE}/floor/${floorId}`);
    if (!res.ok) {
      throw new Error(`HTTP error while loading floor ${floorId}: ${res.status}`);
    }
    const floorResponse = await res.json();
    if (!floorResponse.success || !floorResponse.data) {
      throw new Error(floorResponse.message || 'Invalid floor response');
    }
    const floor = floorResponse.data;
    let floorPois = Array.isArray(floor.pois?.data) ? floor.pois.data : (Array.isArray(floor.pois) ? floor.pois : []);
    // Attach floor metadata for search/labels/routing
    const floorLabel = `${floor.building} - ${floor.name}`;
    floorPois = floorPois.map(poi => ({
      ...poi,
      floorLabel,
    }));
    currentFloor = floor;
    currentFloor.pois = floorPois;
    setActivePois(floorPois);

    // If a GLB is available, load 3D map; otherwise fallback to image plane
    let useModel = false;
    let modelRoot = null;
    if ((scene && window.THREE && THREE.GLTFLoader) || FORCE_GLB) {
      // Convention: if floor.glb_path exists, use it; else try default path
      const glbPath = (window && window.KIOSK_GLB_PATH) ? window.KIOSK_GLB_PATH : (floor.glb_path || '/kiosk/assets/map.glb');
      console.log('[KIOSK] Attempting to load GLB:', glbPath);
      try {
        const GLTFLoaderCtor = (typeof THREE !== 'undefined' && typeof THREE.GLTFLoader === 'function')
          ? THREE.GLTFLoader
          : (typeof window !== 'undefined' && typeof window.GLTFLoader === 'function' ? window.GLTFLoader : null);
        if (!GLTFLoaderCtor) {
          console.warn('[KIOSK] GLTFLoader not found even though scripts should be included.');
          throw new Error('GLTFLoader not available');
        }
        const gltfLoader = new GLTFLoaderCtor();

        const DRACOLoaderCtor = (typeof THREE !== 'undefined' && typeof THREE.DRACOLoader === 'function')
          ? THREE.DRACOLoader
          : (typeof window !== 'undefined' && typeof window.DRACOLoader === 'function' ? window.DRACOLoader : null);
        if (DRACOLoaderCtor) {
          const draco = new DRACOLoaderCtor();
          draco.setDecoderPath(DRACO_DECODER_PATH);
          gltfLoader.setDRACOLoader(draco);
          console.log('[KIOSK] DRACO enabled. Decoder path:', DRACO_DECODER_PATH);
        }
        // Add timeout to prevent hanging on failed loads
        const loadTimeout = setTimeout(() => {
          reject(new Error('GLB load timeout after 30 seconds'));
        }, 30000);
        
        modelRoot = await new Promise((resolve, reject) => {
          gltfLoader.load(
            glbPath, 
            (gltf) => {
              clearTimeout(loadTimeout);
              console.log('[KIOSK] GLB loaded successfully:', glbPath);
              resolve(gltf.scene);
            }, 
            undefined, 
            (error) => {
              clearTimeout(loadTimeout);
              console.error('[KIOSK] GLB load error:', error);
              reject(error);
            }
          );
        });
        // Ignore external GLB_SCALE to avoid unintended enlargement
        useModel = true;
      } catch (e) {
        console.warn('[KIOSK] GLB load failed. Check path, network, or Draco decoders.', e);
        console.warn('[KIOSK] Error details:', e.message, e.stack);
        // Reset state on failure
        isModelActive = false;
        useModel = false;
        modelRoot = null;
      }
    }

    let imgW = floor.width || TARGET_MODEL_EXTENT || 1200;
    let imgH = floor.height || TARGET_MODEL_EXTENT || 800;
    if (useModel && modelRoot) {
      // Clear previous floor plane if it exists
      if (scene && floorPlane) { 
        scene.remove(floorPlane); 
        if (floorPlane.geometry) floorPlane.geometry.dispose();
        if (floorPlane.material) {
          if (Array.isArray(floorPlane.material)) {
            floorPlane.material.forEach(mat => mat.dispose());
          } else {
            floorPlane.material.dispose();
          }
        }
        floorPlane = null; 
      }

      // Store reference to this model for future clearing
      modelRoot.userData = { type: 'floorModel', floorId: floorId };
      scene.add(modelRoot);
      if (modelRoot && modelRoot.traverse) {
        modelRoot.traverse(obj => {
          if (obj && obj.isMesh) {
            obj.castShadow = true;
            obj.receiveShadow = true;
            if (obj.material && 'toneMapped' in obj.material) obj.material.toneMapped = true;
          }
        });
      }
      // Compute bounds
      let bbox = new THREE.Box3().setFromObject(modelRoot);
      let size = bbox.getSize(new THREE.Vector3());
      let center = bbox.getCenter(new THREE.Vector3());
      console.log('[KIOSK] GLB bounds size:', size, 'center:', center);
      // Scale model so its largest dimension fits TARGET_MODEL_EXTENT (e.g., 800)
      const maxDim = Math.max(size.x || 0, size.y || 0, size.z || 0);
      if (maxDim && isFinite(maxDim) && maxDim > 0 && TARGET_MODEL_EXTENT) {
        const factor = TARGET_MODEL_EXTENT / maxDim;
        if (isFinite(factor) && factor > 0 && factor !== 1) {
          modelRoot.scale.multiplyScalar(factor);
          // recompute bounds after scaling
          bbox = new THREE.Box3().setFromObject(modelRoot);
          size = bbox.getSize(new THREE.Vector3());
          center = bbox.getCenter(new THREE.Vector3());
          console.log('[KIOSK] Applied uniform fit scale', factor, 'new size:', size);
        }
      }
      // Center model at origin for easier camera/POI alignment
      modelRoot.position.sub(center);
      // Update ground level after centering
      bbox = new THREE.Box3().setFromObject(modelRoot);
      groundY = bbox.min.y + 0.5;
      // Fit camera to model
      fitCameraToObject(camera, modelRoot, { offset: 1.2 });
      
      // Ensure camera is properly positioned for orthographic view
      // For orthographic cameras, Z position should be fixed
      camera.position.z = 1000;
      camera.lookAt(0, 0, 0);
      camera.up.set(0, 1, 0);
      
      // Apply default 2x zoom to make the model appear larger
      if (camera.zoom !== undefined) {
        camera.zoom = 2.0;
        camera.updateProjectionMatrix();
        console.log('[KIOSK] Applied default 2x zoom');
      }
      
      // Enable orbit controls for interactive rotation/zoom around model
      if (window.OrbitControls) {
        // Dispose old controls if they exist (should already be disposed, but double-check)
        if (orbit) {
          orbit.dispose();
        }
        orbit = new window.OrbitControls(camera, renderer.domElement);
        orbit.enableRotate = true;
        orbit.enablePan = true;
        orbit.enableZoom = true;
        orbit.target.set(0, 0, 0);
        // Reset controls to ensure they start from default position
        orbit.update();
        console.log('[KIOSK] OrbitControls created with camera reset');
      }
      // Use model bounds as extents if floor sizes not set
      imgW = floor.width || size.x || imgW;
      imgH = floor.height || size.y || imgH;
      mapExtents.width = imgW;
      mapExtents.height = imgH;
      isModelActive = true;
      console.log('GLB model loaded successfully, isModelActive set to true');
      
      // Initialize wayfinding system after model is loaded
      setTimeout(() => {
        initializeWayfindingSystem();
      }, 500);
      
      // Optional: add bbox helper
      if (window && window.KIOSK_SHOW_BBOX) {
        const helper = new THREE.Box3Helper(new THREE.Box3().setFromObject(modelRoot), 0x00ff00);
        scene.add(helper);
      }
    } else {
      console.warn('[KIOSK] No GLB model loaded for this floor. POIs will still be placed using default extents.');
      isModelActive = false;
      groundY = 0; // Reset groundY when no model is active
      mapExtents.width = imgW;
      mapExtents.height = imgH;
      
      // Reset camera if no model and dispose OrbitControls
      if (orbit) {
        orbit.dispose();
        orbit = null;
        console.log('[KIOSK] Disposed OrbitControls for image plane fallback');
      }
      if (camera) {
        camera.position.set(0, 0, 1000);
        camera.up.set(0, 1, 0);
        camera.lookAt(0, 0, 0);
        if (camera.zoom !== undefined) {
          camera.zoom = 1.0;
          camera.updateProjectionMatrix();
        }
        console.log('[KIOSK] Reset camera for image plane fallback');
      }
    }

    // Load and display POIs
    if (floorPois && floorPois.length > 0) {
      console.log('Loading', floorPois.length, 'POIs for floor:', floor.name);
      console.log('isModelActive:', isModelActive, 'groundY:', groundY);
      console.log('poiGroup exists:', !!poiGroup);
      console.log('scene exists:', !!scene);
      
      floorPois.forEach(poi => {
        console.log('Processing POI:', poi.name, 'category:', poi.category, 'at (', poi.x, ',', poi.y, ')');
        addPoiMarker(poi, imgW, imgH);
      });
      
      // Populate destination dropdown
      populateDestinationSelect(floorPois);
      
      // Set entrance as current location
      setEntranceAsCurrentLocation(floorPois);
      
      // Populate POI list for search
      populatePoiList(floorPois);
      addPoiNameLabels(floorPois);
      
      console.log('Total POIs in scene after loading:', pois.length);
      console.log('poiGroup children count:', poiGroup ? poiGroup.children.length : 'poiGroup is null');
      
      // Test: Create a simple wayfinding tube at a fixed position to debug
      // createTestWayfindingTube(); // Disabled - was creating duplicate tubes
      
      // Test: Create route to Civil Registry Office
      // setTimeout(() => {
      //   testCivilRegistryRoute();
      // }, 2000); // Wait 2 seconds to ensure everything is loaded - Disabled to prevent duplicate routes
      
      // Force render to ensure POIs are visible
      if (renderer && scene && camera) {
        renderer.render(scene, camera);
        console.log('Forced render after POI loading');
      }
    } else {
      console.warn('No POIs found for floor:', floor.name);
    }
    
    // You Are Here marker is now created by the wayfinding system

  } catch (error) {
    console.error('❌ Error loading City Hall:', error);
    setActivePois([]);
  }
}

// Initialize the wayfinding system
function initializeWayfindingSystem() {
  console.log('🎯 Initializing City Hall wayfinding system...');
  
  // Create "You Are Here" marker
  createYouAreHereMarker();
  
  // Add flat name labels for all POIs (with delay to ensure scene is ready)
  setTimeout(() => {
    console.log('🔍 About to call addPoiNameLabels...');
    console.log('🔍 poiGroup exists:', !!poiGroup);
    console.log('🔍 scene exists:', !!scene);
    console.log('🔍 active POIs length:', getActivePois().length);
    addPoiNameLabels(getActivePois());
  }, 1000);
  
  console.log('✅ Wayfinding system initialized successfully');
  
  // Initialize search functionality
  initializeSearch();
}

// Create "You Are Here" marker at the entrance
function createYouAreHereMarker() {
  if (!poiGroup || !scene) {
    console.warn('Cannot create You Are Here marker - scene not ready');
    return;
  }

  // If current floor explicitly disables the marker, skip
  if (currentFloor && currentFloor.show_you_are_here === false) {
    console.log('ℹ️ Skipping "You Are Here" marker for this floor (show_you_are_here = false).');
    return;
  }
  
  console.log('Creating You Are Here marker at entrance...');
  
  let pos;
  if (currentFloor && currentFloor.you_are_here_model_x !== null && currentFloor.you_are_here_model_x !== undefined) {
    pos = {
      x: currentFloor.you_are_here_model_x,
      y: (currentFloor.you_are_here_model_y !== null && currentFloor.you_are_here_model_y !== undefined)
        ? currentFloor.you_are_here_model_y
        : groundY,
      z: currentFloor.you_are_here_model_z ?? 0,
    };
    console.log(' Using per-floor "You Are Here" position:', pos);
  } else {
    pos = { x: -62, y: 18, z: 275 }; // legacy default
    console.log(' Using default "You Are Here" position:', pos);
  }
  
  // Create a green sphere marker
  const geometry = new THREE.SphereGeometry(15, 16, 16);
  const material = new THREE.MeshBasicMaterial({
    color: '#00ff00', // Green for "You Are Here"
    transparent: false,
    opacity: 0.9
  });
  
  const marker = new THREE.Mesh(geometry, material);
  marker.position.set(pos.x, pos.y  , pos.z);
  marker.userData = { 
    youAreHere: true,
    type: 'youAreHere'
  };
  
  poiGroup.add(marker);
  
  // Create text label
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  canvas.width = 256;
  canvas.height = 64;
  //canvas.width = 256;
  //canvas.height = 64;
  
  // Draw background
  context.fillStyle = 'rgba(0, 255, 0, 0.9)';
  context.fillRect(0, 0, canvas.width, canvas.height);
  
  // Draw text
  context.fillStyle = '#000000';
  context.font = 'bold 18px Arial';
  context.textAlign = 'center';
  context.fillText('YOU ARE HERE', canvas.width / 2, 35);
  
  const texture = new THREE.CanvasTexture(canvas);
  const labelMaterial = new THREE.SpriteMaterial({ 
    map: texture, 
    transparent: true,
    alphaTest: 0.1
  });
  
  const label = new THREE.Sprite(labelMaterial);
  label.scale.set(80, 20, 1); // Larger label to match improved readability
  label.position.set(0, 30, 0); // Higher position for larger marker
  
  marker.add(label);
  
  console.log('✅ You Are Here marker created at position:', marker.position);
}

// Add flat name labels for all POIs on the 3D model surface
function addPoiNameLabels(poiArray = getActivePois()) {
  console.log('🔍 addPoiNameLabels function called');
  
  if (!poiGroup || !scene) {
    console.warn('❌ Cannot create POI labels - scene not ready');
    console.warn('❌ poiGroup:', poiGroup);
    console.warn('❌ scene:', scene);
    return;
  }
  
  console.log('🏷️ Adding flat name labels for all POIs...');
  console.log('🔍 Processing', poiArray.length, 'POIs');
  
  let labelsCreated = 0;
  
  poiArray.forEach((poi, index) => {
    console.log(`🔍 Processing POI ${index + 1}:`, poi.name, 'category:', poi.category);
    
    // Skip entrance POIs as they don't need labels
    if (poi.category === 'Entrance') {
      console.log('⏭️ Skipping entrance POI:', poi.name);
      return;
    }
    
    // Prefer explicit 3D modelPosition if available
    let position = poi.meta?.modelPosition;
    
    // Fallback: derive a reasonable 3D position from 2D x/y and current extents
    if (!position) {
      if (typeof poi.x === 'number' && typeof poi.y === 'number' &&
          mapExtents && typeof mapExtents.width === 'number' && typeof mapExtents.height === 'number') {
        const imgPos = imageToScene(poi.x, poi.y, mapExtents.width, mapExtents.height);
        position = isModelActive
          ? { x: imgPos.x, y: groundY, z: imgPos.y }
          : { x: imgPos.x, y: imgPos.y, z: 0 };
        console.log('ℹ️ Derived label position from 2D coords for POI:', poi.name, position);
      } else {
        console.warn('❌ No model or 2D position found for POI:', poi.name);
      return;
      }
    }
    
    // Special handling for restroom - use final waypoint position
    let labelPosition = position;
    if (poi.name === 'Restroom' && poi.meta?.waypoints && poi.meta.waypoints.length > 0) {
      labelPosition = poi.meta.waypoints[poi.meta.waypoints.length - 1]; // Use final waypoint
      console.log('🚽 Using final waypoint for restroom label:', labelPosition);
    }
    
    console.log('✅ Creating label for:', poi.name, 'at position:', labelPosition);
    createFlatNameLabel(poi.name, labelPosition);
    labelsCreated++;
  });
  
  console.log('✅ POI name labels added successfully. Created', labelsCreated, 'labels');
}

// Create a flat name label for a POI
function createFlatNameLabel(name, position) {
  console.log('🔍 createFlatNameLabel called for:', name);
  
  if (!poiGroup) {
    console.warn('❌ poiGroup is null, cannot create label');
    return;
  }
  
  console.log('🏷️ Creating label for:', name, 'at position:', position);
  
  // Create larger canvas for better text quality
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  canvas.width = 1024; // Doubled for better quality
  canvas.height = 256; // Doubled for better quality
  
  // Draw background with higher opacity for better visibility
  context.fillStyle = 'rgba(255, 255, 255, 0.98)';
  context.fillRect(0, 0, canvas.width, canvas.height);
  
  // Draw border
  context.strokeStyle = '#000000';
  context.lineWidth = 6; // Thicker border for larger canvas
  context.strokeRect(4, 4, canvas.width - 8, canvas.height - 8);
  
  // Draw text with larger font
  context.fillStyle = '#000000';
  context.font = 'bold 68px Arial'; // Much larger, bolder font
  context.textAlign = 'center';
  context.textBaseline = 'middle';
  
  // Split long names into multiple lines if needed
  const words = name.split(' ');
  const maxWordsPerLine = 3;
  const lines = [];
  
  for (let i = 0; i < words.length; i += maxWordsPerLine) {
    lines.push(words.slice(i, i + maxWordsPerLine).join(' '));
  }
  
  // Draw each line with larger spacing
  const lineHeight = 90; // Increased for much larger font
  const startY = canvas.height / 2 - (lines.length - 1) * lineHeight / 2;
  
  lines.forEach((line, index) => {
    context.fillText(line, canvas.width / 2, startY + index * lineHeight);
  });
  
  // Create texture from canvas
  const texture = new THREE.CanvasTexture(canvas);
  const material = new THREE.SpriteMaterial({
    map: texture,
    transparent: true,
    alphaTest: 0.1
  });
  
  // Create sprite for better visibility (always faces camera)
  const label = new THREE.Sprite(material);
  label.scale.set(80, 20, 1); // Make labels much larger for better readability
  
  // Position the label high above everything for maximum visibility
  label.position.set(position.x, 50, position.z);
  
  // Add user data
  label.userData = {
    type: 'poiLabel',
    poiName: name,
    originalPosition: position
  };
  
  // Add to scene
  poiGroup.add(label);
  
  console.log('🏷️ Created sprite label for:', name, 'at position:', label.position);
  console.log('🏷️ Label scale:', label.scale);
}

// Initialize search functionality
function initializeSearch() {
  const searchInput = document.getElementById('search');
  const poiList = document.getElementById('poi-list');
  
  if (!searchInput || !poiList) {
    console.warn('Search elements not found');
    return;
  }
  
  // Initially hide the POI list
  poiList.style.display = 'none';
  
  // Add event listeners
  searchInput.addEventListener('input', handleSearch);
  searchInput.addEventListener('focus', showPoiList);
  searchInput.addEventListener('blur', () => {
    // Delay hiding to allow clicking on POI items
    setTimeout(() => {
      if (!poiList.matches(':hover') && !searchInput.matches(':focus')) {
        poiList.style.display = 'none';
      }
    }, 200);
  });
  
  console.log('✅ Search functionality initialized');
}

// Handle search input
function handleSearch(event) {
  const query = event.target.value.toLowerCase().trim();
  const poiList = document.getElementById('poi-list');
  
  if (!query) {
    poiList.style.display = 'none';
    return;
  }
  
  const availablePois = (Array.isArray(allPois) && allPois.length > 0) ? allPois : getActivePois();
  // Filter POIs based on search query
  const filteredPOIs = availablePois.filter(poi => 
    (poi.name && poi.name.toLowerCase().includes(query)) || 
    (poi.category && poi.category.toLowerCase().includes(query))
  );
  
  // Display search results
  displaySearchResults(filteredPOIs);
  poiList.style.display = 'block';
}

// Show POI list when search input is focused
function showPoiList() {
  const poiList = document.getElementById('poi-list');
  const searchInput = document.getElementById('search');
  
  if (searchInput.value.trim()) {
    // If there's already a search query, show filtered results
    handleSearch({ target: searchInput });
  } else {
    // Show all POIs (global list if available)
    const base = (Array.isArray(allPois) && allPois.length > 0) ? allPois : getActivePois();
    displaySearchResults(base);
    poiList.style.display = 'block';
  }
}

// Display search results in POI list
function displaySearchResults(pois = getActivePois()) {
  const poiList = document.getElementById('poi-list');
  
  if (!poiList) return;
  
  // Clear existing results
  poiList.innerHTML = '';
  
  if (pois.length === 0) {
    poiList.innerHTML = '<div class="poi-item" style="color: rgba(255,255,255,0.7); font-style: italic;">No POIs found</div>';
    return;
  }
  
  // Add each POI as a clickable item
  pois.forEach(poi => {
    const poiItem = document.createElement('div');
    poiItem.className = 'poi-item';

    const floorLabel = poi.floorLabel || (poi.floor && poi.floor.name) || (currentFloor && currentFloor.name) || '';
    const secondary = [
      poi.category || null,
      floorLabel ? floorLabel : null,
    ].filter(Boolean).join(' • ');

    poiItem.innerHTML = `
      <div style="font-weight: bold;">${poi.name}</div>
      <div style="font-size: 12px; color: rgba(255,255,255,0.7);">${secondary}</div>
    `;
    
    // Add click handler to select this POI
    poiItem.addEventListener('click', () => {
      selectPOIFromSearch(poi);
    });
    
    poiList.appendChild(poiItem);
  });
  
  console.log(`🔍 Displayed ${pois.length} search results`);
}

// Handle POI selection from search
async function selectPOIFromSearch(poi) {
  console.log('🎯 POI selected from search:', poi.name);
  
  try {
    const targetFloorId = poi.floor_id || (poi.floor && poi.floor.id);
    const isCrossFloor = targetFloorId && (!currentFloor || currentFloor.id !== targetFloorId);
    
    if (isCrossFloor) {
      console.log('🔄 Destination is on a different floor. Starting cross-floor wayfinding to floor', targetFloorId);
      // Use cross-floor wayfinding which routes to stairs first
      await handleCrossFloorWayfinding(poi, targetFloorId);
    } else {
      // Same floor - route directly
      const locationSelect = document.getElementById('location-select');
      let destination = null;

      if (currentFloor && Array.isArray(currentFloor.pois)) {
        destination = currentFloor.pois.find(p => p.id == poi.id);
      }

      if (destination) {
        // Drive route directly
        handleDestinationSelection(destination);
        showDestinationInfo(destination);

        // Keep dropdown in sync visually
        if (locationSelect) {
          locationSelect.value = destination.id;
        }
      } else if (locationSelect) {
        // Fallback: try legacy dropdown change approach
        locationSelect.value = poi.id;
        const changeEvent = new Event('change', { bubbles: true });
        locationSelect.dispatchEvent(changeEvent);
      }
    }
  } catch (err) {
    console.error('❌ Error handling POI selection from search:', err);
  }
  
  // Clear search and hide POI list
  const searchInput = document.getElementById('search');
  const poiList = document.getElementById('poi-list');
  
  if (searchInput) searchInput.value = '';
  if (poiList) poiList.style.display = 'none';
}

// OLD createWayfindingRoute function removed - using new smart wayfinding system instead
// This prevents duplicate tubes from being generated

// convert image coords (0..imgW,0..imgH) to scene coords centered at 0
function imageToScene(x,y,imgW,imgH){
  const sx = x - imgW/2;
  const sy = (imgH/2) - y; // invert Y so image top is positive Y
  return { x: sx, y: sy };
}

function addPoiMarker(p, imgW, imgH){
  if (!poiGroup) {
    console.warn('POI Group not initialized, cannot add POI:', p.name);
    return;
  }
  
  console.log('Adding POI marker:', p.name, 'at (', p.x, ',', p.y, ')');
  
  // Check if this POI has special geometry requirements
  if (p.meta && p.meta.geometry_type === 'tube') {
    console.log('Creating wayfinding tube for:', p.name);
    return addTubeGeometry(p, imgW, imgH);
  }
  
  const tex = new THREE.TextureLoader().load('/kiosk/assets/marker.svg');
  let marker;
  if (isModelActive) {
    // Flat marker on the ground plane (Y-up), rotated to face upward
    const geom = new THREE.PlaneGeometry(40, 40);
    const mat = new THREE.MeshBasicMaterial({ map: tex, transparent: true, side: THREE.DoubleSide, depthWrite: false });
    marker = new THREE.Mesh(geom, mat);
    marker.rotation.x = -Math.PI / 2; // lay flat on ground
    if (p && p.meta && p.meta.modelPosition) {
      const mp = p.meta.modelPosition;
      const mx = typeof mp.x === 'number' ? mp.x : 0;
      const mz = typeof mp.z === 'number' ? mp.z : 0;
      marker.position.set(mx, groundY, mz);
    } else {
      const pos = imageToScene(p.x, p.y, imgW, imgH);
      marker.position.set(pos.x, groundY, pos.y);
    }
  } else {
    const material = new THREE.SpriteMaterial({ map: tex, sizeAttenuation: false });
    marker = new THREE.Sprite(material);
    const pos = imageToScene(p.x, p.y, imgW, imgH);
    marker.position.set(pos.x, pos.y, 5);
    marker.scale.set(40, 40, 1);
  }
  marker.userData = { poi: p };
  poiGroup.add(marker);
  pois.push(marker);
}

// Create tube geometry for wayfinding POIs
function addTubeGeometry(p, imgW, imgH) {
  if (!poiGroup || !p.meta || !p.meta.tube_properties) {
    console.warn('Cannot create tube geometry - missing requirements:', {
      poiGroup: !!poiGroup,
      meta: !!p.meta,
      tubeProperties: !!(p.meta && p.meta.tube_properties)
    });
    return;
  }
  
  console.log('Creating tube geometry for POI:', p.name, 'with properties:', p.meta.tube_properties);
  
  const props = p.meta.tube_properties;
  let pos;
  
  // Handle positioning for GLB models vs image planes
  console.log('isModelActive:', isModelActive, 'groundY:', groundY);
  
  if (isModelActive) {
    // For GLB models, use modelPosition if available, otherwise convert from image coordinates
    if (p.meta.modelPosition) {
      pos = {
        x: p.meta.modelPosition.x || 0,
        y: p.meta.modelPosition.y || 0,
        z: p.meta.modelPosition.z || 0
      };
      console.log('Using modelPosition:', pos);
    } else {
      // Convert image coordinates to 3D model coordinates
      const imagePos = imageToScene(p.x, p.y, imgW, imgH);
      pos = {
        x: imagePos.x,
        y: groundY,
        z: imagePos.y
      };
      console.log('Converted image coords to model coords:', pos);
    }
  } else {
    // For image planes, use standard image coordinate conversion
    const imagePos = imageToScene(p.x, p.y, imgW, imgH);
    pos = {
      x: imagePos.x,
      y: imagePos.y,
      z: 0
    };
    console.log('Using image plane coords:', pos);
  }
  
  // Create tube geometry with enhanced properties for GLB integration
  const geometry = new THREE.CylinderGeometry(
    props.radius || 12, // Reduced default radius from 20 to 12
    props.radius || 12,
    props.height || 120, // Reduced default height from 200 to 120
    props.segments || 16
  );
  
  // Create material with emissive properties for wayfinding
  const material = new THREE.MeshBasicMaterial({
    color: props.color || '#00ff00',
    transparent: true,
    opacity: props.opacity || 0.8,
    emissive: new THREE.Color(props.color || '#00ff00'),
    emissiveIntensity: 0.3,
    side: THREE.DoubleSide // Ensure visibility from all angles
  });
  
  const tube = new THREE.Mesh(geometry, material);
  
  // Position the tube based on model type
  if (isModelActive) {
    // For GLB models, position relative to ground level
    const finalY = groundY + (props.height || 120) / 2;
    tube.position.set(pos.x, finalY, pos.z);
    console.log('Positioned tube for GLB model at:', tube.position);
  } else {
    // For image planes, position in 2D space
    tube.position.set(pos.x, pos.y, (props.height || 120) / 2);
    console.log('Positioned tube for image plane at:', tube.position);
  }
  
  // Add animation if specified
  if (props.animated) {
    tube.userData.animation = {
      pulseSpeed: props.pulse_speed || 2.0,
      originalScale: tube.scale.clone(),
      time: 0
    };
  }
  
  // Add interaction capabilities
  tube.userData = { 
    poi: p, 
    interactive: p.meta.interactive || false,
    wayfinding: true,
    geometryType: 'tube',
    modelPosition: pos
  };
  
  // Add directional arrows if specified
  if (p.meta.wayfinding_features && p.meta.wayfinding_features.includes('direction_arrows')) {
    addDirectionArrows(tube, props);
  }
  
  // Add wayfinding label if specified
  if (p.meta.wayfinding_features && p.meta.wayfinding_features.includes('distance_display')) {
    addWayfindingLabel(tube, p);
  }
  
  // Add special kiosk location indicator if this is the entrance
  if (p.meta && (p.meta.entrance || p.meta.user_start_location || p.meta.kioskLocation)) {
    addKioskLocationIndicator(tube, p);
  }
  
  poiGroup.add(tube);
  pois.push(tube);
  
  console.log(`✅ Created wayfinding tube for ${p.name} at 3D position (${pos.x}, ${pos.y}, ${pos.z})`);
  console.log('Tube added to poiGroup, total POIs in scene:', pois.length);
  console.log('Tube position:', tube.position);
  console.log('Tube visible:', tube.visible);
}

// Add directional arrows to tube geometry
function addDirectionArrows(tube, props) {
  const arrowGeometry = new THREE.ConeGeometry(5, 15, 8);
  const arrowMaterial = new THREE.MeshBasicMaterial({
    color: props.color || '#00ff00',
    emissive: new THREE.Color(props.color || '#00ff00'),
    emissiveIntensity: 0.5
  });
  
  // Create arrows pointing in cardinal directions
  const directions = [
    { name: 'north', rotation: [0, 0, 0], position: [0, 0, props.radius + 10] },
    { name: 'south', rotation: [0, Math.PI, 0], position: [0, 0, -props.radius - 10] },
    { name: 'east', rotation: [0, -Math.PI/2, 0], position: [props.radius + 10, 0, 0] },
    { name: 'west', rotation: [0, Math.PI/2, 0], position: [-props.radius - 10, 0, 0] }
  ];
  
  directions.forEach(dir => {
    const arrow = new THREE.Mesh(arrowGeometry, arrowMaterial);
    arrow.rotation.set(...dir.rotation);
    arrow.position.set(...dir.position);
    arrow.userData = { direction: dir.name, wayfinding: true };
    tube.add(arrow);
  });
}

// Add wayfinding label above the tube
function addWayfindingLabel(tube, poi) {
  if (!scene) return;
  
  // Create text sprite for wayfinding information
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  canvas.width = 256;
  canvas.height = 64;
  
  // Draw background
  context.fillStyle = 'rgba(0, 0, 0, 0.8)';
  context.fillRect(0, 0, canvas.width, canvas.height);
  
  // Draw text
  context.fillStyle = '#00ff00';
  context.font = '16px Arial';
  context.textAlign = 'center';
  context.fillText(poi.name, canvas.width / 2, 25);
  context.fillText('Wayfinding Point', canvas.width / 2, 45);
  
  // Create texture from canvas
  const texture = new THREE.CanvasTexture(canvas);
  const material = new THREE.SpriteMaterial({ 
    map: texture, 
    transparent: true,
    alphaTest: 0.1
  });
  
  const sprite = new THREE.Sprite(material);
  sprite.scale.set(50, 12.5, 1);
  sprite.position.set(0, tube.geometry.parameters.height + 30, 0); // Moved higher for taller tubes
  
  sprite.userData = { 
    wayfinding: true, 
    label: true,
    poi: poi
  };
  
  tube.add(sprite);
}

// Add kiosk location indicator for entrance wayfinding tube
function addKioskLocationIndicator(tube, poi) {
  if (!scene) return;
  
  // Create a special "YOU ARE HERE" indicator
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  canvas.width = 256;
  canvas.height = 128;
  
  // Draw background with special kiosk styling
  context.fillStyle = 'rgba(0, 255, 0, 0.9)';
  context.fillRect(0, 0, canvas.width, canvas.height);
  
  // Draw border
  context.strokeStyle = '#ffffff';
  context.lineWidth = 3;
  context.strokeRect(2, 2, canvas.width - 4, canvas.height - 4);
  
  // Draw text
  context.fillStyle = '#000000';
  context.font = 'bold 18px Arial';
  context.textAlign = 'center';
  context.fillText('KIOSK LOCATION', canvas.width / 2, 35);
  context.fillText('YOU ARE HERE', canvas.width / 2, 60);
  context.fillText(poi.name, canvas.width / 2, 85);
  context.fillText('', canvas.width / 2, 110);
  
  // Create texture from canvas
  const texture = new THREE.CanvasTexture(canvas);
  const material = new THREE.SpriteMaterial({ 
    map: texture, 
    transparent: true,
    alphaTest: 0.1
  });
  
  const sprite = new THREE.Sprite(material);
  sprite.scale.set(60, 30, 1);
  sprite.position.set(0, tube.geometry.parameters.height + 50, 0); // Moved higher for taller tubes
  
  sprite.userData = { 
    kioskLocation: true, 
    label: true,
    poi: poi
  };
  
  tube.add(sprite);
  
  // Add a pulsing ring around the tube base
  const ringGeometry = new THREE.RingGeometry(25, 35, 32);
  const ringMaterial = new THREE.MeshBasicMaterial({
    color: '#00ff00',
    transparent: true,
    opacity: 0.6,
    side: THREE.DoubleSide
  });
  
  const ring = new THREE.Mesh(ringGeometry, ringMaterial);
  ring.rotation.x = -Math.PI / 2; // Lay flat on ground
  ring.position.y = 0.1; // Slightly above ground
  ring.userData = { kioskRing: true, pulseSpeed: 3.0, time: 0 };
  
  tube.add(ring);
}

// Create a simple "You Are Here" marker as fallback
function createSimpleYouAreHereMarker() {
  if (!poiGroup || !scene) {
    console.warn('Cannot create You Are Here marker - scene not ready');
    return;
  }
  
  console.log('Creating simple You Are Here marker...');
  
  // Create a simple sphere marker
  const geometry = new THREE.SphereGeometry(15, 16, 16);
  const material = new THREE.MeshBasicMaterial({
    color: '#00ff00', // Green for "You Are Here"
    transparent: true,
    opacity: 0.9,
    emissive: new THREE.Color('#00ff00'),
    emissiveIntensity: 0.3
  });
  
  const marker = new THREE.Mesh(geometry, material);
  marker.position.set(-62, 10, 275); // Position above ground -62, 5, 275
  marker.userData = { 
    youAreHere: true,
    poi: { name: 'You Are Here' }
  };
  
  poiGroup.add(marker);
  pois.push(marker);
  
  // Create text label
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  canvas.width = 256;
  canvas.height = 64;
  
  context.fillStyle = 'rgba(0, 255, 0, 0.9)';
  context.fillRect(0, 0, canvas.width, canvas.height);
  
  context.fillStyle = '#000000';
  context.font = 'bold 20px Arial';
  context.textAlign = 'center';
  context.fillText('YOU ARE HERE', canvas.width / 2, 35);
  
  const texture = new THREE.CanvasTexture(canvas);
  const labelMaterial = new THREE.SpriteMaterial({ 
    map: texture, 
    transparent: true,
    alphaTest: 0.1
  });
  
  const label = new THREE.Sprite(labelMaterial);
  label.scale.set(80, 20, 1); // Larger label to match improved readability
  label.position.set(0, 50, 0); // Higher position for larger marker
  
  marker.add(label);
  
  console.log('✅ Simple You Are Here marker created at position:', marker.position);
  console.log('Marker visible:', marker.visible);
}

// Calculate optimal path with multiple directions and waypoints
function calculateOptimalPath(startPos, endPos, waypoints = []) {
  const allSegments = [];
  
  // If waypoints exist, create path through each waypoint
  if (waypoints && waypoints.length > 0) {
    console.log('🔧 Creating path with', waypoints.length, 'waypoints');
    console.log('🔧 Start position:', startPos);
    console.log('🔧 End position:', endPos);
    console.log('🔧 All waypoints:', waypoints);
    
    let currentPos = startPos;
    
    // Create segments to each waypoint
    waypoints.forEach((waypoint, index) => {
      console.log(`🔧 Processing waypoint ${index + 1}/${waypoints.length}:`, waypoint);
      console.log(`🔧 Creating path from current position:`, currentPos, 'to waypoint:', waypoint);
      
      const waypointSegments = calculatePathBetweenPoints(currentPos, waypoint);
      console.log(`🔧 Generated ${waypointSegments.length} segments for waypoint ${index + 1}`);
      
      if (waypointSegments.length === 0) {
        console.warn(` No segments generated for waypoint ${index + 1}!`);
      }
      
      allSegments.push(...waypointSegments);
      currentPos = waypoint;
      console.log(`🔧 Waypoint ${index + 1} reached. Current position updated to:`, currentPos);
    });
    
    // Create final segment to end position only if it's different from the last waypoint
    const lastWaypoint = waypoints[waypoints.length - 1];
    const isEndPosSameAsLastWaypoint = lastWaypoint && 
      Math.abs(lastWaypoint.x - endPos.x) < 0.1 && 
      Math.abs(lastWaypoint.y - endPos.y) < 0.1 && 
      Math.abs(lastWaypoint.z - endPos.z) < 0.1;
    
    if (!isEndPosSameAsLastWaypoint) {
      console.log('🔧 Creating final segment from last waypoint to end position');
      const finalSegments = calculatePathBetweenPoints(currentPos, endPos);
      allSegments.push(...finalSegments);
      console.log('🔧 Added final segment to end position, total segments:', allSegments.length);
    } else {
      console.log('🔧 Skipping final segment - end position same as last waypoint');
    }
    
  } else {
    // No waypoints - create direct path
    console.log('🔧 No waypoints, creating direct path');
    const directSegments = calculatePathBetweenPoints(startPos, endPos);
    allSegments.push(...directSegments);
  }
  
  console.log('🔧 Total segments generated:', allSegments.length);
  return allSegments;
}

// Calculate path between two specific points
function calculatePathBetweenPoints(startPos, endPos) {
  const segments = [];
  
  // Calculate differences in each axis
  const deltaX = endPos.x - startPos.x;
  const deltaZ = endPos.z - startPos.z;
  
  console.log('🔧 Path calculation from:', startPos, 'to:', endPos);
  console.log('🔧 Path calculation - Delta X:', deltaX, 'Delta Z:', deltaZ);
  
  // If both deltas are zero (same position), create a point segment
  if (Math.abs(deltaX) < 0.01 && Math.abs(deltaZ) < 0.01) {
    console.log('🔧 Points are at same position, creating point segment');
    segments.push({
      start: startPos,
      end: endPos,
      direction: 'point'
    });
    return segments;
  }
  
  // Determine the best path based on the destination position
  if (Math.abs(deltaX) > Math.abs(deltaZ)) {
    // X-axis movement is larger - go horizontal first, then vertical
    if (deltaX > 0) {
      // Moving right
      console.log('🔧 Path: Right then Up/Down');
      segments.push({
        start: startPos,
        end: { x: endPos.x, y: startPos.y, z: startPos.z },
        direction: 'horizontal-right'
      });
      if (deltaZ !== 0) {
        segments.push({
          start: { x: endPos.x, y: startPos.y, z: startPos.z },
          end: endPos,
          direction: deltaZ > 0 ? 'vertical-up' : 'vertical-down'
        });
      }
    } else {
      // Moving left
      console.log('🔧 Path: Left then Up/Down');
      segments.push({
        start: startPos,
        end: { x: endPos.x, y: startPos.y, z: startPos.z },
        direction: 'horizontal-left'
      });
      if (deltaZ !== 0) {
        segments.push({
          start: { x: endPos.x, y: startPos.y, z: startPos.z },
          end: endPos,
          direction: deltaZ > 0 ? 'vertical-up' : 'vertical-down'
        });
      }
    }
  } else {
    // Z-axis movement is larger - go vertical first, then horizontal
    if (deltaZ > 0) {
      // Moving up (forward)
      console.log('🔧 Path: Up then Left/Right');
      segments.push({
        start: startPos,
        end: { x: startPos.x, y: startPos.y, z: endPos.z },
        direction: 'vertical-up'
      });
      if (deltaX !== 0) {
        segments.push({
          start: { x: startPos.x, y: startPos.y, z: endPos.z },
          end: endPos,
          direction: deltaX > 0 ? 'horizontal-right' : 'horizontal-left'
        });
      }
    } else {
      // Moving down (backward)
      console.log('🔧 Path: Down then Left/Right');
      segments.push({
        start: startPos,
        end: { x: startPos.x, y: startPos.y, z: endPos.z },
        direction: 'vertical-down'
      });
      if (deltaX !== 0) {
        segments.push({
          start: { x: startPos.x, y: startPos.y, z: endPos.z },
          end: endPos,
          direction: deltaX > 0 ? 'horizontal-right' : 'horizontal-left'
        });
      }
    }
  }
  
  // If no movement needed, create a single point segment
  if (segments.length === 0) {
    segments.push({
      start: startPos,
      end: endPos,
      direction: 'point'
    });
  }
  
  return segments;
}

// Create smart wayfinding tube route from You Are Here to destination
function createWayfindingRoute(startPos, endPos, destinationName, waypoints = []) {
  console.log('🔧 createWayfindingRoute called with:', { startPos, endPos, destinationName, waypoints });
  
  if (!poiGroup || !scene) {
    console.warn('❌ Cannot create wayfinding route - scene not ready');
    console.warn('❌ poiGroup exists:', !!poiGroup);
    console.warn('❌ scene exists:', !!scene);
    return;
  }
  
  // Normalize waypoints to ensure they have proper coordinates
  let normalizedWaypoints = [];
  if (waypoints && waypoints.length > 0) {
    console.log('🔧 Processing', waypoints.length, 'waypoints for normalization');
    normalizedWaypoints = waypoints.map((wp, index) => {
      // Ensure waypoint has x, y, z coordinates
      // If Y is not provided or is 0, use appropriate default based on scene type
      let yValue = typeof wp.y === 'number' ? wp.y : null;
      if (yValue === null || yValue === 0) {
        // Use groundY for GLB models, or startPos.y for image planes
        yValue = isModelActive ? (groundY || startPos.y) : startPos.y;
      }
      
      const normalized = {
        x: typeof wp.x === 'number' ? wp.x : 0,
        y: yValue,
        z: typeof wp.z === 'number' ? wp.z : 0
      };
      
      console.log(`🔧 Normalized waypoint ${index + 1}:`, {
        original: wp,
        normalized: normalized,
        isModelActive: isModelActive,
        groundY: groundY
      });
      
      return normalized;
    });
    console.log('🔧 All waypoints normalized:', normalizedWaypoints);
  } else {
    console.log('🔧 No waypoints provided or waypoints array is empty');
  }
  
  // If waypoints exist, use the last waypoint as the actual end position
  let actualEndPos = endPos;
  if (normalizedWaypoints && normalizedWaypoints.length > 0) {
    actualEndPos = normalizedWaypoints[normalizedWaypoints.length - 1];
    console.log('🔧 Using last waypoint as end position:', actualEndPos);
  }
  
  console.log('✅ Scene and poiGroup are ready, creating L-shaped pipe...');
  console.log('Creating L-shaped wayfinding route from', startPos, 'to', actualEndPos, 'for', destinationName);
  console.log('🔧 Normalized waypoints:', normalizedWaypoints);
  console.log('🔧 Number of waypoints:', normalizedWaypoints.length);
  
  // Clear existing routes (both wayfinding and straight line routes)
  clearRoute();
  clearWayfindingRoute();
  
  // Create smart pathway: Calculate optimal path with multiple directions and waypoints
  const pathSegments = calculateOptimalPath(startPos, actualEndPos, normalizedWaypoints);
  
  console.log('🔧 Smart pathway calculation complete');
  console.log('🔧 Total segments generated:', pathSegments.length);
  
  if (pathSegments.length === 0) {
    console.error('❌ No path segments generated! Cannot create wayfinding tubes.');
    console.error('❌ Start position:', startPos);
    console.error('❌ End position:', actualEndPos);
    console.error('❌ Waypoints:', normalizedWaypoints);
    return;
  }
  
  pathSegments.forEach((segment, index) => {
    console.log(`🔧 Segment ${index + 1}/${pathSegments.length}:`, {
      start: segment.start,
      end: segment.end,
      direction: segment.direction
    });
  });
  
  // Animation settings
  const segmentDelay = 800; // Delay between segments (ms)
  const growDuration = 600; // Time to grow each segment (ms)
  
  // Create animated pipe segments for each path segment
  console.log('🎬 Starting to create pipe segments...');
  createAnimatedPipeSegments(pathSegments, destinationName, segmentDelay, growDuration);
  
  // Create destination marker at the final position (actualEndPos) after animation completes
  console.log('🎯 Creating destination marker at final position:', actualEndPos);
  console.log('🎯 Original endPos was:', endPos);
  
  // Calculate total animation time and show destination marker at the end
  const totalAnimationTime = (pathSegments.length - 1) * segmentDelay + growDuration;
  setTimeout(() => {
    createDestinationMarker(actualEndPos, destinationName);
    console.log('🎯 Destination marker created after animation complete');
  }, totalAnimationTime + 200); // Small delay after last segment completes
  
  console.log('✅ L-shaped wayfinding route created to', destinationName);
}

// Create individual pipe segment with enhanced direction support
function createPipeSegment(startPos, endPos, direction, destinationName) {
  const pipeRadius = 4; // Reduced from 8 to 4 for thinner connectors
  
  // Calculate distance for this segment
  let distance, geometry, material, pipe;
  
  if (direction.startsWith('vertical')) {
    // Vertical pipe (Z direction) - lay it flat on the surface
    distance = Math.abs(endPos.z - startPos.z);
    geometry = new THREE.CylinderGeometry(pipeRadius, pipeRadius, distance, 12);
    // Rotate to make it horizontal (flat on surface)
    geometry.rotateX(Math.PI / 2);
    
    material = new THREE.MeshBasicMaterial({
      color: 0x00ff00, // Green pipe color
      transparent: true,
      opacity: 0.9,
      side: THREE.DoubleSide
    });
    
    pipe = new THREE.Mesh(geometry, material);
    
    // Position at midpoint of vertical segment, raised above surface
    const midZ = (startPos.z + endPos.z) / 2;
    pipe.position.set(startPos.x, startPos.y + 16, midZ); // Raised above surface level
    
    console.log('🔧 Vertical pipe segment (' + direction + '):', {
      distance: distance,
      position: pipe.position,
      from: startPos,
      to: endPos
    });
    
  } else if (direction.startsWith('horizontal')) {
    // Horizontal pipe (X direction) - lay it flat on the surface
    distance = Math.abs(endPos.x - startPos.x);
    geometry = new THREE.CylinderGeometry(pipeRadius, pipeRadius, distance, 12);
    // Rotate to make it horizontal and flat on surface
    geometry.rotateZ(Math.PI / 2);
    
    material = new THREE.MeshBasicMaterial({
      color: 0x00ff00, // Green pipe color
      transparent: true,
      opacity: 0.9,
      side: THREE.DoubleSide
    });
    
    pipe = new THREE.Mesh(geometry, material);
    
    // Position at midpoint of horizontal segment, raised above surface
    const midX = (startPos.x + endPos.x) / 2;
    pipe.position.set(midX, startPos.y + 16, startPos.z); // Raised above surface level
    
    // Calculate rotation for horizontal pipe to point from start to end
    const angle = Math.atan2(endPos.z - startPos.z, endPos.x - startPos.x);
    pipe.rotation.y = angle;
    
    console.log('🔧 Horizontal pipe segment (' + direction + '):', {
      distance: distance,
      position: pipe.position,
      rotation: pipe.rotation,
      from: startPos,
      to: endPos
    });
    
  } else if (direction === 'point') {
    // Single point - create a small sphere
    geometry = new THREE.SphereGeometry(pipeRadius, 8, 6);
    material = new THREE.MeshBasicMaterial({
      color: 0x00ff00,
      transparent: true,
      opacity: 0.9
    });
    
    pipe = new THREE.Mesh(geometry, material);
    pipe.position.set(startPos.x, startPos.y + pipeRadius + 15, startPos.z);
    
    console.log('🔧 Point segment:', {
      position: pipe.position,
      at: startPos
    });
  }
  
  pipe.userData = {
    wayfindingTube: true,
    destination: destinationName,
    type: 'wayfindingTube',
    direction: direction
  };
  
  // Animation removed - static tube
  
  poiGroup.add(pipe);
  pois.push(pipe);
  
  console.log('✅ Pipe segment added to scene');
}

// Create animated pipe segments that grow from start to finish
function createAnimatedPipeSegments(pathSegments, destinationName, segmentDelay, growDuration) {
  if (!poiGroup) {
    console.error('❌ Cannot create pipe segments - poiGroup is null');
    return;
  }
  
  console.log('🎬 Creating animated pipe segments...');
  console.log('🎬 Total segments to create:', pathSegments.length);
  console.log('🎬 Segment delay:', segmentDelay, 'ms');
  console.log('🎬 Grow duration:', growDuration, 'ms');
  
  if (pathSegments.length === 0) {
    console.warn(' No path segments to create pipes for!');
    return;
  }
  
  pathSegments.forEach((segment, index) => {
    // Calculate delay for this segment
    const delay = index * segmentDelay;
    
    console.log(`🎬 Scheduling segment ${index + 1}/${pathSegments.length} with delay ${delay}ms`);
    console.log(`🎬 Segment ${index + 1} details:`, {
      start: segment.start,
      end: segment.end,
      direction: segment.direction
    });
    
    // Create segment function
    const createSegment = () => {
      try {
        console.log(`🎬 Creating segment ${index + 1}/${pathSegments.length} now (delay was ${delay}ms)`);
        console.log(`🎬 Segment ${index + 1} start:`, segment.start);
        console.log(`🎬 Segment ${index + 1} end:`, segment.end);
        console.log(`🎬 Segment ${index + 1} direction:`, segment.direction);
        
        // Create the pipe segment
        const isFinalSegment = index === pathSegments.length - 1;
        console.log(`🎬 Calling createAnimatedPipeSegment for segment ${index + 1}...`);
        console.log(`🎬 Parameters:`, {
          startPos: segment.start,
          endPos: segment.end,
          direction: segment.direction,
          destinationName: destinationName,
          duration: growDuration,
          isFinalSegment: isFinalSegment
        });
        
        const pipe = createAnimatedPipeSegment(segment.start, segment.end, segment.direction, destinationName, growDuration, isFinalSegment);
        
        if (!pipe) {
          console.error(`❌ Failed to create pipe segment ${index + 1} - createAnimatedPipeSegment returned null`);
          return;
        }
        
        console.log(`✅ Pipe segment ${index + 1} created successfully!`);
        console.log(`✅ Pipe position:`, pipe.position);
        console.log(`✅ Pipe scale:`, pipe.scale);
        console.log(`✅ Pipe visible:`, pipe.visible);
        console.log(`✅ Pipe in scene:`, poiGroup ? poiGroup.children.includes(pipe) : 'poiGroup is null');
        console.log(`✅ Total objects in poiGroup:`, poiGroup ? poiGroup.children.length : 0);
        
        // Add connection point between segments (except for the last segment)
        if (index < pathSegments.length - 1) {
          setTimeout(() => {
            const isFinalConnection = index === pathSegments.length - 2; // Second to last segment
            createConnectionPoint(segment.end, destinationName, isFinalConnection);
          }, growDuration * 0.8); // Show connection point near the end of segment growth
        }
      } catch (error) {
        console.error(`❌ Error creating segment ${index + 1}:`, error);
        console.error(`❌ Error stack:`, error.stack);
      }
    };
    
    // Execute immediately if delay is 0, otherwise use setTimeout
    if (delay === 0) {
      console.log(`🎬 Executing segment ${index + 1} immediately (no delay)`);
      createSegment();
    } else {
      console.log(`🎬 Scheduling segment ${index + 1} with setTimeout (${delay}ms delay)`);
      setTimeout(() => {
        console.log(`🎬 setTimeout callback executing for segment ${index + 1}`);
        createSegment();
      }, delay);
    }
  });
  
  console.log('🎬 All segments scheduled for creation');
}

// Create a single animated pipe segment
function createAnimatedPipeSegment(startPos, endPos, direction, destinationName, duration, isFinalSegment = false) {
  console.log('🔧 createAnimatedPipeSegment called with:', {
    startPos,
    endPos,
    direction,
    destinationName,
    duration,
    isFinalSegment
  });
  
  if (!poiGroup) {
    console.error('❌ poiGroup is null in createAnimatedPipeSegment');
    return null;
  }
  
  const pipeRadius = 4; // Reduced from 8 to 4 for thinner connectors
  // Use elevated positioning only for final segment of Restroom destination
  const yOffset = (destinationName === 'Restroom' && isFinalSegment) ? 30 : 16;
  let geometry, material, pipe;
  
  // Calculate distance and direction
  const deltaX = endPos.x - startPos.x;
  const deltaZ = endPos.z - startPos.z;
  const distance = Math.sqrt(deltaX * deltaX + deltaZ * deltaZ);
  
  console.log('🔧 Pipe segment calculations:', {
    deltaX,
    deltaZ,
    distance,
    yOffset,
    pipeRadius
  });
  
  // Create geometry based on direction
  if (direction.startsWith('vertical')) {
    console.log('🔧 Creating vertical pipe segment');
    // Vertical pipe (along Z-axis)
    geometry = new THREE.CylinderGeometry(pipeRadius, pipeRadius, distance, 8);
    geometry.rotateX(Math.PI / 2);
    
    material = new THREE.MeshBasicMaterial({
      color: 0x00ff00, // Green pipe color
      transparent: true,
      opacity: 0.9,
      side: THREE.DoubleSide
    });
    
    pipe = new THREE.Mesh(geometry, material);
    pipe.position.set(
      startPos.x + deltaX / 2,
      startPos.y + yOffset,
      startPos.z + deltaZ / 2
    );
    console.log('🔧 Vertical pipe created at position:', pipe.position);
    
  } else if (direction.startsWith('horizontal')) {
    console.log('🔧 Creating horizontal pipe segment');
    // Horizontal pipe (along X-axis, but can be diagonal)
    geometry = new THREE.CylinderGeometry(pipeRadius, pipeRadius, distance, 8);
    geometry.rotateZ(Math.PI / 2);
    
    material = new THREE.MeshBasicMaterial({
      color: 0x00ff00, // Green pipe color
      transparent: true,
      opacity: 0.9,
      side: THREE.DoubleSide
    });
    
    pipe = new THREE.Mesh(geometry, material);
    pipe.position.set(
      startPos.x + deltaX / 2,
      startPos.y + yOffset,
      startPos.z + deltaZ / 2
    );
    
    // Calculate rotation for horizontal pipe to point from start to end (handles diagonal)
    if (deltaZ !== 0) {
      const angle = Math.atan2(deltaZ, deltaX);
      pipe.rotation.y = angle;
      console.log('🔧 Horizontal pipe rotated by angle:', angle);
    }
    console.log('🔧 Horizontal pipe created at position:', pipe.position);
    
  } else if (direction === 'point') {
    console.log('🔧 Creating point segment');
    // Single point - create a small sphere
    geometry = new THREE.SphereGeometry(pipeRadius, 8, 6);
    material = new THREE.MeshBasicMaterial({
      color: 0x00ff00,
      transparent: true,
      opacity: 0.9
    });
    
    pipe = new THREE.Mesh(geometry, material);
    pipe.position.set(startPos.x, startPos.y + yOffset, startPos.z);
  }
  
  if (!pipe) {
    console.error('❌ Failed to create pipe geometry');
    return null;
  }
  
  // Set initial scale to 0 (invisible) for animation
  pipe.scale.set(0, 0, 0);
  
  // Add user data
  pipe.userData = {
    destination: destinationName,
    type: 'wayfindingTube',
    direction: direction,
    isAnimating: true,
    wayfindingTube: true
  };
  
  // Add to scene immediately
  if (!poiGroup) {
    console.error('❌ poiGroup is null, cannot add pipe to scene');
    return null;
  }
  
  poiGroup.add(pipe);
  pois.push(pipe);
  
  console.log('✅ Pipe segment added to scene at position:', pipe.position);
  console.log('✅ Pipe segment scale (will animate):', pipe.scale);
  
  // Animate the growth
  if (duration > 0) {
    animatePipeGrowth(pipe, duration);
  } else {
    // If duration is 0, show immediately
    pipe.scale.set(1, 1, 1);
  }
  
  console.log('✅ Animated pipe segment created and added');
  return pipe;
}

// Animate pipe growth from 0 to full scale
function animatePipeGrowth(pipe, duration) {
  const startTime = Date.now();
  const startScale = { x: 0, y: 0, z: 0 };
  const endScale = { x: 1, y: 1, z: 1 };
  
  function animate() {
    const elapsed = Date.now() - startTime;
    const progress = Math.min(elapsed / duration, 1);
    
    // Use easing function for smooth animation
    const easedProgress = easeOutCubic(progress);
    
    // Update scale
    pipe.scale.set(
      startScale.x + (endScale.x - startScale.x) * easedProgress,
      startScale.y + (endScale.y - startScale.y) * easedProgress,
      startScale.z + (endScale.z - startScale.z) * easedProgress
    );
    
    if (progress < 1) {
      requestAnimationFrame(animate);
    } else {
      // Animation complete
      pipe.userData.isAnimating = false;
      console.log('✅ Pipe segment animation complete');
    }
  }
  
  animate();
}

// Easing function for smooth animation
function easeOutCubic(t) {
  return 1 - Math.pow(1 - t, 3);
}

// Create connection point at the corner of L-shaped path
function createConnectionPoint(position, destinationName = '', isFinalConnection = false) {
  if (!poiGroup) return;
  
  const connectionRadius = 10;
  // Use elevated positioning only for final connection of Restroom destination
  const yOffset = (destinationName === 'Restroom' && isFinalConnection) ? 30 : 16;
  const geometry = new THREE.SphereGeometry(connectionRadius, 12, 8);
  const material = new THREE.MeshBasicMaterial({
    color: 0x00ff00, // Green for connection point
    transparent: true,
    opacity: 0.9
  });
  
  const connectionPoint = new THREE.Mesh(geometry, material);
  connectionPoint.position.set(position.x, position.y + yOffset, position.z); // Raised to match tube level
  
  connectionPoint.userData = {
    wayfindingTube: true,
    type: 'connectionPoint'
  };
  
  // Animation removed - static connection point
  
  poiGroup.add(connectionPoint);
  pois.push(connectionPoint);
  
  console.log('✅ Connection point added at:', position);
}

// Create end caps for the pipe to make it look more realistic
function createPipeEndCaps(startPos, endPos, pipeRadius) {
  if (!poiGroup) return;
  
  // Create end cap geometry (circle)
  const capGeometry = new THREE.CircleGeometry(pipeRadius, 12);
  
  // Create cap material
  const capMaterial = new THREE.MeshBasicMaterial({
    color: 0x004499, // Darker blue for caps
    transparent: true,
    opacity: 0.9,
    side: THREE.DoubleSide
  });
  
  // Start cap
  const startCap = new THREE.Mesh(capGeometry, capMaterial);
  startCap.position.set(startPos.x, startPos.y + 3, startPos.z);
  
  // Calculate rotation for start cap
  const startAngle = Math.atan2(endPos.z - startPos.z, endPos.x - startPos.x);
  startCap.rotation.y = startAngle;
  startCap.rotation.x = Math.PI / 2; // Rotate to be vertical
  
  startCap.userData = {
    wayfindingTube: true,
    type: 'pipeCap'
  };
  
  // End cap
  const endCap = new THREE.Mesh(capGeometry, capMaterial);
  endCap.position.set(endPos.x, endPos.y + 3, endPos.z);
  
  // Calculate rotation for end cap (opposite direction)
  const endAngle = startAngle + Math.PI;
  endCap.rotation.y = endAngle;
  endCap.rotation.x = Math.PI / 2; // Rotate to be vertical
  
  endCap.userData = {
    wayfindingTube: true,
    type: 'pipeCap'
  };
  
  poiGroup.add(startCap);
  poiGroup.add(endCap);
  pois.push(startCap, endCap);
  
  console.log('🔧 Start cap position:', startCap.position);
  console.log('🔧 End cap position:', endCap.position);
}

// Generate route points between start and end positions
function generateRoutePoints(startPos, endPos) {
  const points = [];
  const numPoints = 8; // Number of wayfinding tubes along the route
  
  for (let i = 0; i <= numPoints; i++) {
    const t = i / numPoints;
    const point = {
      x: startPos.x + (endPos.x - startPos.x) * t,
      y: startPos.y + (endPos.y - startPos.y) * t,
      z: startPos.z + (endPos.z - startPos.z) * t
    };
    points.push(point);
  }
  
  return points;
}

// Create destination marker at the end of the route
function createDestinationMarker(position, destinationName) {
  if (!poiGroup || !scene) {
    console.warn('Cannot create destination marker - scene not ready');
    return;
  }
  
  console.log('Creating destination marker for', destinationName, 'at', position);
  
  // Create destination tube (much larger and different color)
  const geometry = new THREE.CylinderGeometry(25, 25, 80, 16);
  const material = new THREE.MeshBasicMaterial({
    color: '#ff6600', // Orange for destination
    transparent: true,
    opacity: 0.9,
    emissive: new THREE.Color('#ff6600'),
    emissiveIntensity: 0.4
  });
  
  const destinationTube = new THREE.Mesh(geometry, material);
  destinationTube.position.set(position.x, position.y + 30, position.z);
  destinationTube.userData = { 
    destination: true,
    destinationName: destinationName
  };
  
  poiGroup.add(destinationTube);
  pois.push(destinationTube);
  
  // Add destination label with larger text
  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d');
  canvas.width = 512; // Larger canvas
  canvas.height = 128; // Larger canvas
  
  context.fillStyle = 'rgba(255, 102, 0, 0.9)';
  context.fillRect(0, 0, canvas.width, canvas.height);
  
  context.fillStyle = '#ffffff';
  context.font = 'bold 32px Arial'; // Much larger font
  context.textAlign = 'center';
  context.fillText('DESTINATION', canvas.width / 2, 50);
  context.fillText(destinationName, canvas.width / 2, 90);
  
  const texture = new THREE.CanvasTexture(canvas);
  const labelMaterial = new THREE.SpriteMaterial({ 
    map: texture, 
    transparent: true,
    alphaTest: 0.1
  });
  
  const label = new THREE.Sprite(labelMaterial);
  label.scale.set(80, 20, 1); // Larger label to match improved readability
  label.position.set(0, 50, 0); // Higher position for larger marker
  
  destinationTube.add(label);
  
  // Animation removed - static destination marker
  
  console.log('✅ Destination marker created for', destinationName);
}

// Helper function to find stairs/elevator POI on a floor
function findStairsOnFloor(floorPois) {
  if (!Array.isArray(floorPois)) return null;
  
  // Look for POIs with category "Stairs", "Elevator", or names containing "stair" or "elevator"
  const stairsPoi = floorPois.find(poi => {
    const category = (poi.category || '').toLowerCase();
    const name = (poi.name || '').toLowerCase();
    return category.includes('stair') || 
           category.includes('elevator') || 
           name.includes('stair') || 
           name.includes('elevator');
  });
  
  return stairsPoi || null;
}

// Handle cross-floor wayfinding: route to stairs, load new floor, route to destination
async function handleCrossFloorWayfinding(destinationPoi, targetFloorId) {
  console.log('🔄 Starting cross-floor wayfinding to:', destinationPoi.name, 'on floor:', targetFloorId);
  
  if (!currentFloor) {
    console.warn('❌ No current floor data available');
    // Fallback: just load the floor and route
    await loadFloorAndPois(targetFloorId);
    const newFloorPois = currentFloor?.pois || [];
    const destination = newFloorPois.find(p => p.id == destinationPoi.id);
    if (destination) {
      handleDestinationSelection(destination);
      showDestinationInfo(destination);
    }
    return;
  }
  
  // Step 1: Check if current floor has stairs coordinates in database
  const hasCurrentStairs = currentFloor.stairs_model_x !== null && currentFloor.stairs_model_x !== undefined;
  
  if (hasCurrentStairs) {
    console.log('✅ Found stairs coordinates on current floor from database');
    
    // Step 2: Create wayfinding route to stairs on current floor
    clearWayfindingRoute();
    clearRoute();
    
    // Get start position (You Are Here)
    let startPos;
    if (currentFloor && currentFloor.you_are_here_model_x !== null && currentFloor.you_are_here_model_x !== undefined) {
      startPos = {
        x: currentFloor.you_are_here_model_x,
        y: (currentFloor.you_are_here_model_y !== null && currentFloor.you_are_here_model_y !== undefined)
          ? currentFloor.you_are_here_model_y
          : groundY,
        z: currentFloor.you_are_here_model_z ?? 0,
      };
      console.log('🎯 Using per-floor "You Are Here" position as route start:', startPos);
    } else {
      // Legacy default
      startPos = { x: -62, y: 2, z: 275 };
      console.log('🎯 Using default "You Are Here" position as route start:', startPos);
    }
    
    // Get stairs position from floor database coordinates
    const stairsPos = {
      x: currentFloor.stairs_model_x,
      y: (currentFloor.stairs_model_y !== null && currentFloor.stairs_model_y !== undefined)
        ? currentFloor.stairs_model_y
        : groundY,
      z: currentFloor.stairs_model_z ?? 0,
    };
    
    console.log('🎯 Creating route to stairs:', startPos, '->', stairsPos);
    
    // Create route to stairs
    createWayfindingRoute(startPos, stairsPos, 'Stairs', []);
    
    // Step 3: Wait a moment for the route to be visible, then load new floor
    setTimeout(async () => {
      console.log('🔄 Loading target floor:', targetFloorId);
      await loadFloorAndPois(targetFloorId);
      
      // Wait for floor to fully load
      setTimeout(() => {
        // Step 4: Check if new floor has stairs coordinates in database
        const newFloorPois = currentFloor?.pois || [];
        const destination = newFloorPois.find(p => p.id == destinationPoi.id);
        
        if (!destination) {
          console.error('❌ Destination POI not found on new floor');
          return;
        }
        
        // Step 5: Create route from stairs to destination on new floor
        const hasNewStairs = currentFloor && currentFloor.stairs_model_x !== null && currentFloor.stairs_model_x !== undefined;
        
        if (hasNewStairs) {
          console.log('✅ Found stairs coordinates on new floor from database');
          
          // Get stairs position from new floor database coordinates
          const newStairsPos = {
            x: currentFloor.stairs_model_x,
            y: (currentFloor.stairs_model_y !== null && currentFloor.stairs_model_y !== undefined)
              ? currentFloor.stairs_model_y
              : groundY,
            z: currentFloor.stairs_model_z ?? 0,
          };
          
          // Get destination position
          let destPos;
          if (destination.meta?.modelPosition) {
            destPos = {
              x: destination.meta.modelPosition.x,
              y: destination.meta.modelPosition.y || groundY,
              z: destination.meta.modelPosition.z || 0
            };
          } else {
            const imgW = currentFloor.width || 1200;
            const imgH = currentFloor.height || 800;
            const scenePos = imageToScene(destination.x, destination.y, imgW, imgH);
            destPos = {
              x: scenePos.x,
              y: groundY,
              z: scenePos.y
            };
          }
          
          console.log('🎯 Creating route from stairs to destination:', newStairsPos, '->', destPos);
          
          // Clear previous route and create new one
          clearWayfindingRoute();
          clearRoute();
          
          const waypoints = destination.meta?.waypoints || [];
          createWayfindingRoute(newStairsPos, destPos, destination.name, waypoints);
          
          currentDestination = destination;
          showDestinationInfo(destination);
        } else {
          // No stairs coordinates on new floor, route directly from entrance
          console.log(' No stairs coordinates found on new floor, routing from entrance');
          handleDestinationSelection(destination);
          showDestinationInfo(destination);
        }
      }, 500);
    }, 2000); // Wait 2 seconds to show route to stairs
    
  } else {
    // No stairs coordinates on current floor, just load new floor and route
    console.log(' No stairs coordinates found on current floor, loading target floor directly');
    await loadFloorAndPois(targetFloorId);
    const newFloorPois = currentFloor?.pois || [];
    const destination = newFloorPois.find(p => p.id == destinationPoi.id);
    if (destination) {
      handleDestinationSelection(destination);
      showDestinationInfo(destination);
    }
  }
}

// Handle destination selection and create route
function handleDestinationSelection(destinationPoi) {
  if (!destinationPoi) {
    console.warn('❌ No destination POI provided');
    return;
  }
  
  console.log('🎯 Creating route to destination:', destinationPoi.name);
  console.log('🎯 Destination POI data:', destinationPoi);
  
  // Clear any existing wayfinding route first
  clearWayfindingRoute();
  clearRoute(); // Also clear any old route lines
  
  // Define start and end positions
  let startPos;

  // If floor config says to start from stairs and we have coordinates, use them
  if (currentFloor && currentFloor.use_stairs_as_start &&
      currentFloor.stairs_model_x !== null && currentFloor.stairs_model_x !== undefined) {
    startPos = {
      x: currentFloor.stairs_model_x,
      y: (currentFloor.stairs_model_y !== null && currentFloor.stairs_model_y !== undefined)
        ? currentFloor.stairs_model_y
        : groundY,
      z: currentFloor.stairs_model_z ?? 0,
    };
    console.log('🎯 Using stairs position as route start:', startPos);
  } else {
    // Use per-floor "You Are Here" position if available, else fallback
    if (currentFloor && currentFloor.you_are_here_model_x !== null && currentFloor.you_are_here_model_x !== undefined) {
      startPos = {
        x: currentFloor.you_are_here_model_x,
        y: (currentFloor.you_are_here_model_y !== null && currentFloor.you_are_here_model_y !== undefined)
          ? currentFloor.you_are_here_model_y
          : groundY,
        z: currentFloor.you_are_here_model_z ?? 0,
      };
      console.log('🎯 Using per-floor "You Are Here" position as route start:', startPos);
    } else {
      // Legacy default
      startPos = { x: -62, y: 2, z: 275 };
      console.log('🎯 Using default "You Are Here" position as route start:', startPos);
    }
  }
  // Use the exact coordinates for Information Desk
  const endPos = destinationPoi.meta?.modelPosition || { 
    x: -3, 
    y: -30, 
    z: 180 
  };
  
  console.log('🎯 Start position:', startPos);
  console.log('🎯 End position:', endPos);
  console.log('🎯 Scene exists:', !!scene);
  console.log('🎯 POI Group exists:', !!poiGroup);
  
  // Create the wayfinding route with waypoints
  const waypoints = destinationPoi.meta?.waypoints || [];
  createWayfindingRoute(startPos, endPos, destinationPoi.name, waypoints);
  
  // Update current destination
  currentDestination = destinationPoi;
  
  console.log('✅ Route creation completed for:', destinationPoi.name);
}

// Clear wayfinding route - comprehensive clearing function
function clearWayfindingRoute() {
  if (!poiGroup) return;
  
  console.log('🧹 Clearing all wayfinding elements...');
  
  // Remove wayfinding tubes, pipe caps, destination markers, and connection points
  const objectsToRemove = [];
  poiGroup.traverse((child) => {
    if (child.userData && (
      child.userData.wayfindingTube || 
      child.userData.destination || 
      child.userData.pipeCap || 
      child.userData.connectionPoint ||
      child.userData.type === 'wayfindingTube' ||
      child.userData.type === 'destination' ||
      child.userData.type === 'connectionPoint'
    )) {
      objectsToRemove.push(child);
    }
  });
  
  console.log('🧹 Found', objectsToRemove.length, 'wayfinding objects to remove');
  
  objectsToRemove.forEach(obj => {
    if (obj.parent) {
      obj.parent.remove(obj);
    }
    if (obj.geometry) obj.geometry.dispose();
    if (obj.material) {
      if (Array.isArray(obj.material)) {
        obj.material.forEach(mat => mat.dispose());
      } else {
        obj.material.dispose();
      }
    }
  });
  
  // Also remove from pois array
  for (let i = pois.length - 1; i >= 0; i--) {
    if (pois[i].userData && (
      pois[i].userData.wayfindingTube || 
      pois[i].userData.destination || 
      pois[i].userData.pipeCap || 
      pois[i].userData.connectionPoint ||
      pois[i].userData.type === 'wayfindingTube' ||
      pois[i].userData.type === 'destination' ||
      pois[i].userData.type === 'connectionPoint'
    )) {
      pois.splice(i, 1);
    }
  }
  
  console.log('✅ Wayfinding route cleared successfully');
}

// Hide destination info
function hideDestinationInfo() {
  const infoDiv = document.getElementById('destination-info');
  if (infoDiv) {
    infoDiv.remove();
  }
}

// Zoom functions for the new UI
function zoomIn() {
  if (camera) {
    if (camera.zoom !== undefined) {
      // For orthographic camera (zoom-based)
      camera.zoom = Math.min(camera.zoom * 1.2, maxZoom);
      camera.updateProjectionMatrix();
      console.log('🔍 Zoomed in to:', camera.zoom);
    } else if (controls) {
      // For perspective camera (distance-based)
      const currentDistance = camera.position.distanceTo(controls.target);
      const newDistance = Math.max(currentDistance * 0.8, 10);
      
      const direction = new THREE.Vector3();
      camera.getWorldDirection(direction);
      direction.multiplyScalar(currentDistance - newDistance);
      
      camera.position.add(direction);
      controls.update();
      console.log('🔍 Zoomed in');
    }
  }
}

function zoomOut() {
  if (camera) {
    if (camera.zoom !== undefined) {
      // For orthographic camera (zoom-based)
      camera.zoom = Math.max(camera.zoom * 0.8, minZoom);
      camera.updateProjectionMatrix();
      console.log('🔍 Zoomed out to:', camera.zoom);
    } else if (controls) {
      // For perspective camera (distance-based)
      const currentDistance = camera.position.distanceTo(controls.target);
      const newDistance = Math.min(currentDistance * 1.25, 500);
      
      const direction = new THREE.Vector3();
      camera.getWorldDirection(direction);
      direction.multiplyScalar(newDistance - currentDistance);
      
      camera.position.add(direction);
      controls.update();
      console.log('🔍 Zoomed out');
    }
  }
}

// Test function to create route to Civil Registry Office
function testCivilRegistryRoute() {
  const civilRegistryPoi = {
    name: 'Civil Registry Office',
    meta: {
      modelPosition: {
        x: 10,
        y: 2,
        z: 50
      }
    }
  };
  
  handleDestinationSelection(civilRegistryPoi);
}

// Global function to test Civil Registry route (callable from browser console)
window.testCivilRegistryRoute = testCivilRegistryRoute;

// click / touch handler
function onPointerDown(evt){
  if (!renderer || !poiGroup || !camera) return;
  const rect = renderer.domElement.getBoundingClientRect();
  pointer.x = ((evt.clientX - rect.left) / rect.width) * 2 - 1;
  pointer.y = - ((evt.clientY - rect.top) / rect.height) * 2 + 1;
  raycaster.setFromCamera(pointer, camera);

  const intersects = raycaster.intersectObjects(poiGroup.children, true);
  if (intersects.length) {
    const spr = intersects[0].object;
    onPoiClicked(spr.userData.poi);
  } else {
    // Clicked empty space => clear route
    clearRoute();
  }
}

function onPoiClicked(poi){
  console.log('POI clicked', poi);
  
  // Set the clicked POI as destination and create wayfinding route from entrance
  if (currentLocation) {
    // Use the wayfinding system instead of drawRoute
    handleDestinationSelection(poi);
    // Update the destination dropdown to show selected POI
    document.getElementById('location-select').value = poi.id;
  } else {
    console.log('No current location set (entrance not found)');
  }
}

// Draw straight-line route between two pois
async function drawRoute(from, to){
  if (!scene) return;
  clearRoute();
  try {
    const res = await fetch(`${API_BASE}/route?from=${from.id}&to=${to.id}`);
    if (!res.ok) throw new Error('route api error');
    const data = await res.json();
    const pts = (data.points || []).map(p => {
      const s = imageToScene(p.x, p.y, mapExtents.width, mapExtents.height);
      return new THREE.Vector3(s.x, s.y, 6);
    });
    if (pts.length >= 2) {
      const curve = new THREE.CatmullRomCurve3(pts);
      const points = curve.getPoints(100);
      const geometry = new THREE.BufferGeometry().setFromPoints(points);
      const material = new THREE.LineBasicMaterial({ linewidth: 4 });
      routeLine = new THREE.Line(geometry, material);
      routeLine.renderOrder = 2;
      scene.add(routeLine);
      const mid = points[Math.floor(points.length/2)];
      camera.position.set(mid.x, mid.y, camera.position.z);
      return;
    }
    // fallback to straight line if API returned no points
    drawRouteSimple(from, to);
  } catch (e) {
    // fallback to straight line if API fails
    drawRouteSimple(from, to);
  }
}

function drawRouteSimple(from, to){
  if (!scene) return;
  clearRoute();
  let fromPos, toPos;
  if (isModelActive && from && to && from.meta && from.meta.modelPosition && to.meta && to.meta.modelPosition) {
    const fa = from.meta.modelPosition; const ta = to.meta.modelPosition;
    fromPos = new THREE.Vector3(
      typeof fa.x === 'number' ? fa.x : 0,
      typeof fa.y === 'number' ? fa.y : 0,
      (typeof fa.z === 'number' ? fa.z : 0) + 6
    );
    toPos = new THREE.Vector3(
      typeof ta.x === 'number' ? ta.x : 0,
      typeof ta.y === 'number' ? ta.y : 0,
      (typeof ta.z === 'number' ? ta.z : 0) + 6
    );
  } else {
    const f2 = imageToScene(from.x, from.y, mapExtents.width, mapExtents.height);
    const t2 = imageToScene(to.x, to.y, mapExtents.width, mapExtents.height);
    fromPos = new THREE.Vector3(f2.x, f2.y, 6);
    toPos = new THREE.Vector3(t2.x, t2.y, 6);
  }
  const curve = new THREE.CatmullRomCurve3([fromPos, toPos]);
  const points = curve.getPoints(50);
  const geometry = new THREE.BufferGeometry().setFromPoints(points);
  const material = new THREE.LineBasicMaterial({ linewidth: 4 });
  routeLine = new THREE.Line(geometry, material);
  routeLine.renderOrder = 2;
  scene.add(routeLine);
  camera.position.set((fromPos.x+toPos.x)/2, (fromPos.y+toPos.y)/2, camera.position.z);
}

// clear route with proper memory management
function clearRoute(){
  if (routeLine && scene) { 
    MEMORY_MANAGER.disposeObject(routeLine);
    scene.remove(routeLine); 
    routeLine = null; 
  }
}

// search filter
function populatePoiList(poiArray = getActivePois()){
  if (!Array.isArray(poiArray)) {
    poiArray = [];
  }
  const list = document.getElementById('poi-list');
  list.innerHTML = '';
  poiArray.forEach(p=>{
    const el = document.createElement('div');
    el.className = 'poi-item';
    const floorLabel = p.floorLabel || (p.floor && p.floor.name) || (currentFloor && currentFloor.name) || '';
    let suffix = '';
    if (p.category) suffix += ' — ' + p.category;
    if (floorLabel) suffix += ` (${floorLabel})`;
    el.textContent = `${p.name}${suffix}`;
    el.onclick = ()=> { onPoiClicked(p); };
    list.appendChild(el);
  });
}

function animate(){
  if (!renderer || !scene || !camera) return;
  
  // Performance monitoring
  PERFORMANCE_MONITOR.frameCount++;
  if (PERFORMANCE_MONITOR.frameCount % 60 === 0) { // Log every 60 frames
    PERFORMANCE_MONITOR.logMemory();
  }
  
  // Animate wayfinding tubes
  animateWayfindingTubes();
  
  requestAnimationFrame(animate);
  renderer.render(scene, camera);
}

// Animate wayfinding objects
function animateWayfindingTubes() {
  if (!poiGroup) return;
  
  poiGroup.traverse(obj => {
    if (obj.userData && obj.userData.animation) {
      const anim = obj.userData.animation;
      anim.time += 0.016; // ~60fps
      
      // Pulsing animation
      const pulse = Math.sin(anim.time * anim.pulseSpeed) * 0.1 + 0.9;
      obj.scale.copy(anim.originalScale).multiplyScalar(pulse);
      
      // Special animation for wayfinding tubes
      if (obj.userData.wayfindingTube) {
        // Wayfinding tubes pulse with opacity
        const tubePulse = Math.sin(anim.time * anim.pulseSpeed + anim.time * 2) * 0.15 + 0.85;
        obj.material.opacity = tubePulse;
      }
      
      // Special animation for destination markers
      if (obj.userData.destination) {
        // Destination markers have a gentle pulsing effect
        const destPulse = Math.sin(anim.time * anim.pulseSpeed * 0.5) * 0.05 + 0.95;
        obj.scale.copy(anim.originalScale).multiplyScalar(destPulse);
      }
    }
  });
}

// helper to fit camera to plane
function fitCameraToObject(camera, object, {offset = 1.25} = {}){
  const box = new THREE.Box3().setFromObject(object);
  const size = box.getSize(new THREE.Vector3());
  const center = box.getCenter(new THREE.Vector3());
  const width = size.x, height = size.y;
  const container = document.getElementById('canvas-container');
  camera.left = -container.clientWidth/2;
  camera.right = container.clientWidth/2;
  camera.top = container.clientHeight/2;
  camera.bottom = -container.clientHeight/2;
  // Set camera position to center of object
  camera.position.x = center.x;
  camera.position.y = center.y;
  // For orthographic cameras, keep Z position fixed
  camera.position.z = 1000;
  // Ensure camera is looking at the center and oriented correctly
  camera.lookAt(center.x, center.y, 0);
  camera.up.set(0, 1, 0);
  camera.updateProjectionMatrix();
}

// Event handlers for dropdowns
function onFloorChange(evt) {
  const floorId = evt.target.value;
  if (floorId) {
    loadFloorAndPois(floorId);
    // Clear destination and related UI when switching floors
    const destSelect = document.getElementById('location-select');
    if (destSelect) destSelect.value = '';
    hideDestinationInfo();
    currentDestination = null;
    const searchInput = document.getElementById('search');
    const poiList = document.getElementById('poi-list');
    if (searchInput) searchInput.value = '';
    if (poiList) poiList.innerHTML = '';
  }
}

async function onLocationChange(evt) {
  const destinationId = evt.target.value;
  console.log('🔍 Destination selection changed:', destinationId);
  
  if (destinationId) {
    // Clear any existing routes first
    clearWayfindingRoute();
    clearRoute();
    hideDestinationInfo();
    
    // First try to find in current floor POIs
    let destination = null;
    if (currentFloor && currentFloor.pois) {
      destination = currentFloor.pois.find(poi => poi.id == destinationId);
      console.log('🔍 Found in current floor POIs:', destination);
    }
    
    // If not found in current floor, search all floors
    if (!destination) {
      console.log('🔍 Destination not in current floor, searching all floors...');
      // Try to find the POI in all floors by fetching floor data
      // For now, we'll need to check if the dropdown contains floor info
      // This is a fallback - ideally the dropdown should only show current floor POIs
      console.warn(' Destination not found in current floor. Cross-floor navigation from dropdown requires floor data.');
    }
    
    if (destination) {
      console.log('🎯 Destination selected:', destination.name);
      console.log('🎯 Destination position:', destination.meta?.modelPosition);
      
      // Check if destination is on a different floor
      const targetFloorId = destination.floor_id || (destination.floor && destination.floor.id);
      const isCrossFloor = targetFloorId && (!currentFloor || currentFloor.id !== targetFloorId);
      
      if (isCrossFloor) {
        console.log('🔄 Destination is on a different floor. Starting cross-floor wayfinding to floor', targetFloorId);
        await handleCrossFloorWayfinding(destination, targetFloorId);
      } else {
        // Same floor - route directly
        handleDestinationSelection(destination);
        showDestinationInfo(destination);
      }
      
      // Note: drawRoute is disabled to prevent duplicate routes with wayfinding system
      // The wayfinding system handles all routing now
    } else {
      console.error('❌ Destination not found for ID:', destinationId);
    }
  } else if (!destinationId) {
    console.log('🧹 Clearing route - no destination selected');
    // Clear route if no destination selected
    clearRoute();
    clearWayfindingRoute();
    hideDestinationInfo();
  }
}

// Show destination information
function showDestinationInfo(destination) {
  // Create or update destination info display
  let infoDiv = document.getElementById('destination-info');
  if (!infoDiv) {
    infoDiv = document.createElement('div');
    infoDiv.id = 'destination-info';
    infoDiv.style.cssText = `
      position: absolute;
      top: 120px;
      left: 10px;
      background: rgba(255, 255, 255, 0.95);
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      max-width: 300px;
      z-index: 100;
    `;
    document.getElementById('ui').appendChild(infoDiv);
  }
  
  infoDiv.innerHTML = `
    <h3 style="margin: 0 0 10px 0; color: #333;">${destination.name}</h3>
    <p style="margin: 0; font-size: 12px; color: #666;">${destination.category}</p>
    <div style="margin-top: 10px; padding: 8px; background: #f0f0f0; border-radius: 4px;">
      <small style="color: #666;">Follow the colored tube to reach your destination</small>
    </div>
  `;
}

// Hide destination information
function hideDestinationInfo() {
  const infoDiv = document.getElementById('destination-info');
  if (infoDiv) {
    infoDiv.remove();
  }
}

// Populate destination dropdown with POIs from current floor (excluding entrance)
function populateDestinationSelect(poiArray = getActivePois()) {
  if (!Array.isArray(poiArray)) {
    poiArray = [];
  }
  const locationSelect = document.getElementById('location-select');
  locationSelect.innerHTML = '<option value="">Select destination</option>';
  
  console.log('Populating destination dropdown with', poiArray.length, 'POIs');
  
  poiArray.forEach(poi => {
    console.log('Processing POI for dropdown:', poi.name, 'category:', poi.category, 'id:', poi.id);
    
    // Skip entrance POIs as they are the starting point
    if (poi.category !== 'Entrance') {
      const option = document.createElement('option');
      option.value = poi.id;
      option.textContent = `${poi.name} ${poi.category ? '— ' + poi.category : ''}`;
      locationSelect.appendChild(option);
      console.log('Added to dropdown:', poi.name);
    } else {
      console.log('Skipped entrance POI:', poi.name);
    }
  });
  
  console.log('Destination dropdown populated with', locationSelect.children.length - 1, 'options');
}

// Set entrance as current location automatically (fixed since kiosk is at entrance)
function setEntranceAsCurrentLocation(poiArray = getActivePois()) {
  if (!Array.isArray(poiArray) || poiArray.length === 0) {
    console.warn('No POIs available to determine entrance location.');
    return;
  }
  // Look for entrance POI or wayfinding tube at entrance
  const entrance = poiArray.find(poi => 
    poi.category === 'Entrance' || 
    poi.name.toLowerCase().includes('entrance') ||
    (poi.meta && poi.meta.entrance) ||
    (poi.meta && poi.meta.user_start_location)
  );
  
  if (entrance) {
    currentLocation = entrance;
    // Mark as fixed location since kiosk is physically at entrance
    currentLocation.fixed = true;
    currentLocation.kioskLocation = true;
    console.log('User fixed at entrance location:', entrance.name, 'at coordinates (', entrance.x, ',', entrance.y, ')');
    updateCurrentLocationDisplay();
  } else {
    // Fallback: use first POI or create default entrance
    console.warn('No entrance found, using first POI as current location');
    if (poiArray.length > 0) {
      currentLocation = poiArray[0];
      currentLocation.fixed = true;
      currentLocation.kioskLocation = true;
      updateCurrentLocationDisplay();
    }
  }
}

// Update current location display in UI
function updateCurrentLocationDisplay() {
  if (!currentLocation) return;
  
  // Update current location indicator in UI
  const currentLocationElement = document.getElementById('current-location');
  if (currentLocationElement) {
    if (currentLocation.fixed && currentLocation.kioskLocation) {
      currentLocationElement.innerHTML = `
        <strong> Current Location: ${currentLocation.name}</strong>
        <br><small style="color: #00ff00;">Fixed at Kiosk Location (Entrance)</small>
      `;
    } else {
      currentLocationElement.innerHTML = `Current Location: ${currentLocation.name}`;
    }
  }
  
  // Update route calculation if destination is set
  if (currentDestination) {
    calculateRoute();
  }
}

// Loading and error state management
function showLoadingState(message = 'Loading...') {
  const container = document.getElementById('canvas-container');
  if (!container) return;
  
  let loadingEl = document.getElementById('loading-overlay');
  if (!loadingEl) {
    loadingEl = document.createElement('div');
    loadingEl.id = 'loading-overlay';
    loadingEl.style.cssText = `
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.7);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      color: white;
      font-size: 18px;
      font-family: Arial, sans-serif;
    `;
    container.appendChild(loadingEl);
  }
  
  loadingEl.innerHTML = `
    <div style="text-align: center;">
      
      <div>${message}</div>
    </div>
  `;
  loadingEl.style.display = 'flex';
}

function hideLoadingState() {
  const loadingEl = document.getElementById('loading-overlay');
  if (loadingEl) {
    loadingEl.style.display = 'none';
  }
}

function showErrorState(message) {
  const container = document.getElementById('canvas-container');
  if (!container) return;
  
  let errorEl = document.getElementById('error-overlay');
  if (!errorEl) {
    errorEl = document.createElement('div');
    errorEl.id = 'error-overlay';
    errorEl.style.cssText = `
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(220, 53, 69, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      color: white;
      font-size: 16px;
      font-family: Arial, sans-serif;
    `;
    container.appendChild(errorEl);
  }
  
  errorEl.innerHTML = `
    <div style="text-align: center; max-width: 80%;">
      <div style="margin-bottom: 20px; font-size: 48px;"></div>
      <div style="margin-bottom: 20px;">${message}</div>
      <button onclick="hideErrorState(); loadFloors();" style="
        padding: 10px 20px;
        background: white;
        color: #dc3545;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
      ">Retry</button>
    </div>
  `;
  errorEl.style.display = 'flex';
}

function hideErrorState() {
  const errorEl = document.getElementById('error-overlay');
  if (errorEl) {
    errorEl.style.display = 'none';
  }
}

// Performance monitoring functions
window.getPerformanceStats = function() {
  const stats = PERFORMANCE_MONITOR.getMemoryStats();
  const loadTime = Math.round(performance.now() - PERFORMANCE_MONITOR.startTime);
  
  return {
    loadTime: loadTime + 'ms',
    memory: stats,
    frameCount: PERFORMANCE_MONITOR.frameCount,
    fps: Math.round(PERFORMANCE_MONITOR.frameCount / (loadTime / 1000))
  };
};

// Memory cleanup on page unload
window.addEventListener('beforeunload', function() {
  MEMORY_MANAGER.clearScene();
  if (renderer) {
    renderer.dispose();
  }
});

// Mouse interactions for pan/zoom
function onMouseDown(e){
  if (!renderer) return;
  if (e.button === 0) { // left
    isPanning = true;
    lastPan.x = e.clientX;
    lastPan.y = e.clientY;
  }
}

function onMouseMove(e){
  if (!isPanning || !camera || !renderer) return;
  const dx = e.clientX - lastPan.x;
  const dy = e.clientY - lastPan.y;
  lastPan.x = e.clientX;
  lastPan.y = e.clientY;
  // Convert screen delta to world units based on zoom
  const scale = 1 / (camera.zoom || 1);
  camera.position.x -= dx * scale;
  camera.position.y += dy * scale;
}

function onMouseUp(){
  isPanning = false;
}

function onWheel(e){
  if (!camera) return;
  e.preventDefault();
  const delta = Math.sign(e.deltaY);
  const factor = 1 + (0.1 * Math.abs(delta));
  if (delta > 0) camera.zoom = Math.max(minZoom, camera.zoom / factor);
  else camera.zoom = Math.min(maxZoom, camera.zoom * factor);
  camera.updateProjectionMatrix();
}
