# XenForo CSS Patterns & Solutions

## Reusable Mixins (xv_variables.less)

XenVibe provides reusable LESS mixins to avoid code duplication:

### Grid Layouts

```less
// 3 → 2 → 1 columns (for collapsed sidebar)
.xv-grid-3col-responsive() {
  grid-template-columns: repeat(3, 1fr);

  @media (max-width: 1200px) {
    grid-template-columns: repeat(2, 1fr);
  }

  @media (max-width: 767px) {
    grid-template-columns: 1fr;
  }
}

// 2 → 1 columns (default with sidebar open)
.xv-grid-2col-responsive() {
  display: grid !important;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;

  @media (max-width: 767px) {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
```

### Card Hover Effect

```less
// Simple transform-only hover
.xv-card-hover() {
  transform: translateY(-2px);
}

// Usage:
.my-card {
  transition: transform 0.2s ease;

  &:hover {
    .xv-card-hover();
  }
}
```

---

## Common Layout Patterns

---

## Card Grid Layout

### Grid Container
```less
.block-body {
  display: grid !important;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.25rem;
  align-items: stretch !important;

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }

  // Ensure grid items stretch
  > .node {
    height: 100% !important;
  }
}
```

### Card with Footer at Bottom
```less
.card {
  position: relative !important;
  padding: 1.25rem;
  padding-bottom: 5rem !important; // Space for footer
  height: 100%;
  box-sizing: border-box !important;
}

.card-footer {
  position: absolute !important;
  bottom: 1.25rem !important;
  left: 1.25rem !important;
  right: 1.25rem !important;
}
```

---

## Glass Effect (Glassmorphism)

```less
.glass-element {
  background: fade(@xf-textColor, 8%);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid fade(@xf-textColor, 10%);
  border-radius: 0.75rem;
}
```

---

## Icon Centering (RTL-Compatible)

```less
.icon-container {
  position: relative;
  width: 2.5rem;
  height: 2.5rem;

  i, .fa--xf {
    position: absolute;
    inset: 0;
    margin: auto;
    width: fit-content;
    height: fit-content;
    line-height: 1;
  }
}
```

**Why not `transform: translate(-50%, -50%)`?**
- Breaks in RTL (right-to-left) layouts
- `inset: 0; margin: auto;` works in both LTR and RTL

---

## Float with Flexbox Parent

Float doesn't work inside flex containers. Solutions:

### Option 1: Use `display: block` on parent
```less
.parent {
  display: block !important;
  overflow: hidden; // Contain floats
}

.floated-icon {
  float: left;
  margin-right: 1rem;
}
```

### Option 2: Use Grid instead
```less
.parent {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 1rem;
}
```

---

## Responsive Columns

```less
// 3 columns when sidebar collapsed, 2 when open
.block-body {
  grid-template-columns: repeat(2, 1fr);
}

.sidebar-collapsed .block-body {
  grid-template-columns: repeat(3, 1fr);

  @media (max-width: 1200px) {
    grid-template-columns: repeat(2, 1fr);
  }

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
}
```

---

## Labels/Thread Prefixes Styling

### CSS-Only Softening
```less
.label,
.iconic-label,
.labelLink {
  position: relative;
  background-image: none !important;
  border: none !important;
  font-weight: 700 !important;
  font-size: 10px !important;
  padding: 0.125rem 0.5rem !important;
  border-radius: 0.25rem !important;
  letter-spacing: 0.05em !important;
  text-transform: uppercase !important;
  backdrop-filter: blur(4px);
  overflow: hidden;

  // Overlay to soften background
  &::before {
    content: '';
    position: absolute;
    inset: 0;
    background: @xf-pageBg;
    opacity: 0.2;
    pointer-events: none;
    border-radius: inherit;
  }
}
```

---

## Hide Element While Keeping Space

```less
// Hide visually but keep layout space
.hidden-keep-space {
  visibility: hidden;
}

// Hide completely
.hidden {
  display: none !important;
}
```

---

## Truncate Text with Ellipsis

```less
.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

// Multi-line truncate
.truncate-lines {
  display: -webkit-box;
  -webkit-line-clamp: 2; // Number of lines
  -webkit-box-orient: vertical;
  overflow: hidden;
}
```

---

## Smooth Hover Effects

```less
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px fade(@xf-textColor, 15%);
  }
}
```

---

## Dark/Light Mode Compatible Shadows

```less
// Instead of hardcoded shadow colors
box-shadow: 0 2px 8px fade(@xf-textColor, 10%);

// For hover states
&:hover {
  box-shadow: 0 4px 16px fade(@xf-textColor, 15%);
}
```

---

## Specificity Override Pattern

When XenForo styles override your custom styles:

```less
// Increase specificity
.template-forum_list .block--category .node--forum .element {
  property: value !important;
  }

// Or use multiple classes
.node.node--forum.node--unread {
  property: value;
}
```

---

## Debugging CSS Issues

### Test if selector matches
```less
.element {
  display: none !important; // If it hides, selector works
}
```

### Visual debugging
```less
.element {
  outline: 2px solid red !important;
  background: rgba(255, 0, 0, 0.1) !important;
}
```

---

## Common Pitfalls

### 1. Conflicting selectors
```less
// This selector...
.block-body > .node {
  display: block !important;
}

// ...overrides this if both match same element
.node--forum {
  display: flex !important;
}
```

### 2. Forgetting `!important` in overrides
XenForo uses `!important` frequently. Match it:
```less
.element {
  property: value !important;
}
```

### 3. Using hardcoded colors
```less
// BAD
color: #333;

// GOOD
color: @xf-textColor;
```

### 4. Forgetting `-webkit-` prefix for backdrop-filter
```less
backdrop-filter: blur(8px);
-webkit-backdrop-filter: blur(8px); // Required for Safari
```

---

## Badge Container Pattern

Used for notification badges (alerts, inbox, reports).

```less
.badgeContainer {
  position: relative;
  
  &::after {
    content: attr(data-badge);
    position: absolute;
    top: -8px;
    right: -8px;
    background: @xf-paletteColor1; // Red/Attention color
    color: white;
    font-size: 10px;
    padding: 2px 5px;
    border-radius: 10px;
    opacity: 0;
    transform: scale(0);
    transition: all 0.2s ease;
  }
}

.badgeContainer--highlighted::after {
  opacity: 1;
  transform: scale(1);
}
```

---

## Staff Bar Layout

Top bar for admins/moderators.

```less
.p-staffBar {
  background: @xf-paletteColor5; // Dark bar
  color: white;
  font-size: 13px;
  
  &-inner {
    padding: 5px 15px;
    display: flex;
    justify-content: space-between;
  }
  
  a {
    color: inherit;
    opacity: 0.8;
    &:hover { opacity: 1; }
  }
}
```

---

## Off-Canvas Navigation

Robust mobile/mega-menu drawer pattern.

```less
.offCanvasMenu {
  // Base styles provided by XF, enhanced by XenVibe
  &-content {
    background: @xf-contentBg;
  }
  
  &-header {
    background: @xf-majorHeadingBg;
    border-bottom: 1px solid @xf-borderColor;
  }
}
```

---

## Micro-Animations

Subtle animations for polished UX.

```less
// Fade-in on load
@keyframes xv-fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.xv-card {
  animation: xv-fadeIn 0.3s ease-out;
}

// Staggered children animation
.xv-cards-grid > * {
  animation: xv-fadeIn 0.3s ease-out backwards;
  
  @for $i from 1 through 12 {
    &:nth-child(@{i}) {
      animation-delay: (@i * 0.05s);
    }
  }
}
```

---

## Focus States (Accessibility)

Ensure keyboard navigation is visible.

```less
// Custom focus ring
:focus-visible {
  outline: 2px solid @xf-linkColor !important;
  outline-offset: 2px !important;
}

// Remove default outline for mouse users
:focus:not(:focus-visible) {
  outline: none;
}

// Card focus state
.xv-card:focus-within {
  box-shadow: 0 0 0 2px @xf-linkColor;
}
```

---

## Custom Scrollbar Styling

```less
// Thin scrollbar for sidebars and menus
.xv-sidebar {
  scrollbar-width: thin; // Firefox
  scrollbar-color: fade(@xf-textColor, 20%) transparent;
  
  // WebKit browsers
  &::-webkit-scrollbar {
    width: 6px;
  }
  
  &::-webkit-scrollbar-track {
    background: transparent;
  }
  
  &::-webkit-scrollbar-thumb {
    background: fade(@xf-textColor, 20%);
    border-radius: 3px;
    
    &:hover {
      background: fade(@xf-textColor, 40%);
    }
  }
}
```

---

## Skeleton Loading State

```less
// Placeholder shimmer effect
@keyframes xv-shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

.xv-skeleton {
  background: linear-gradient(
    90deg,
    fade(@xf-textColor, 5%) 25%,
    fade(@xf-textColor, 10%) 50%,
    fade(@xf-textColor, 5%) 75%
  );
  background-size: 200% 100%;
  animation: xv-shimmer 1.5s infinite;
  border-radius: 0.25rem;
}
```

