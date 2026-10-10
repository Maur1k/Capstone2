# 🎯 GLB Wayfinding Tube Integration Guide

This guide shows you how to use wayfinding tubes with your GLB 3D models in the 3D Map Kiosk.

## 📋 Prerequisites

1. **GLB Model**: Your 3D model file (e.g., `map.glb`)
2. **Floor Data**: Floor information with width/height dimensions
3. **POI Data**: Points of interest with coordinates

## 🚀 Quick Start

### Step 1: Load Your GLB Model

Your GLB model should be placed in `/kiosk/assets/map.glb` or specify the path in your floor metadata:

```json
{
  "meta": {
    "glb_path": "/kiosk/assets/your-model.glb"
  }
}
```

### Step 2: Create Wayfinding Tube POI

Use the API to create a wayfinding tube POI:

```bash
# Create a wayfinding tube at coordinates (600, 400)
curl -X POST http://127.0.0.1:8000/api/poi \
  -H "Content-Type: application/json" \
  -d '{
    "floor_id": 1,
    "name": "Main Wayfinding Point",
    "category": "Navigation",
    "x": 600,
    "y": 400,
    "meta": {
      "description": "Interactive wayfinding tube for main navigation",
      "geometry_type": "tube",
      "tube_properties": {
        "radius": 25,
        "height": 120,
        "segments": 16,
        "color": "#00ff00",
        "opacity": 0.8,
        "animated": true,
        "pulse_speed": 2.0,
        "material": "emissive"
      },
      "interactive": true,
      "wayfinding_features": [
        "direction_arrows",
        "distance_display",
        "route_highlighting",
        "voice_guidance"
      ],
      "modelPosition": {
        "x": 0,
        "y": 0.5,
        "z": 0
      }
    }
  }'
```

### Step 3: Position on GLB Model

The wayfinding tube will automatically position itself on your GLB model using:

1. **Image Coordinates**: `x`, `y` from the POI data
2. **Model Position**: `meta.modelPosition` for precise 3D placement
3. **Ground Level**: Automatically calculated from model bounds

## 🎨 Customization Options

### Tube Properties

```javascript
"tube_properties": {
  "radius": 20,           // Tube radius in units
  "height": 100,          // Tube height in units
  "segments": 16,         // Number of segments (smoothness)
  "color": "#00ff00",     // Hex color code
  "opacity": 0.8,         // Transparency (0-1)
  "animated": true,       // Enable pulse animation
  "pulse_speed": 2.0,     // Animation speed
  "material": "emissive"  // Material type
}
```

### Wayfinding Features

```javascript
"wayfinding_features": [
  "direction_arrows",     // Cardinal direction indicators
  "distance_display",     // Distance information label
  "route_highlighting",   // Route visualization
  "voice_guidance",       // Audio assistance
  "touch_interaction"     // Touch/click interaction
]
```

## 🔧 Advanced Positioning

### Using the GLB Positioning Helper

Include the positioning helper script in your HTML:

```html
<script src="/kiosk/glb-positioning-helper.js"></script>
```

Then use it to position wayfinding tubes precisely:

```javascript
// Initialize helper with your model info
const helper = new GLBPositioningHelper();
helper.initialize({
  bounds: { size: { x: 800, y: 100, z: 600 } },
  scale: 1,
  groundLevel: 0.5,
  imageSize: { width: 1200, height: 800 },
  modelSize: { x: 800, z: 600 }
});

// Create wayfinding tube at specific location
const wayfindingTube = helper.createWayfindingTubePOI(
  'Entrance Wayfinding',
  600, 400,    // Image coordinates
  1200, 800,   // Image dimensions
  { 
    color: '#ff0000', 
    radius: 25,
    height: 150 
  }
);
```

### Manual Model Position

For precise control, specify exact 3D coordinates:

```json
"modelPosition": {
  "x": 50.0,    // X position in model space
  "y": 0.5,     // Y position (ground level + offset)
  "z": -30.0    // Z position in model space
}
```

## 🎮 Interactive Features

### Touch/Click Interaction

Wayfinding tubes support interaction:

```javascript
// Click handler for wayfinding tubes
function onPointerDown(event) {
  const intersects = getIntersections(event);
  intersects.forEach(intersect => {
    if (intersect.object.userData.wayfinding) {
      const poi = intersect.object.userData.poi;
      console.log('Wayfinding tube clicked:', poi.name);
      
      // Handle wayfinding interaction
      handleWayfindingInteraction(poi);
    }
  });
}
```

### Animation Control

Tubes automatically animate with pulse effects:

```javascript
// Custom animation properties
"tube_properties": {
  "animated": true,
  "pulse_speed": 1.5,    // Slower pulse
  "pulse_amplitude": 0.2 // Larger pulse effect
}
```

## 📍 Multiple Wayfinding Points

Create multiple wayfinding tubes for different areas:

```javascript
const wayfindingPoints = [
  {
    name: "Main Entrance",
    position: { x: 100, y: 200 },
    color: "#00ff00"
  },
  {
    name: "Information Desk", 
    position: { x: 400, y: 300 },
    color: "#0066ff"
  },
  {
    name: "Elevator Bank",
    position: { x: 700, y: 500 },
    color: "#ff6600"
  }
];

wayfindingPoints.forEach(point => {
  const poi = helper.createWayfindingTubePOI(
    point.name,
    point.position.x,
    point.position.y,
    1200, 800,
    { color: point.color }
  );
  
  // Add to your POI system
  addWayfindingTube(poi);
});
```

## 🎯 Best Practices

### 1. **Strategic Placement**
- Place wayfinding tubes at key decision points
- Ensure visibility from multiple angles
- Consider user flow patterns

### 2. **Visual Design**
- Use consistent colors for similar functions
- Ensure adequate contrast with background
- Test visibility in different lighting conditions

### 3. **Performance**
- Limit number of animated tubes (max 10-15)
- Use appropriate segment counts (8-16)
- Monitor frame rate with performance tools

### 4. **Accessibility**
- Include audio guidance features
- Provide clear visual indicators
- Ensure wheelchair accessibility

## 🔍 Troubleshooting

### Tube Not Visible
- Check `modelPosition` coordinates
- Verify `groundLevel` calculation
- Ensure material opacity > 0

### Animation Issues
- Confirm `animated: true` in properties
- Check `pulse_speed` value (1.0-5.0 recommended)
- Verify animation loop is running

### Positioning Problems
- Use GLB Positioning Helper for accurate conversion
- Check image vs model coordinate systems
- Verify model bounds and scale

## 📊 Example Implementation

See the complete example in `/kiosk/test-tube.html` for a working demonstration of wayfinding tube integration with GLB models.

## 🚀 Next Steps

1. **Test with Your GLB**: Load your model and test wayfinding tube positioning
2. **Customize Appearance**: Adjust colors, sizes, and animations
3. **Add Interactions**: Implement touch/click handlers for user interaction
4. **Optimize Performance**: Monitor and adjust for smooth rendering

---

**Need Help?** Check the browser console for detailed logging of wayfinding tube creation and positioning.
