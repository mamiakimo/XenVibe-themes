<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

echo "=== All member_notable templates ===\n";
$templates = $db->fetchAll("
    SELECT style_id, template
    FROM xf_template
    WHERE title = 'member_notable'
    AND type = 'public'
");

foreach ($templates as $t) {
    echo "\n--- Style ID: {$t['style_id']} ---\n";

    $hasFilterBtn = strpos($t['template'], 'xv-filter-btn') !== false;
    $hasFollowBtn = strpos($t['template'], 'js-followBtn') !== false;
    $hasXvFollowing = strpos($t['template'], 'xvFollowing') !== false;
    $hasCssInclude = strpos($t['template'], 'member.less') !== false;

    echo "Has xv-filter-btn: " . ($hasFilterBtn ? 'YES' : 'NO') . "\n";
    echo "Has js-followBtn: " . ($hasFollowBtn ? 'YES' : 'NO') . "\n";
    echo "Has xvFollowing: " . ($hasXvFollowing ? 'YES' : 'NO') . "\n";
    echo "Has member.less: " . ($hasCssInclude ? 'YES' : 'NO') . "\n";

    // Show first 200 chars
    echo "First 200 chars: " . substr($t['template'], 0, 200) . "\n";
}

// Also check the modification error log
echo "\n=== Recent template errors ===\n";
$errors = $db->fetchAll("
    SELECT * FROM xf_error_log
    WHERE message LIKE '%member_notable%' OR message LIKE '%template%modification%'
    ORDER BY error_id DESC
    LIMIT 5
");

if (empty($errors)) {
    echo "No errors found\n";
} else {
    foreach ($errors as $e) {
        echo $e['message'] . "\n\n";
    }
}
