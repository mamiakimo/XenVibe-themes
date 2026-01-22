# XenVibe Themes - XenForo Style Framework

> **Repository:** github.com/mamiakimo/XenVibe-themes
> **Last Updated:** January 2026

Professional XenForo 2.3+ style framework converting Stitch HTML/CSS designs into XenForo styles.

---

## Repository Structure

```
XenVibe-themes/
├── CLAUDE.md           # This file - project instructions
├── skills/             # AI-readable documentation
│   ├── xenforo-structure.md    # XenForo architecture
│   ├── css-patterns.md         # CSS solutions & patterns
│   ├── addon-development.md    # Addon development guide
│   └── deployment.md           # Deployment procedures
├── Stitch/             # 9 Style designs (HTML/CSS)
│   ├── 01_Dashboard/
│   ├── 02_Cards/
│   ├── 03_DynamicList/
│   ├── 04_NanoPro/
│   ├── 05_PortalGrid/
│   ├── 06_RichVisual/
│   ├── 07_StreamFeed/
│   ├── 08_TimelinePulse/
│   └── 09_ZenFocus/
├── templates/          # LESS/CSS templates
└── addon/              # XenForo addon files
```

---

## Quick Reference

### Development Server
| Item | Value |
|------|-------|
| URL | https://dev.zyzoom.net/ |
| SSH Host | `zyzoom` |
| Basic Auth | `admin` / `Zyzoom@2026!` |

### Deploy CSS
```bash
scp templates/*.less zyzoom:/opt/zyzoom-dev/public_html/forum/src/styles/xenvibe_cards/templates/public/
```

### Rebuild Addon
```bash
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-addon:rebuild XenVibe/Core -n"
```

---

## 9 Stitch Styles

| # | Style | Status |
|---|-------|--------|
| 1 | Cards | **In Progress** |
| 2 | Dashboard | Pending |
| 3 | DynamicList | Pending |
| 4 | NanoPro | Pending |
| 5 | PortalGrid | Pending |
| 6 | RichVisual | Pending |
| 7 | StreamFeed | Pending |
| 8 | TimelinePulse | Pending |
| 9 | ZenFocus | Pending |

---

## CSS Guidelines

### Use XenForo Variables (NO Hardcoded Colors!)
```less
// GOOD
color: @xf-textColor;
background: fade(@xf-pageBg, 50%);

// BAD
color: #ffffff;
background: rgba(0, 0, 0, 0.5);
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

### Glass Effect
```less
background: fade(@xf-textColor, 8%);
backdrop-filter: blur(8px);
-webkit-backdrop-filter: blur(8px);
border: 1px solid fade(@xf-textColor, 10%);
```

---

## Skills Documentation

The `skills/` folder contains AI-readable documentation:

| File | Content |
|------|---------|
| `xenforo-structure.md` | XenForo templates, CSS, variables |
| `css-patterns.md` | Common CSS solutions & patterns |
| `addon-development.md` | Addon XML files, phrases, mods |
| `deployment.md` | Server setup, deploy commands |

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

## Current Work: Cards Style

### Completed Pages
- [x] What's New section
- [x] Forum List (Grid + List views)
- [x] Forum View (Thread listing)
- [x] Thread View (Posts)
- [x] Labels/Thread Prefixes styling

### In Progress
- [ ] Members List page
- [ ] Profile page

### Key CSS Files
| File | Purpose |
|------|---------|
| `extra.less` | Entry point |
| `xv_forum_list.less` | Forum categories |
| `xv_forum_view.less` | Thread listing |
| `xv_thread_view.less` | Post display |

---

## Important Notes

1. **Designer Mode is ENABLED** - No recompile needed
2. **Always test dark AND light modes**
3. **Always test RTL layout**
4. **Update skills/ docs when learning new patterns**
