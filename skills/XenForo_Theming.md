# XenForo Theming Master Guide

## Introduction
This guide consolidates knowledge for developing advanced XenForo themes, specifically focusing on the XenVibe architecture and patterns derived from top competitors.

---

## 1. Core Architecture
- **Structure**: See `xenforo-structure.md` for standard XF file layout.
- **XenVibe Specifics**: See `xenvibe-architecture.md` for the `xv_` prefix system and custom `PAGE_CONTAINER`.
- **Variables**: See `xenforo-variables.md` for the extensive list of LESS variables.
- **Style Properties**: See `xenvibe-style-properties.md` for XenVibe's property usage and customization.

## 2. Common Patterns & Solutions
- **CSS Patterns**: See `css-patterns.md` for reusable snippets like Glassmorphism, Card Grids, and the Badge Container pattern.
- **Template Mods**: See `template-modifications.md` for how to inject code without replacing templates.
- **Addon Dev**: See `addon-development.md` for packaging themes as addons.

## 3. Competitor Analysis
See `competitor-patterns.md` for detailed breakdowns of:
- **UI.X**: Property-heavy, full overrides.
- **XenBase**: Parent/Child inheritance.
- **Fluent**: Template modification driven.
- **Stitch / NanoPro**: Minimal dark themes.
- **Stitch / Cards**: Grid-based layouts.

---

## 4. Essential Workflows

### Creating a New Component
1.  **Create File**: `src/styles/[Style]/templates/public/xv_new_component.less`
2.  **Import**: Add `@import 'xv_new_component.less';` to `_xv_extra.less`.
3.  **Use Variables**: Use `@xf-` variables for colors/spacing.
4.  **Prefix**: Use `.xv-newComponent` class names.

### Debugging
- **Designer Mode**: Ensure `$config['designer']['enabled'] = true;` is set.
- **CSS**: Use `outline: 1px solid red` to debug layouts.
- **Templates**: Use `<!-- template name -->` comments if needed (XF adds `data-template` attributes automatically).

---

## 5. XenVibe Specific Features
- **Staff Bar**: Top bar for mods (`.p-staffBar`).
- **Off-Canvas**: Unified mobile/desktop drawer.
- **Badge System**: `.badgeContainer` for consistent notifications.
- **Card System**: `.xv-card` based layouts for nodes, threads, and members.

---

## 6. Quick Reference

### Most Used Variables
```less
@xf-textColor         // Primary text
@xf-textColorMuted    // Secondary text
@xf-linkColor         // Links, buttons
@xf-contentBg         // Card backgrounds
@xf-pageBg            // Page background
@xf-borderColor       // Borders
```

### Common Selectors
```less
.template-forum_list   // Forum index page
.template-thread_view  // Thread/post page
.template-member_view  // Profile page
.node--forum           // Forum node
.structItem--thread    // Thread in list
.message--post         // Individual post
```

### Key Deploy Commands
```bash
# Upload CSS
scp file.less zyzoom:/opt/zyzoom-dev/.../templates/public/

# Rebuild addon
docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-addon:rebuild XenVibe/Core -n
```

---

## 7. Troubleshooting

| Problem | Solution |
|---------|---------|
| CSS not showing | Hard refresh (Ctrl+Shift+R), check LESS errors |
| Template mod not working | Verify find string exists exactly |
| Wrong colors in dark mode | Use `@xf-` variables, not hardcoded |
| Layout broken in RTL | Use `inset: 0; margin: auto;` instead of `transform` |
| Addon changes not visible | Run `xf-addon:rebuild`, clear cache |

