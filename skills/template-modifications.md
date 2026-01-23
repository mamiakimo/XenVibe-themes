# XenForo Template Modifications

## Why Template Modifications?

Template modifications are preferred over full template overrides because:
1. **Upgrade-safe** - XenForo updates don't break your changes
2. **Minimal changes** - Only modify what you need
3. **Compatibility** - Works with other addons
4. **Maintainable** - Easier to track and debug

---

## Structure

```xml
<?xml version="1.0" encoding="utf-8"?>
<template_modifications>
  <modification
    type="public"
    template="template_name"
    modification_key="xv_unique_key"
    description="What this modification does"
    execution_order="10"
    enabled="1"
    action="str_replace">
    <find><![CDATA[text to find]]></find>
    <replace><![CDATA[replacement text]]></replace>
  </modification>
</template_modifications>
```

---

## Action Types

### 1. str_replace (Simple Replacement)
```xml
<modification action="str_replace">
  <find><![CDATA[<div class="block-body">]]></find>
  <replace><![CDATA[<div class="block-body xv-custom-body">]]></replace>
</modification>
```

### 2. preg_replace (Regex Replacement)
```xml
<modification action="preg_replace">
  <find><![CDATA[/<div class="([^"]+)">/]]></find>
  <replace><![CDATA[<div class="$1 xv-modified">]]></replace>
</modification>
```

### 3. callback (PHP Callback)
```xml
<modification action="callback">
  <find><![CDATA[{$content}]]></find>
  <replace><![CDATA[XenVibe\Core\Callback::modifyContent]]></replace>
</modification>
```

---

## Common Modification Patterns

### Add Class to Element
```xml
<find><![CDATA[<div class="node-body">]]></find>
<replace><![CDATA[<div class="node-body xv-node-body">]]></replace>
```

### Inject HTML Before Element
```xml
<find><![CDATA[<div class="block-body">]]></find>
<replace><![CDATA[<div class="xv-before-block"></div>
<div class="block-body">]]></replace>
```

### Inject HTML After Element
```xml
<find><![CDATA[</div><!-- end block-body -->]]></find>
<replace><![CDATA[</div><!-- end block-body -->
<div class="xv-after-block"></div>]]></replace>
```

### Wrap Element
```xml
<find><![CDATA[<div class="content">{$content}</div>]]></find>
<replace><![CDATA[<div class="xv-wrapper">
  <div class="content">{$content}</div>
</div>]]></replace>
```

### Add Attribute
```xml
<find><![CDATA[<a href="{$link}">]]></find>
<replace><![CDATA[<a href="{$link}" data-xv-track="true">]]></replace>
```

### Inject JavaScript
```xml
<modification template="PAGE_CONTAINER" modification_key="xv_scripts">
  <find><![CDATA[</body>]]></find>
  <replace><![CDATA[<xf:js src="styles/xenvibe/scripts.js" />
</body>]]></replace>
</modification>
```

### Inject CSS
```xml
<modification template="PAGE_CONTAINER" modification_key="xv_styles">
  <find><![CDATA[</head>]]></find>
  <replace><![CDATA[<xf:css src="styles/xenvibe/custom.css" />
</head>]]></replace>
</modification>
```

---

## Best Practices

### 1. Use Unique Keys
```xml
<!-- Good -->
<modification modification_key="xv_forum_list_grid">

<!-- Bad -->
<modification modification_key="grid_mod">
```

### 2. Target Specific Text
```xml
<!-- Good - specific -->
<find><![CDATA[<div class="node-body">]]></find>

<!-- Bad - too generic, may match multiple places -->
<find><![CDATA[<div>]]></find>
```

### 3. Include Comments for Context
```xml
<find><![CDATA[</div>
</xf:if><!-- end node stats -->]]></find>
```

### 4. Set Appropriate Execution Order
```
10-30: Early modifications (add classes, attributes)
40-60: Content modifications (inject HTML)
70-90: Late modifications (scripts, cleanup)
```

### 5. Test Find String Exists
Before creating modification, search the template:
```bash
grep -n "your find string" template.html
```

---

## Debugging Modifications

### Check if Applied
1. Admin Panel → Appearance → Template modifications
2. Look for your modification
3. Check "Test" to see if find string matches

### Common Issues

**Find string not found:**
- Template may have been updated
- Whitespace differences
- Check for exact match including newlines

**Multiple matches:**
- Make find string more specific
- Include surrounding context

**Execution order conflict:**
- Another addon modifies same area
- Adjust execution order

---

## Template Reference

### Key Templates to Modify

| Template | Purpose |
|----------|---------|
| `PAGE_CONTAINER` | Main page structure |
| `forum_list` | Forum categories page |
| `forum_view` | Thread list in forum |
| `thread_view` | Posts in thread |
| `member_view` | User profile |
| `whats_new_posts` | What's New page |
| `post_macros` | Post display macros |
| `message_macros` | Message user info |
| `node_list_forum` | Single forum node |

### Finding the Right Template
1. View page source in browser
2. Look for `<!-- template: template_name -->`
3. Or check Admin → Appearance → Templates

---

## Real Examples from XenVibe

### Add Filter Buttons to Members List
```xml
<modification
  type="public"
  template="member_notable"
  modification_key="xv_member_pill_filters"
  description="Add filter pill buttons to members list"
  execution_order="10"
  action="str_replace">
  <find><![CDATA[<div class="block-body">]]></find>
  <replace><![CDATA[<div class="xv-member-filters">
    <button class="xv-filter-btn active" data-filter="all">{{ phrase('all') }}</button>
    <button class="xv-filter-btn" data-filter="staff">{{ phrase('staff') }}</button>
  </div>
<div class="block-body">]]></replace>
</modification>
```

### Show Signature Only on First Post
```xml
<modification
  type="public"
  template="post_macros"
  modification_key="xv_signature_first_post"
  description="Show signature only for thread starter's first post"
  execution_order="50"
  action="preg_replace">
  <find><![CDATA[/<xf:if is="\$post\.canShowSignature\(\)">/]]></find>
  <replace><![CDATA[<xf:if is="$post.canShowSignature() && $post.isFirstPost()">]]></replace>
</modification>
```

---

## Import Command

After adding/modifying template_modifications.xml:
```bash
php cmd.php xf-dev:import-template-modifications
```

Or rebuild entire addon:
```bash
php cmd.php xf-addon:rebuild XenVibe/Core -n
```
