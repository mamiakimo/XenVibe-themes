# XenVibe Themes - XenForo Style Framework

> **Repository:** github.com/mamiakimo/XenVibe-themes
> **Last Updated:** January 28, 2026

Professional XenForo 2.3+ style framework converting Stitch HTML/CSS designs into XenForo styles.

---

## Repository Structure

```
XenVibe-themes/
├── CLAUDE.md           # This file - project instructions
├── skills/             # AI-readable documentation (11 files)
├── Stitch/             # 9 Style designs (HTML/CSS)
├── templates/          # LESS/CSS templates (active development)
├── addon/              # XenForo addon source files
└── Production/         # Export-ready packages
    └── XenVibe_Cards/V1.0/
        ├── 1_Addon/
        ├── 2_Style/
        └── 3_Documentation/
```

---

## Quick Reference

### Development Server
| Item | Value |
|------|-------|
| URL | https://dev.zyzoom.net/ |
| SSH Host | `zyzoom` |
| Basic Auth | `admin` / `Zyzoom@2026` |

### Deploy CSS
```bash
scp templates/*.less zyzoom:/opt/zyzoom-dev/public_html/forum/src/styles/xenvibe_cards/templates/public/
```

### Export Addon
```bash
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-addon:export XenVibe/Core"
```

### Export Style
Admin Panel > Appearance > Styles > XenVibe Cards > Export

---

## 9 Stitch Styles

| # | Style | Status |
|---|-------|--------|
| 1 | **Cards** | ✅ **COMPLETE - V1.0 Released** |
| 2 | Dashboard | Pending |
| 3 | DynamicList | Pending |
| 4 | **NanoPro** | 🔄 **In Progress** |
| 5 | PortalGrid | Pending |
| 6 | RichVisual | Pending |
| 7 | StreamFeed | Pending |
| 8 | TimelinePulse | Pending |
| 9 | ZenFocus | Pending |

---

## Cards Style V1.0 - COMPLETE

### All Pages Styled
- [x] What's New (Featured, Posts, Profile Posts)
- [x] Forum List (Grid + List views)
- [x] Forum View (Thread listing)
- [x] Thread View (Posts)
- [x] Members List (Filter buttons, cards)
- [x] Member Profile
- [x] Labels/Thread Prefixes styling

### Features
- Dual layout: Grid/List toggle (saved in localStorage)
- Collapsible left sidebar
- Widget position: `xv_left_sidebar`
- Dark/Light mode support
- RTL support
- Performance optimized (CLS < 0.15)

### Template Modifications (3)
| Key | Template | Purpose |
|-----|----------|---------|
| `xv_signature_first_post` | `post_macros` | Signature only for OP |
| `xv_member_filter_js` | `member_notable` | Filter buttons JS |
| `xv_member_card_redesign` | `member_notable` | Stitch-style cards |

---

## Export Workflow

### 1. Export Addon
```bash
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-addon:export XenVibe/Core"
scp -r zyzoom:/opt/zyzoom-dev/public_html/forum/src/addons/XenVibe/Core/* Production/ProductName/1_Addon/
```

### 2. Clean & Fix
- Remove `_output/`, `hashes.json`, empty folders
- **FIX:** Event listeners `active="0"` → `active="1"`
- Verify template_modifications.xml is complete

### 3. Export Style
From Admin Panel > Styles > Export

### 4. Documentation
- README.md, INSTALLATION.md, CUSTOMIZATION.md
- CHANGELOG.md, TRANSLATIONS.md

See `skills/export-workflow.md` for full guide.

---

## CSS Guidelines

### Use XenForo Variables (NO Hardcoded Colors!)
```less
// GOOD
color: @xf-textColor;
background: fade(@xf-pageBg, 50%);

// BAD
color: #ffffff;
```

### RTL-Compatible Centering
```less
// GOOD - works in RTL
position: absolute;
inset: 0;
margin: auto;

// BAD - breaks in RTL
transform: translate(-50%, -50%);
```

---

## Skills Documentation

| File | Content |
|------|---------|
| `xenforo-structure.md` | XenForo architecture |
| `css-patterns.md` | CSS solutions & patterns |
| `addon-development.md` | Addon XML files, phrases |
| `deployment.md` | Server setup, deploy |
| `export-workflow.md` | Export process |
| `template-modifications.md` | Template mod techniques |
| `xenvibe-style-properties.md` | Style properties |
| `xenforo-variables.md` | LESS variables |
| `nanopro-style.md` | **NEW** - NanoPro style guide |

**Update skills as we learn new patterns!**

---

## Naming Conventions

| Type | Prefix | Example |
|------|--------|---------|
| CSS Classes | `.xv-` | `.xv-thread-card` |
| Variables | `@xv-` | `@xv-card-bg` |
| Phrases | `xv_` | `xv_welcome_message` |
| Templates | `xv_` | `xv_home.html` |

---

## Scripts

### Style Colors Updater (`scripts/update_style_colors.php`)

Updates XenForo 2.3+ style properties with BOTH dark AND light mode values.

**Location:** `E:\Projects\XenVibe-themes\scripts\update_style_colors.php`

**Usage:**
```bash
# On server (inside PHP container)
docker exec zyzoom_dev_php php /var/www/html/scripts/update_style_colors.php [style_id]

# After running, MUST recompile:
docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-dev:recompile-style-properties
```

**XenForo 2.3 Style Variations Convention:**
- `default` = **Light Mode** (base style)
- `alternate` = **Dark Mode** (variation)

**How it works:**
1. Reads color values from `$lightColors` and `$darkColors` arrays
2. Creates JSON: `{"default": "#lightHex", "alternate": "#darkHex"}`
3. Updates `xf_style_property` table with `property_value` JSON
4. Sets `has_variations = 1` to enable variation support

**Configurable Colors:**
```php
$lightColors = [
    'pageBg'              => '#f6f6f8',
    'contentBg'           => '#ffffff',
    'contentHighlightBg'  => '#f1f5f9',
    'textColor'           => '#0f172a',
    'textColorMuted'      => '#64748b',
    'linkColor'           => '#135bec',
    'borderColor'         => '#e2e8f0',
    'textColorAttention'  => '#ef4444',
];

$darkColors = [
    'pageBg'              => '#0f1115',
    'contentBg'           => '#161b22',
    'contentHighlightBg'  => '#1c2128',
    'textColor'           => '#ffffff',
    'textColorMuted'      => '#848d97',
    'linkColor'           => '#135bec',
    'borderColor'         => '#30363d',
    'textColorAttention'  => '#ef4444',
];
```

### Debug Scripts (`scripts/check_*.php`)

| Script | Purpose |
|--------|---------|
| `check_css.php` | Check compiled style properties blob |
| `check_xf_vars.php` | Check XF repository properties |
| `check_less_vars.php` | Check LESS variables and property values |
| `check_blob.php` | Compare master vs style property values |

---

## Important Notes

1. **Designer Mode is ENABLED** - No recompile needed
2. **Always test dark AND light modes**
3. **Always test RTL layout**
4. **Event listeners export with active="0" - must fix!**
5. **Update skills/ docs when learning new patterns**
6. **After color updates, ALWAYS run recompile-style-properties**
