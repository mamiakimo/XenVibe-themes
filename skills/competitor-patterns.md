# Competitor Style Analysis & Patterns

## Overview

Analysis of major XenForo theme frameworks to learn best practices.

| Framework | Developer | Key Features |
|-----------|-----------|--------------|
| UI.X 2 | ThemeHouse | Full PAGE_CONTAINER override, 200+ properties |
| XenBase/Apex | PixelExit | 366 properties, parent/child inheritance |
| FluentXen | DohTheme | Template modifications, minimal overrides |
| MaterialXen | DohTheme | Material Design implementation |

---

## UI.X 2 Dark (ThemeHouse)

### Architecture
- **Full template overrides** - Completely replaces PAGE_CONTAINER
- **Property-driven** - Over 200 style properties
- **Component macros** - Reusable template macros
- **JavaScript modules** - Custom JS for interactions

### Property Naming Convention
```
uix_{component}_{setting}
```
Examples:
- `uix_navigationType` - Navigation style type
- `uix_collapsibleSidebar` - Sidebar collapse feature
- `uix_pageWidthToggle` - Page width toggle feature
- `uix_searchIconBehavior` - Search behavior setting

### Template Patterns
```html
<!-- Dynamic class building -->
<xf:set var="$uix_htmlClasses"></xf:set>
<xf:if is="property('uix_visitorTabsMobile') == 'tabbar'">
    <xf:set var="$uix_htmlClasses">{{$uix_htmlClasses}} uix_hasBottomTabs</xf:set>
</xf:if>

<!-- Component macros -->
<xf:macro id="uix_sidebarTrigger__component" arg-location="" arg-content="">
    <xf:if is="({$location} == property('uix_sidebarTriggerPosition'))">
        {$content}
    </xf:if>
</xf:macro>
```

### Key Learnings
1. Build HTML classes dynamically based on properties
2. Use macros for reusable components
3. Property-based component visibility
4. Sidebar toggle with cookies/localStorage

---

## XenBase / Apex (PixelExit)

### Architecture
- **Parent/Child pattern** - XenBase as parent, Apex as child
- **366 style properties** - Extensive customization
- **Minimal template overrides** - Mostly CSS-based
- **Icon options** - Choose between text labels or icons

### Property Naming Convention
```
xb{Component}{Setting}
```
Examples:
- `xbMessageAvatarSize` - Avatar size in messages
- `xbMessageUserIcons` - Show icons instead of labels
- `messageUserElements` - Array of elements to show

### Template Patterns
```html
<!-- Conditional rendering based on properties -->
<xf:if is="property('xbMessageUserIcons')">
    <dt><xf:fa icon="fa-calendar fa-fw" data-xf-init="tooltip" title="{{ phrase('joined') }}" /></dt>
<xf:else />
    <dt>{{ phrase('joined') }}</dt>
</xf:if>

<!-- Property as attribute value -->
<xf:avatar user="$user" size="{{ property('xbMessageAvatarSize') }}" />
```

### Key Learnings
1. Parent style should NOT be edited
2. Use child styles for customization
3. Properties can control both CSS and template behavior
4. Array properties for multiple options

---

## FluentXen (DohTheme)

### Architecture
- **Addon + Style** - Separate addon for functionality
- **Template modifications** - Minimal template changes
- **Node grid style** - Child style for grid layouts
- **Modular approach** - Separate styles for features

### File Structure
```
01_Install_addon/
    DohTheme/Core/
        _data/
            style_properties.xml
            style_property_groups.xml
            template_modifications.xml
            phrases.xml
02_Import_Style/
    01_Parent_theme/style.xml
    02_Node_style/style-nodeGrid.xml
```

### Template Modification Approach
Instead of full template overrides, uses targeted modifications:
- Find specific HTML in templates
- Replace or inject new content
- Maintains compatibility with XF updates

### Key Learnings
1. Template mods are more upgrade-safe
2. Separate styles for different layouts (grid, list)
3. Addon provides core functionality
4. Style provides visual changes

---

## Common Patterns Across All Frameworks

### 1. Property Groups Organization
```xml
<!-- Group related properties -->
<group group_name="xvHeader" title="Header" display_order="10" />
<group group_name="xvSidebar" title="Sidebar" display_order="20" />
<group group_name="xvContent" title="Content" display_order="30" />
```

### 2. Conditional Classes
```html
<!-- Add classes based on state -->
<html class="
    {{ $xf.visitor.user_id ? 'is-logged-in' : 'is-guest' }}
    {{ property('darkMode') ? 'is-dark' : 'is-light' }}
    template-{{ $contentTemplate }}
">
```

### 3. State Management
```javascript
// Save preferences
localStorage.setItem('xv_layout', 'grid');
document.cookie = 'xv_sidebar=collapsed; path=/';

// Apply on load
if (localStorage.getItem('xv_layout') === 'list') {
    document.documentElement.classList.add('xv-layout-list');
}
```

### 4. RTL Support
```less
// Use logical properties when possible
margin-inline-start: 1rem;  // instead of margin-left
padding-inline-end: 1rem;   // instead of padding-right

// Or use RTL-aware variables
.element {
    float: @xf-floatLeft;  // Adapts to RTL
}
```

### 5. Component Visibility
```html
<!-- Property-controlled visibility -->
<xf:if is="property('showSidebar')">
    <aside class="sidebar">...</aside>
</xf:if>

<!-- Role-based visibility -->
<xf:if is="$xf.visitor.is_admin">
    <div class="admin-tools">...</div>
</xf:if>
```

---

## Recommended Approach for XenVibe

### Do
- Use template modifications over full overrides
- Organize properties into logical groups
- Support both icons and text labels
- Build CSS classes dynamically
- Save user preferences in localStorage
- Test in both RTL and LTR modes

### Don't
- Hardcode colors (use XF variables)
- Override templates unnecessarily
- Create properties for one-time use
- Ignore mobile responsiveness
- Forget dark/light mode support

### Property Naming
```
xv_{component}_{setting}
```
Examples:
- `xv_header_sticky` - Sticky header toggle
- `xv_sidebar_collapsed` - Default sidebar state
- `xv_card_radius` - Card border radius
- `xv_layout_default` - Default layout (grid/list)

### CSS Class Naming
```
.xv-{component}
.xv-{component}--{modifier}
.xv-{component}__{element}
```
Examples:
- `.xv-card` - Card component
- `.xv-card--featured` - Featured card modifier
- `.xv-card__header` - Card header element

---

## Feature Comparison

| Feature | UI.X | XenBase | FluentXen | XenVibe |
|---------|------|---------|-----------|---------|
| Template Override | Full | Partial | Mods only | Mods + Partial |
| Style Properties | 200+ | 366 | 50+ | 20+ |
| RTL Support | Yes | Yes | Yes | Yes |
| Dark Mode | Yes | Yes | Yes | Yes |
| Sidebar Toggle | Yes | No | No | Yes |
| Layout Toggle | No | No | Yes | Yes |
| Grid View | No | No | Yes | Yes |

---

## Sources

- UI.X 2 Dark: `/Other/UI.X 2 Dark/`
- XenBase: `/Other/XenBase - PixelExit-2.3.4/`
- Apex: `/Other/Apex - PixelExit.com 2.3.4/`
- FluentXen: `/Other/fluentxen_v237/`
- MaterialXen: `/Other/materialxen_v237/`

---

## Stitch - NanoPro (Dark)

### Overview
A minimal, dark-themed styling focus that simplifies the XenForo interface.

### Key Features
- **Minimal Numbering**: simplified pagination or item counting.
- **Dark Mode Optimization**: Deep integration with dark palettes.
- **Profile Redesign**: "NanoPro" profile layout variations (Forum Dark, Dashboard Dark).

### CSS Strategy
- Likely uses high-contrast borders and subtle gradients.
- Focus on spacing and typography over heavy graphical elements.

---

## Stitch - Cards

### Overview
A fully grid/card-based layout system that transforms standard lists into visual cards.

### Key Features
- **Floating Number Cards**: Unique styling for iteration or counts.
- **Card-based Forums**: Transforms node list into a grid of cards.
- **Card-based Threads**: Thread list becomes a masonry or grid layout.
- **Interactive Elements**: "Featured" and "New" badges integrated into card headers.

