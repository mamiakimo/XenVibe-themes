# XenVibe Style Properties & Customization

## Overview
XenVibe is designed to be **lightweight and compatible**, meaning it relies heavily on XenForo's native Style Properties rather than creating a bloated custom system. This ensures addons and future XF updates work seamlessly.

---

## 1. Key Style Properties (Native)
XenVibe utilizes these core XenForo properties to control layout and theming. enhancing them via `PAGE_CONTAINER`.

### Basic Options
| Property | Use in XenVibe |
|----------|----------------|
| `publicLogoUrl` | Main site logo. |
| `publicLogoWidth` / `Height` | Enforced dimensions in header calculation. |
| `publicFaviconUrl` | Browser tab icon. |
| `publicIconUrl` | Homescreen/App icon. |

### Header & Navigation
| Property | Use in XenVibe |
|----------|----------------|
| `publicNavSticky` | Controls sticky functionality (`primary`, `all`, `none`). XenVibe adds mobile support. |
| `publicStaffBar` | *Implied*, handled via `xv_components.less` logic. |

### Page Features
| Property | Use in XenVibe |
|----------|----------------|
| `scrollJumpButtons` | Controls "Back to Top" / "Go to Bottom" buttons. |
| `sidebarLocation` | Standard sidebar placement. |

---

## 2. LESS Variable Mapping (`xv_variables.less`)
Instead of new Style Properties, XenVibe maps custom LESS variables to XF properties. This allows you to change the site's "vibe" by editing `xv_variables.less` or the XF Color Palette.

| XenVibe Variable | Mapped To (Default) | Purpose |
|------------------|---------------------|---------|
| `@xv-primary` | `@xf-linkColor` | Main interactive color. |
| `@xv-bg` | `@xf-pageBg` | Page background. |
| `@xv-surface` | `@xf-contentBg` | Card/Container background. |
| `@xv-text` | `@xf-textColor` | Primary body text. |
| `@xv-text-muted` | `@xf-textColorMuted` | Secondary text. |
| `@xv-border` | `@xf-borderColor` | Borders and dividers. |

---

## 3. Custom XenVibe "Properties"
While not database-stored properties (yet), these are controlled via `xv_home` template or specific LESS files:

- **Welcome Message**: Hardcoded in `xv_home` > `xv-welcome-title`
- **Navigation Chips**: Hardcoded in `xv_home` > `xv-nav-section`
- **PWA Installation**: Uses `publicPwaInstallVideoUrl` (standard XF property).

---

## 4. How to Customize
1.  **Colors**: Go to **Appearance > Style Properties > Color Palette**. XenVibe adapts automatically.
2.  **Typography**: Go to **Typography**.
3.  **Layout**: Edit `xv_variables.less` for global sizing/radius changes that shouldn't be properties.

---

## 5. Color Palette Reference

XenForo's Color Palette (found in **Style Properties > Color Palette**) is XenVibe's foundation:

| Property | Default (Dark) | Usage |
|----------|----------------|-------|
| `paletteColor1` | Red | Attention, errors, alerts |
| `paletteColor2` | Green | Success, online status |
| `paletteColor3` | Blue | Links, primary actions |
| `paletteColor4` | Yellow/Orange | Warnings |
| `paletteColor5` | Neutral Dark | Headers, Staff Bar |
| `paletteNeutral1` | Light Gray | Subtle backgrounds |
| `paletteNeutral2` | Medium Gray | Secondary backgrounds |

### Accessing in LESS
```less
.error { color: @xf-paletteColor1; }
.success { color: @xf-paletteColor2; }
.primary { background: @xf-paletteColor3; }
```

---

## 6. Typography Properties

| Property | Usage |
|----------|-------|
| `fontFamily` | Main font stack |
| `fontSizeBase` | Body text size |
| `fontSizeSmall` | Secondary text |
| `fontWeightNormal` | Regular weight |
| `fontWeightBold` | Bold headings |

### Custom Fonts (XenVibe)
XenVibe uses Google Fonts loaded in `PAGE_CONTAINER`:
- **Plus Jakarta Sans** - Primary Latin font
- **Noto Sans Arabic** - Arabic support

