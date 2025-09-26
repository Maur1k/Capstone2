# CSS Organization Guide

## 📁 CSS Files Overview

This directory contains all stylesheets for the Click2Serve application, organized by functionality and usage.

### 🎯 **Core CSS Files**

#### **admin.css**
- **Purpose**: Admin dashboard and management interface styling
- **Used by**: All admin pages, officer dashboard, activity logs
- **Features**: 
  - Responsive design with proper scrolling
  - Modern UI components (cards, buttons, tables)
  - Clean, professional appearance
  - Mobile-friendly layout
- **Key Classes**: `.card`, `.btn`, `.table`, `.grid`, `.stats-grid`

#### **forms.css**
- **Purpose**: Form styling for login and setup pages
- **Used by**: Admin login, officer login, admin setup, form pages
- **Features**:
  - Consistent with kiosk interface design
  - Password toggle functionality
  - Kiosk-friendly form layouts
- **Key Classes**: `.form-panel`, `.form-title`, `.form-group`

#### **services.css**
- **Purpose**: Main kiosk interface styling
- **Used by**: Service selection pages, civil services, main kiosk
- **Features**:
  - Fixed viewport design for kiosk
  - Service tile layouts
  - Kiosk-optimized scrolling
- **Note**: Has `overflow:hidden` on body for kiosk design

### 🔧 **Specialized CSS Files**

#### **civil.css**
- **Purpose**: Civil service specific styling
- **Used by**: Civil service forms and pages
- **Features**: Form-specific styling for civil services

#### **tracker.css**
- **Purpose**: Request tracking interface
- **Used by**: Tracking pages and status displays
- **Features**: Status indicators and tracking layouts

#### **kiosk.css**
- **Purpose**: Kiosk-specific interface elements
- **Used by**: Kiosk mode pages
- **Features**: Touch-friendly interface, kiosk optimizations

#### **chatbot.css**
- **Purpose**: Chatbot interface styling
- **Used by**: Chatbot modal and interface
- **Features**: Chat bubbles, modal styling

## 🎨 **Design System**

### **Color Palette**
- **Primary**: `#0ea5e9` (Blue)
- **Secondary**: `#6b7280` (Gray)
- **Success**: `#10b981` (Green)
- **Warning**: `#f59e0b` (Orange)
- **Error**: `#ef4444` (Red)

### **Typography**
- **Font Family**: Inter, system fonts
- **Sizes**: 12px, 14px, 16px, 18px, 20px, 24px, 32px

### **Spacing**
- **Margins**: 4px, 8px, 12px, 16px, 20px, 24px
- **Padding**: 4px, 8px, 12px, 16px, 20px, 24px

### **Shadows**
- **Small**: `0 1px 2px 0 rgba(0, 0, 0, 0.05)`
- **Medium**: `0 4px 6px -1px rgba(0, 0, 0, 0.1)`
- **Large**: `0 10px 15px -3px rgba(0, 0, 0, 0.1)`

## 📱 **Responsive Design**

### **Breakpoints**
- **Mobile**: < 480px
- **Tablet**: 480px - 768px
- **Desktop**: > 768px

### **Grid System**
- **1 Column**: Mobile
- **2 Columns**: Tablet
- **3-4 Columns**: Desktop

## 🔧 **Usage Guidelines**

### **When to Use Each CSS File**

1. **Admin Pages**: Use `admin.css`
   - Dashboard, officers, services, activity logs
   - Any admin management interface

2. **Forms**: Use `forms.css`
   - Login pages, setup pages, form submissions
   - Any form-heavy interface

3. **Kiosk Interface**: Use `services.css`
   - Main service selection, civil services
   - Public-facing kiosk pages

4. **Specialized Pages**: Use appropriate specialized CSS
   - Civil services: `civil.css`
   - Tracking: `tracker.css`
   - Kiosk mode: `kiosk.css`
   - Chatbot: `chatbot.css`

### **Best Practices**

1. **Consistency**: Use the same CSS file for similar page types
2. **Performance**: Only load necessary CSS files
3. **Maintenance**: Update CSS files in their respective directories
4. **Testing**: Test all breakpoints when making changes

## 🚀 **Performance Notes**

- All CSS files are minified in production
- Critical CSS is inlined for above-the-fold content
- Non-critical CSS is loaded asynchronously
- CSS files are cached by browsers

---

**Last Updated**: $(date)
**Version**: 1.0
