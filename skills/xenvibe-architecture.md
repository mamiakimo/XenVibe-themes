# XenVibe Theme Architecture

## Overview
XenVibe is a complex XenForo 2.3+ theme framework that uses a modular LESS architecture and extensive template modifications to deliver a highly customizable, modern experience.

---

## File Structure

### Template Locations
`src/styles/[StyleName]/templates/public/`

| File | Purpose |
|------|---------|
| `PAGE_CONTAINER_zyzoom.html` | The custom master template for XenVibe. |
| `_xv_extra.less` | Main entry point for custom CSS. |
| `xv_*.less` | Modular CSS components (see below). |

### Modular CSS Components
XenVibe splits CSS into logical units prefixed with `xv_`:

- **Core**
  - `xv_variables.less`: Custom LESS variables (colors, sizes).
  - `xv_xenforo.less`: Overrides for default XenForo elements.
  - `xv_components.less`: Reusable UI components (buttons, badges).

- **Layouts**
  - `xv_cards.less`: The core Card system styles.
  - `xv_forum_list.less`: Homepage/Forum list specific styles.
  - `xv_thread_view.less`: Thread view styles.
  - `xv_member_cards.less`: Member profile cards.

---

## Key Architectural Patterns

### 1. `xv-` Prefixing
All custom classes and IDs should use the `xv-` prefix to avoid conflicts with XenForo core or third-party addons.
```html
<div class="xv-card">
  <div class="xv-card__header">...</div>
</div>
```

### 2. The "Staff Bar"
A dedicated top bar for moderators and admins, separate from the main navigation.
- **Location**: `PAGE_CONTAINER` > `.p-staffBar`
- **Features**: Approval queue, Reports, Moderator Tools menu.
- **Classes**: `p-staffBar`, `p-staffBar-link`, `badgeContainer`.

### 3. Badge Containers
A unified pattern for showing notification counts (alerts, inbox, reports).
```html
<a class="badgeContainer badgeContainer--visible {{ $count ? 'badgeContainer--highlighted' : '' }}" 
   data-badge="{$count}">
   Title
</a>
```
- **Visible**: Always shows the badge container space.
- **Highlighted**: Adds the red notification color when count > 0.

### 4. Off-Canvas Navigation
XenVibe uses a robust off-canvas menu for mobile and desktop "mega menu" interactions.
- **Trigger**: `data-xf-click="off-canvas"`
- **Target**: `#js-nav` or custom IDs like `#js-SideNavOcm`.
- **Builder**: Uses `data-ocm-builder` to dynamically clone content.

### 5. Style Variations
Full support for XenForo 2.3 Style Variations:
- **Meta Theme Color**: Dynamic based on variation.
- **Attributes**: `data-variation`, `data-color-scheme`.

---

## Development Workflow

1.  **Edit LESS**: modify `xv_*.less` files for style changes.
2.  **Edit Templates**: modify `PAGE_CONTAINER_zyzoom.html` or individual templates.
3.  **Upload**: Move files to `src/styles/...`.
4.  **Recompile**: XenForo automatically recompiles in Designer Mode.

## Best Practices

- **Avoid Hardcoding**: Always use `@xf-` variables or `xv_variables.less` values.
- **RTL First**: Ensure all flex/grid layouts work in RTL (use logical properties or standard flex alignments).
- **Mobile optimization**: Use `.p-navSticky` logic for sticky headers on mobile.

---

## Complete LESS Module Reference

| File | Purpose | Priority |
|------|---------|----------|
| `xv_variables.less` | Custom colors, sizing & reusable mixins | 1 - Load first |
| `xv_xenforo.less` | Core XF overrides (blocks, menus) | 2 |
| `xv_components.less` | Header, Sidebar, Footer | 3 |
| `xv_cards.less` | Base card system | 4 |
| `xv_forum_list.less` | Forum index cards | 5 |
| `xv_forum_view.less` | Thread list in forum | 5 |
| `xv_thread_view.less` | Post display | 5 |
| `xv_whats_new.less` | What's New pages | 5 |
| `xv_profile.less` | Member profile | 5 |
| `xv_members_list.less` | Members list page | 5 |
| `xv_member_cards.less` | Member card styling | 5 |
| `xv_fixes.less` | Bug fixes and patches | 6 - Load last |

---

## Debugging Tips

### Check if Designer Mode is Active
```php
// In src/config.php
if ($config['designer']['enabled']) {
    // Active - files load directly
}
```

### Verify LESS Compilation
If CSS isn't applying:
1. Check browser console for LESS errors
2. Verify file uploaded to correct path
3. Hard refresh: `Ctrl+Shift+R`

### Template Debug Mode
Add to `src/config.php`:
```php
$config['debug'] = true;
```
This shows template names in HTML comments.

