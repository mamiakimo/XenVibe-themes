<?php
/**
 * Add/update Arabic translations for XenVibe phrases
 */

require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

// Check existing phrases
echo "=== Checking existing phrases ===\n";
$phrases = ['xv_quick_links', 'grid_view', 'list_view'];
foreach ($phrases as $p) {
    $result = $db->fetchAll("SELECT language_id, phrase_text FROM xf_phrase WHERE title = ?", $p);
    echo "$p:\n";
    foreach ($result as $r) {
        echo "  Language {$r['language_id']}: {$r['phrase_text']}\n";
    }
    if (empty($result)) {
        echo "  NOT FOUND\n";
    }
}

// Get Arabic language ID
$arabicLang = $db->fetchRow("SELECT language_id FROM xf_language WHERE title LIKE '%Arabic%' OR title LIKE '%عربي%' LIMIT 1");
echo "\n=== Arabic Language ===\n";
if ($arabicLang) {
    echo "Found Arabic language ID: {$arabicLang['language_id']}\n";
} else {
    echo "Arabic language not found, listing all languages:\n";
    $langs = $db->fetchAll("SELECT language_id, title FROM xf_language");
    foreach ($langs as $l) {
        echo "  ID {$l['language_id']}: {$l['title']}\n";
    }
}
