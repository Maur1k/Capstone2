/**
 * GLB Positioning Helper for Wayfinding Tubes
 * 
 * This script helps you position wayfinding tubes precisely on your GLB model.
 * It provides utilities to convert between image coordinates and 3D model coordinates.
 */

class GLBPositioningHelper {
  constructor() {
    this.modelBounds = null;
    this.modelScale = 1;
    this.groundLevel = 0;
    this.imageToModelRatio = { x: 1, y: 1 };
  }

  /**
   * Initialize the positioning helper with model information
   * @param {Object} modelInfo - Information about the loaded GLB model
   */
  initialize(modelInfo) {
    this.modelBounds = modelInfo.bounds;
    this.modelScale = modelInfo.scale || 1;
    this.groundLevel = modelInfo.groundLevel || 0;
    
    // Calculate conversion ratios from image coordinates to model coordinates
    if (modelInfo.imageSize && modelInfo.modelSize) {
      this.imageToModelRatio = {
        x: modelInfo.modelSize.x / modelInfo.imageSize.width,
        y: modelInfo.modelSize.z / modelInfo.imageSize.height // Z for depth in 3D
      };
    }
    
    console.log('GLB Positioning Helper initialized:', {
      bounds: this.modelBounds,
      scale: this.modelScale,
      groundLevel: this.groundLevel,
      ratios: this.imageToModelRatio
    });
  }

  /**
   * Convert image coordinates to 3D model coordinates
   * @param {number} imageX - X coordinate in image space
   * @param {number} imageY - Y coordinate in image space
   * @param {number} imageWidth - Width of the source image
   * @param {number} imageHeight - Height of the source image
   * @returns {Object} 3D coordinates {x, y, z}
   */
  imageToModelCoords(imageX, imageY, imageWidth, imageHeight) {
    // Normalize image coordinates (0-1)
    const normalizedX = imageX / imageWidth;
    const normalizedY = imageY / imageHeight;
    
    // Convert to model space
    const modelX = (normalizedX - 0.5) * this.modelBounds.size.x;
    const modelZ = (0.5 - normalizedY) * this.modelBounds.size.z; // Invert Y for 3D
    const modelY = this.groundLevel;
    
    return {
      x: modelX,
      y: modelY,
      z: modelZ
    };
  }

  /**
   * Convert 3D model coordinates to image coordinates
   * @param {number} modelX - X coordinate in model space
   * @param {number} modelZ - Z coordinate in model space (depth)
   * @param {number} imageWidth - Width of the target image
   * @param {number} imageHeight - Height of the target image
   * @returns {Object} Image coordinates {x, y}
   */
  modelToImageCoords(modelX, modelZ, imageWidth, imageHeight) {
    // Normalize model coordinates
    const normalizedX = (modelX / this.modelBounds.size.x) + 0.5;
    const normalizedZ = 0.5 - (modelZ / this.modelBounds.size.z);
    
    // Convert to image space
    const imageX = normalizedX * imageWidth;
    const imageY = normalizedZ * imageHeight;
    
    return {
      x: imageX,
      y: imageY
    };
  }

  /**
   * Generate modelPosition metadata for a POI
   * @param {Object} poi - POI object with x, y coordinates
   * @param {number} imageWidth - Width of the floor image
   * @param {number} imageHeight - Height of the floor image
   * @returns {Object} modelPosition metadata
   */
  generateModelPosition(poi, imageWidth, imageHeight) {
    const modelCoords = this.imageToModelCoords(poi.x, poi.y, imageWidth, imageHeight);
    
    return {
      x: modelCoords.x,
      y: modelCoords.y,
      z: modelCoords.z,
      generated: true,
      timestamp: new Date().toISOString()
    };
  }

  /**
   * Update POI with modelPosition metadata
   * @param {Object} poi - POI object to update
   * @param {number} imageWidth - Width of the floor image
   * @param {number} imageHeight - Height of the floor image
   * @returns {Object} Updated POI with modelPosition
   */
  updatePOIWithModelPosition(poi, imageWidth, imageHeight) {
    const modelPosition = this.generateModelPosition(poi, imageWidth, imageHeight);
    
    return {
      ...poi,
      meta: {
        ...poi.meta,
        modelPosition: modelPosition
      }
    };
  }

  /**
   * Create a wayfinding tube POI positioned on the GLB model
   * @param {string} name - Name of the wayfinding point
   * @param {number} imageX - X coordinate in image space
   * @param {number} imageY - Y coordinate in image space
   * @param {number} imageWidth - Width of the floor image
   * @param {number} imageHeight - Height of the floor image
   * @param {Object} tubeProps - Custom tube properties
   * @returns {Object} Complete POI object for wayfinding tube
   */
  createWayfindingTubePOI(name, imageX, imageY, imageWidth, imageHeight, tubeProps = {}) {
    const modelCoords = this.imageToModelCoords(imageX, imageY, imageWidth, imageHeight);
    
    const defaultTubeProps = {
      radius: 20,
      height: 100,
      segments: 16,
      color: '#00ff00',
      opacity: 0.8,
      animated: true,
      pulse_speed: 2.0,
      material: 'emissive'
    };

    return {
      name: name,
      category: 'Navigation',
      x: imageX,
      y: imageY,
      meta: {
        description: `Interactive wayfinding tube: ${name}`,
        geometry_type: 'tube',
        tube_properties: { ...defaultTubeProps, ...tubeProps },
        interactive: true,
        wayfinding_features: [
          'direction_arrows',
          'distance_display',
          'route_highlighting',
          'voice_guidance',
          'touch_interaction'
        ],
        modelPosition: {
          x: modelCoords.x,
          y: modelCoords.y,
          z: modelCoords.z,
          generated: true,
          timestamp: new Date().toISOString()
        },
        accessibility: {
          wheelchair_accessible: true,
          audio_guidance: true,
          visual_indicators: true
        }
      }
    };
  }

  /**
   * Batch update multiple POIs with model positions
   * @param {Array} pois - Array of POI objects
   * @param {number} imageWidth - Width of the floor image
   * @param {number} imageHeight - Height of the floor image
   * @returns {Array} Updated POIs with modelPosition metadata
   */
  batchUpdatePOIs(pois, imageWidth, imageHeight) {
    return pois.map(poi => this.updatePOIWithModelPosition(poi, imageWidth, imageHeight));
  }
}

// Export for use in main application
window.GLBPositioningHelper = GLBPositioningHelper;

// Example usage:
/*
// Initialize the helper after loading your GLB model
const helper = new GLBPositioningHelper();
helper.initialize({
  bounds: { size: { x: 800, y: 100, z: 600 } },
  scale: 1,
  groundLevel: 0.5,
  imageSize: { width: 1200, height: 800 },
  modelSize: { x: 800, z: 600 }
});

// Create a wayfinding tube at a specific location
const wayfindingTube = helper.createWayfindingTubePOI(
  'Main Entrance Wayfinding',
  600, 400, // Image coordinates
  1200, 800, // Image dimensions
  { color: '#ff0000', radius: 25 } // Custom properties
);

// Update existing POIs with model positions
const updatedPOIs = helper.batchUpdatePOIs(existingPOIs, 1200, 800);
*/
