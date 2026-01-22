<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

// Check the modification
$mod = \XF::finder('XF:TemplateModification')
    ->where('modification_key', 'xv_member_card_redesign')
    ->fetchOne();

echo "=== MODIFICATION REPLACE ===\n";
if ($mod) {
    // Check if members/follow is in the replace
    if (strpos($mod->replace, 'members/follow') !== false) {
        echo "✓ 'members/follow' FOUND in modification\n";
    } else {
        echo "✗ 'members/follow' NOT FOUND in modification\n";
    }

    // Show the link line
    preg_match('/href.*?xv-member-follow-btn/s', $mod->replace, $m);
    if ($m) {
        echo "Link pattern: " . $m[0] . "\n";
    }
}

echo "\n=== CHECKING COMPILED TEMPLATE ===\n";

// Get the compiled template
$db = \XF::db();
$compiled = $db->fetchOne("
    SELECT template_compiled
    FROM xf_template_compiled
    WHERE title = 'member_notable'
    AND style_id > 0
    LIMIT 1
");

if ($compiled) {
    if (strpos($compiled, 'members/follow') !== false) {
        echo "✓ 'members/follow' FOUND in compiled template\n";
    } else {
        echo "✗ 'members/follow' NOT FOUND in compiled template\n";

        // Check what link is being used
        if (preg_match('/xv-member-follow-btn[^>]*href=["\']([^"\']+)["\']/s', $compiled, $m)) {
            echo "Current link: " . $m[1] . "\n";
        }
    }
} else {
    echo "No compiled template found\n";
}
