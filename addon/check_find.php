<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

// Get the modification
$mod = $db->fetchRow("
    SELECT * FROM xf_template_modification
    WHERE modification_key = 'xv_member_filter_js'
");

echo "=== Modification Find String ===\n";
echo "Find: [" . $mod['find'] . "]\n";
echo "Length: " . strlen($mod['find']) . "\n";

// Get the template
$template = $db->fetchOne("
    SELECT template FROM xf_template
    WHERE title = 'member_notable'
    AND type = 'public'
    AND style_id = 0
");

echo "\n=== Searching in template ===\n";

// Exact search
$pos = strpos($template, $mod['find']);
if ($pos !== false) {
    echo "✓ EXACT match found at position $pos\n";
} else {
    echo "✗ EXACT match NOT found\n";

    // Try variations
    $find1 = '<xf:css src="member.less" />';
    $find2 = "<xf:css src=\"member.less\" />";

    echo "\nTrying variations:\n";
    echo "Variation 1: " . (strpos($template, $find1) !== false ? "FOUND" : "NOT FOUND") . "\n";
    echo "Variation 2: " . (strpos($template, $find2) !== false ? "FOUND" : "NOT FOUND") . "\n";

    // Show context around member.less
    $pos = strpos($template, 'member.less');
    if ($pos !== false) {
        echo "\nContext around 'member.less' (pos $pos):\n";
        echo "[" . substr($template, max(0, $pos - 20), 60) . "]\n";
    }
}

// Force recompile
echo "\n=== Forcing template recompile ===\n";
$templateEntity = \XF::finder('XF:Template')
    ->where('title', 'member_notable')
    ->where('type', 'public')
    ->where('style_id', 0)
    ->fetchOne();

if ($templateEntity) {
    $templateEntity->getBehavior('XF:DevOutputWritable')->setOption('write_dev_output', false);
    $templateEntity->save();
    echo "Template saved - check if modifications applied now\n";

    // Re-check
    $newTemplate = $db->fetchOne("
        SELECT template FROM xf_template
        WHERE title = 'member_notable'
        AND type = 'public'
        AND style_id = 0
    ");

    echo "Has xv-filter-btn now: " . (strpos($newTemplate, 'xv-filter-btn') !== false ? 'YES' : 'NO') . "\n";
}
