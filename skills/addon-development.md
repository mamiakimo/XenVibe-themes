# XenForo Addon Development

## Addon Structure

```
src/addons/{Vendor}/{AddonName}/
├── addon.json              # Addon metadata
├── Setup.php               # Install/upgrade/uninstall
├── Listener.php            # Event listeners
├── Pub/
│   └── Controller/         # Public controllers
└── _data/
    ├── phrases.xml                 # Language phrases
    ├── routes.xml                  # URL routes
    ├── templates.xml               # Template list
    ├── template_modifications.xml  # Template mods
    ├── style_properties.xml        # Style properties
    ├── style_property_groups.xml   # Property groups
    ├── code_event_listeners.xml    # Event listeners
    └── widget_positions.xml        # Widget positions
```

---

## addon.json

```json
{
  "legacy_addon_id": "",
  "title": "XenVibe Core",
  "description": "XenVibe Style Framework",
  "version_id": 1000010,
  "version_string": "1.0.0",
  "dev": "YourName",
  "dev_url": "https://yoursite.com",
  "faq_url": "",
  "support_url": "",
  "extra_urls": {},
  "require": [],
  "icon": ""
}
```

---

## Phrases (phrases.xml)

```xml
<?xml version="1.0" encoding="utf-8"?>
<phrases>
  <phrase title="xv_phrase_key" version_id="0">
    <![CDATA[English text here]]>
  </phrase>
  <phrase title="grid_view" version_id="0">
    <![CDATA[Grid View]]>
  </phrase>
</phrases>
```

### Using Phrases in Templates
```html
{{ phrase('xv_phrase_key') }}
{{ phrase('grid_view') }}
```

### Import Phrases
```bash
php cmd.php xf-dev:import-phrases --addon=Vendor/AddonName
```

---

## Template Modifications (template_modifications.xml)

```xml
<?xml version="1.0" encoding="utf-8"?>
<template_modifications>
  <modification
    type="public"
    template="template_name"
    modification_key="xv_mod_key"
    description="Description of modification"
    execution_order="10"
    enabled="1"
    action="str_replace">
    <find><![CDATA[text to find]]></find>
    <replace><![CDATA[replacement text]]></replace>
  </modification>
</template_modifications>
```

### Action Types
- `str_replace` - Simple string replacement
- `preg_replace` - Regex replacement
- `callback` - PHP callback function

### Import Template Modifications
```bash
php cmd.php xf-dev:import-template-modifications
```

---

## Style Properties (style_properties.xml)

```xml
<?xml version="1.0" encoding="utf-8"?>
<style_properties>
  <property
    property_name="xv_headerBackground"
    group_name="xvHeader"
    title="Header Background"
    description="Background color for header"
    property_type="color"
    css_components="background-color"
    value_type="color"
    default_value="#1a1a2e">
  </property>
</style_properties>
```

### Property Types
- `color` - Color picker
- `unit` - Size with unit (px, rem, etc.)
- `number` - Numeric value
- `template` - CSS template
- `checkbox` - Boolean

---

## Style Property Groups (style_property_groups.xml)

```xml
<?xml version="1.0" encoding="utf-8"?>
<style_property_groups>
  <group
    group_name="xvHeader"
    title="XenVibe: Header"
    description="Header styling options"
    display_order="10">
  </group>
</style_property_groups>
```

---

## Routes (routes.xml)

```xml
<?xml version="1.0" encoding="utf-8"?>
<routes>
  <route
    route_type="public"
    route_prefix="xenvibe"
    sub_name=""
    format=""
    build_class=""
    build_method=""
    controller="XenVibe\Core\Pub\Controller\Home"
    context=""
    action_prefix="">
  </route>
</routes>
```

---

## CLI Commands

### Rebuild Addon
```bash
php cmd.php xf-addon:rebuild Vendor/AddonName -n
```

### Export Addon Data
```bash
php cmd.php xf-addon:export Vendor/AddonName
```

### Import Specific Data
```bash
php cmd.php xf-dev:import-phrases --addon=Vendor/AddonName
php cmd.php xf-dev:import-template-modifications
php cmd.php xf-dev:import-templates
php cmd.php xf-dev:import-style-properties --addon=Vendor/AddonName
```

### Rebuild Caches
```bash
php cmd.php xf-dev:rebuild-caches
```

### Recompile
```bash
php cmd.php xf-dev:recompile-templates
php cmd.php xf-dev:recompile-phrases
```

---

## Event Listeners

### code_event_listeners.xml
```xml
<?xml version="1.0" encoding="utf-8"?>
<code_event_listeners>
  <listener
    event_id="templater_setup"
    execute_order="10"
    callback_class="XenVibe\Core\Listener"
    callback_method="templaterSetup"
    active="1"
    description="Setup templater">
  </listener>
</code_event_listeners>
```

### Common Events
- `templater_setup` - Add template globals/functions
- `app_pub_start_end` - After public app starts
- `navigation_setup` - Modify navigation
- `entity_pre_save` - Before entity saves

---

## Naming Conventions

| Type | Prefix | Example |
|------|--------|---------|
| PHP Namespace | `Vendor\AddonName\` | `XenVibe\Core\Pub\Controller\Home` |
| Phrases | `xv_` | `xv_welcome_message` |
| Style Properties | `xv_` | `xv_headerBackground` |
| Property Groups | `xv` | `xvHeader` |
| CSS Classes | `.xv-` | `.xv-thread-card` |
| Templates | `xv_` | `xv_home` |
| Template Mods | `xv_` | `xv_signature_first_post` |

---

## Development Tips

### 1. Use Designer Mode for CSS
Enable in config.php for instant CSS updates without recompile.

### 2. Export After Changes
Always export addon data after making changes in Admin panel:
```bash
php cmd.php xf-addon:export Vendor/AddonName
```

### 3. Test in Multiple Languages
Phrases must work in all supported languages.

### 4. Check Version Compatibility
Test with minimum supported XenForo version.

### 5. Use Proper Selectors
Template modifications should use unique selectors to avoid conflicts.
