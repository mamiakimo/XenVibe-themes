<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

echo "=== All XV Template Modifications ===\n";
$mods = $db->fetchAll("
    SELECT *
    FROM xf_template_modification
    WHERE modification_key LIKE 'xv_%'
");

foreach ($mods as $mod) {
    echo "\n--- {$mod['modification_key']} ---\n";
    echo "addon_id: {$mod['addon_id']}\n";
    echo "template: {$mod['template']}\n";
    echo "type: {$mod['type']}\n";
    echo "enabled: {$mod['enabled']}\n";
    echo "action: {$mod['action']}\n";
    echo "execution_order: {$mod['execution_order']}\n";
    echo "find (first 50): " . substr($mod['find'], 0, 50) . "\n";
    echo "replace (first 100): " . substr($mod['replace'], 0, 100) . "\n";
}

echo "\n=== Testing modification manually ===\n";

// Get the original template
$template = $db->fetchOne("
    SELECT template FROM xf_template
    WHERE title = 'member_notable'
    AND type = 'public'
    AND style_id = 0
");

// Get the JS modification
$jsMod = $db->fetchRow("
    SELECT * FROM xf_template_modification
    WHERE modification_key = 'xv_member_filter_js'
");

if ($jsMod) {
    $newTemplate = str_replace($jsMod['find'], $jsMod['replace'], $template);
    $changed = $newTemplate !== $template;
    echo "Manual str_replace would change template: " . ($changed ? 'YES' : 'NO') . "\n";

    if ($changed) {
        echo "After modification, has xv-filter-btn: " . (strpos($newTemplate, 'xv-filter-btn') !== false ? 'YES' : 'NO') . "\n";
    }
}
