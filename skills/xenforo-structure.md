# XenForo 2.3+ Style Structure

## Overview

XenForo styles consist of templates, CSS (LESS), style properties, and phrases. Understanding the structure is essential for creating custom themes.

---

## Directory Structure

```
src/styles/{style_name}/
├── templates/
│   └── public/
│       ├── PAGE_CONTAINER.html      # Main page wrapper
│       ├── extra.less               # CSS entry point
│       ├── xv_*.less                # Modular CSS files
│       └── *.html                   # Template overrides
```

---

## Key Templates

### PAGE_CONTAINER.html
The main wrapper template containing:
- `<html>` and `<head>` structure
- Header/Navigation
- Sidebar
- Main content area
- Footer
- JavaScript includes

### Template Hierarchy
```
PAGE_CONTAINER.html
├── header (navigation, search, user menu)
├── body
│   ├── sidebar (widgets)
│   └── content (page-specific content)
└── footer
```

---

## CSS Architecture

### Entry Point: extra.less
```less
// Import modular files
@import "xv_variables.less";
@import "xv_components.less";
@import "xv_xenforo.less";
@import "xv_cards.less";
// ... more imports
```

### Modular CSS Files
| File | Purpose |
|------|---------|
| `xv_variables.less` | Custom variables and colors |
| `xv_components.less` | Header, Sidebar, Footer |
| `xv_xenforo.less` | XenForo core overrides |
| `xv_cards.less` | Card system styles |
| `xv_forum_list.less` | Forum categories page |
| `xv_forum_view.less` | Thread listing page |
| `xv_thread_view.less` | Post display page |
| `xv_whats_new.less` | What's New pages |
| `xv_profile.less` | Member profile page |
| `xv_members_list.less` | Members list page |

---

## XenForo LESS Variables

### Built-in Variables (Use These!)
```less
@xf-textColor           // Primary text color
@xf-textColorMuted      // Secondary/muted text
@xf-linkColor           // Links and active states
@xf-contentBg           // Content background
@xf-contentHighlightBg  // Hover states
@xf-pageBg              // Page background
@xf-borderColor         // Border color
```

### Custom Variables (Define in xv_variables.less)
```less
@xv-card-bg: @xf-contentBg;
@xv-card-radius: 1rem;
```

### IMPORTANT: No Hardcoded Colors!
```less
// BAD - breaks dark/light mode
color: #ffffff;
background: rgba(0, 0, 0, 0.5);

// GOOD - adapts to theme
color: @xf-textColor;
background: fade(@xf-pageBg, 50%);
```

---

## XenForo LESS Compiler Limitations

1. **No `body.classA.classB` selectors**
   ```less
   // BAD
   body.is-dark.has-sidebar { }

   // GOOD
   .is-dark.has-sidebar { }
   ```

2. **No `fade()` with `currentColor`**
   ```less
   // BAD
   background: fade(currentColor, 50%);

   // GOOD
   background: fade(@xf-textColor, 50%);
   ```

3. **Attribute selectors work**
   ```less
   // OK
   [style*="color"] { }
   ```

---

## Template Classes

### Page-specific Classes
```html
<html class="template-forum_list">  <!-- Forum list page -->
<html class="template-forum_view">  <!-- Thread list in forum -->
<html class="template-thread_view"> <!-- Thread/post view -->
<html class="template-whats_new">   <!-- What's New page -->
<html class="template-member_view"> <!-- Member profile -->
```

### Targeting Specific Pages
```less
.template-forum_list {
  // Styles only for forum list page
}

.template-thread_view {
  // Styles only for thread view
}
```

### Additional Template Classes
```html
<html class="template-register">      <!-- Registration page -->
<html class="template-login">         <!-- Login page -->
<html class="template-conversation_view"> <!-- Private message view -->
<html class="template-search_results">  <!-- Search results -->
<html class="template-account">       <!-- Account settings pages -->
<html class="template-help">          <!-- Help pages -->
```

### Body State Classes
```html
<body class="
  has-no-js            <!-- JS not yet initialized -->
  is-logged-in         <!-- User is logged in -->
  is-guest             <!-- User is guest -->
  has-sidebar          <!-- Page has sidebar -->
  sidebar-collapsed    <!-- Sidebar is collapsed (if feature enabled) -->
">

---

## Common HTML Structure

### Forum Node (Category/Forum)
```html
<div class="node node--forum node--id123">
  <div class="node-body">
    <span class="node-icon">...</span>
    <div class="node-main">
      <h3 class="node-title">...</h3>
      <div class="node-description">...</div>
      <div class="node-meta">
        <div class="node-statsMeta">...</div>
      </div>
    </div>
    <div class="node-stats">...</div>
  </div>
  <div class="node-extra">...</div>  <!-- Last post info -->
</div>
```

### Thread Item
```html
<div class="structItem structItem--thread">
  <div class="structItem-cell structItem-cell--icon">...</div>
  <div class="structItem-cell structItem-cell--main">
    <div class="structItem-title">...</div>
    <div class="structItem-minor">...</div>
  </div>
  <div class="structItem-cell structItem-cell--meta">...</div>
  <div class="structItem-cell structItem-cell--latest">...</div>
</div>
```

### Post
```html
<article class="message message--post">
  <div class="message-inner">
    <div class="message-cell message-cell--user">...</div>
    <div class="message-cell message-cell--main">
      <div class="message-content">...</div>
      <div class="message-footer">...</div>
    </div>
  </div>
</article>
```

---

## Designer Mode

When Designer Mode is enabled:
- No need to recompile templates
- Just upload files and refresh browser
- Changes appear immediately

### Enable Designer Mode
```php
// src/config.php
$config['designer']['enabled'] = true;
$config['designer']['basePath'] = 'src/styles';
```

---

## Deployment Commands

### Upload Single File
```bash
scp "local/path/file.less" server:/path/to/styles/templates/public/
```

### Rebuild Addon
```bash
php cmd.php xf-addon:rebuild AddonId -n
```

### Import Phrases
```bash
php cmd.php xf-dev:import-phrases --addon=AddonId
```

### Recompile Templates (if not using Designer Mode)
```bash
php cmd.php xf-dev:recompile-templates
```
