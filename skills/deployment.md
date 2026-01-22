# XenForo Style Deployment

## Server Setup

### Development Server Info
| Item | Value |
|------|-------|
| URL | https://dev.zyzoom.net/ |
| SSH Host | `zyzoom` |
| Server IP | 159.195.68.202 |
| SSH Port | 1985 |

### Docker Containers
| Container | Purpose |
|-----------|---------|
| `zyzoom_dev_php` | PHP 8.x for XenForo |
| `zyzoom_dev_db` | MySQL Database |
| `zyzoom_dev_nginx` | Web Server |

### File Paths
| Path | Description |
|------|-------------|
| `/opt/zyzoom-dev/public_html/forum/` | XenForo root |
| `/opt/zyzoom-dev/public_html/forum/src/addons/` | Addons directory |
| `/opt/zyzoom-dev/public_html/forum/src/styles/` | Styles directory |

---

## Quick Deploy Commands

### Upload Single CSS File
```bash
scp "local/path/file.less" zyzoom:/opt/zyzoom-dev/public_html/forum/src/styles/{style_name}/templates/public/
```

### Upload All CSS Templates
```bash
scp local/templates/*.less zyzoom:/opt/zyzoom-dev/public_html/forum/src/styles/{style_name}/templates/public/
```

### Upload Addon Files
```bash
scp -r local/addon/* zyzoom:/opt/zyzoom-dev/public_html/forum/src/addons/Vendor/AddonName/
```

---

## SSH Commands

### Connect to Server
```bash
ssh zyzoom
```

### Enter PHP Container
```bash
docker exec -it zyzoom_dev_php bash
```

### Run XenForo Commands
```bash
# From inside container
cd /var/www/html
php cmd.php xf-addon:rebuild Vendor/AddonName -n

# Or from host
docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-addon:rebuild Vendor/AddonName -n
```

---

## Common Deployment Tasks

### 1. Deploy CSS Changes
```bash
# Upload files
scp templates/*.less zyzoom:/opt/zyzoom-dev/public_html/forum/src/styles/xenvibe_cards/templates/public/

# Hard refresh browser (Ctrl+Shift+R)
```

### 2. Deploy Addon Changes
```bash
# Upload addon files
scp -r addon_data/* zyzoom:/opt/zyzoom-dev/public_html/forum/src/addons/XenVibe/Core/_data/

# Rebuild addon
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-addon:rebuild XenVibe/Core -n"
```

### 3. Deploy Template Modifications
```bash
# Upload template_modifications.xml
scp addon_data/template_modifications.xml zyzoom:/opt/zyzoom-dev/public_html/forum/src/addons/XenVibe/Core/_data/

# Import modifications
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-dev:import-template-modifications"
```

### 4. Deploy Phrases
```bash
# Upload phrases.xml
scp addon_data/phrases.xml zyzoom:/opt/zyzoom-dev/public_html/forum/src/addons/XenVibe/Core/_data/

# Import phrases
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-dev:import-phrases --addon=XenVibe/Core"

# Rebuild caches
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-dev:rebuild-caches"
```

---

## Troubleshooting

### CSS Not Updating
1. Hard refresh browser (Ctrl+Shift+R)
2. Check file was uploaded: `ssh zyzoom "ls -la /path/to/file.less"`
3. Check for LESS syntax errors in browser console

### Phrases Not Showing
1. Import phrases: `xf-dev:import-phrases --addon=AddonId`
2. Rebuild caches: `xf-dev:rebuild-caches`
3. Hard refresh browser

### Template Mods Not Working
1. Check modification is enabled in Admin panel
2. Verify template name is correct
3. Check execution order (lower = earlier)
4. Test find string exists in template

### Addon Changes Not Reflecting
1. Rebuild addon: `xf-addon:rebuild AddonId -n`
2. Clear browser cache
3. Check file permissions on server

---

## Designer Mode

### Benefits
- No need to recompile templates
- Instant CSS updates
- Direct file editing

### Enable
```php
// src/config.php
$config['designer']['enabled'] = true;
$config['designer']['basePath'] = 'src/styles';
```

### Workflow with Designer Mode
1. Edit local `.less` file
2. Upload via SCP
3. Refresh browser
4. See changes immediately

---

## Backup Before Deploy

### Export Current State
```bash
# Export addon
ssh zyzoom "docker exec zyzoom_dev_php php /var/www/html/cmd.php xf-addon:export XenVibe/Core"

# Backup style files
ssh zyzoom "cp -r /opt/zyzoom-dev/public_html/forum/src/styles/xenvibe_cards /opt/zyzoom-dev/backups/xenvibe_cards_$(date +%Y%m%d)"
```

---

## Production Deployment Checklist

- [ ] Test all pages on dev server
- [ ] Check both dark and light modes
- [ ] Verify RTL support (if applicable)
- [ ] Test on mobile devices
- [ ] Export addon data
- [ ] Create backup of production
- [ ] Deploy to production
- [ ] Rebuild addon on production
- [ ] Clear all caches
- [ ] Verify functionality
