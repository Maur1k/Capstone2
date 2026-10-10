# Assets for Kiosk

- Place your GLB model(s) in this folder.
- Default GLB path used: `/kiosk/assets/map.glb`
- You can override at runtime (no DB change) by adding before `main.js`:

```html
<script>window.KIOSK_GLB_PATH = '/kiosk/assets/your-model.glb';</script>
```

- Sprite marker assets live here too (e.g., `marker.svg`).
