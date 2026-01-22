<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

// Get member_macros template
$template = $db->fetchOne("
    SELECT template
    FROM xf_template
    WHERE title = 'member_macros'
    AND type = 'public'
    AND style_id = 0
");

// Find the follow button section
if (preg_match('/canFollowUser.*?<\/xf:button>/s', $template, $match)) {
    echo "=== Follow Button Code ===\n";
    echo $match[0];
}

echo "\n\n=== Full macro containing follow ===\n";
// Find the macro that contains follow
if (preg_match('/<xf:macro name="member_tooltip".*?<\/xf:macro>/s', $template, $match)) {
    echo $match[0];
}
