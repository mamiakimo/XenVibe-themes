<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

// Check if there are any template modification errors
$db = \XF::db();

echo "=== Template Modification Status ===\n";
$mods = $db->fetchAll("
    SELECT modification_key, enabled, action
    FROM xf_template_modification
    WHERE modification_key LIKE 'xv_%'
");

foreach ($mods as $mod) {
    echo $mod['modification_key'] . " - enabled: " . $mod['enabled'] . " - action: " . $mod['action'] . "\n";
}

echo "\n=== Checking if modification applies ===\n";

// Get the original template
$template = \XF::finder('XF:Template')
    ->where('title', 'member_notable')
    ->where('type', 'public')
    ->where('style_id', 0)
    ->fetchOne();

if ($template) {
    // Check if the find string exists
    $findString = '<xf:css src="member.less" />';
    if (strpos($template->template, $findString) !== false) {
        echo "✓ Find string exists in template\n";
    } else {
        echo "✗ Find string NOT found in template\n";
        echo "First 500 chars of template:\n";
        echo substr($template->template, 0, 500) . "\n";
    }
}

// Check compiled template in style
echo "\n=== Checking compiled template ===\n";
$styles = $db->fetchAll("SELECT style_id, title FROM xf_style WHERE style_id > 0");
foreach ($styles as $style) {
    $compiled = \XF::finder('XF:Template')
        ->where('title', 'member_notable')
        ->where('type', 'public')
        ->where('style_id', $style['style_id'])
        ->fetchOne();

    if ($compiled) {
        $hasFilter = strpos($compiled->template, 'xv-filter-btn') !== false;
        $hasFollow = strpos($compiled->template, 'xvFollowing') !== false;
        echo "Style {$style['title']} (ID: {$style['style_id']}): filters=" . ($hasFilter ? 'YES' : 'NO') . ", xvFollowing=" . ($hasFollow ? 'YES' : 'NO') . "\n";
    }
}
