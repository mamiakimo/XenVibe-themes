<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

echo "=== Searching for follow-related templates ===\n\n";

// Find templates containing "follow"
$db = \XF::db();

$templates = $db->fetchAll("
    SELECT title, style_id, template
    FROM xf_template
    WHERE type = 'public'
    AND (
        title LIKE '%tooltip%'
        OR title LIKE '%member_view%'
        OR title LIKE '%member_card%'
        OR title = 'member_macros'
    )
    AND style_id = 0
");

foreach ($templates as $t) {
    if (stripos($t['template'], 'follow') !== false) {
        echo "=== " . $t['title'] . " ===\n";

        // Find the follow-related code
        preg_match_all('/.*follow.*/i', $t['template'], $matches);
        foreach ($matches[0] as $line) {
            echo trim($line) . "\n";
        }
        echo "\n";
    }
}

echo "\n=== Searching for isFollowing usage ===\n";
$templates2 = $db->fetchAll("
    SELECT title, template
    FROM xf_template
    WHERE type = 'public'
    AND template LIKE '%isFollowing%'
    AND style_id = 0
");

foreach ($templates2 as $t) {
    echo "Found in: " . $t['title'] . "\n";
    preg_match_all('/.*isFollowing.*/i', $t['template'], $matches);
    foreach ($matches[0] as $line) {
        echo "  " . trim($line) . "\n";
    }
}
