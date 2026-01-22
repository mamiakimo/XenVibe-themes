<?php
/**
 * Add Arabic translations for XenVibe phrases
 */

require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

$arabicLangId = 3;

// Phrases to add/translate
$phrases = [
    'xv_quick_links' => [
        'english' => 'Quick Links',
        'arabic' => 'روابط سريعة'
    ],
    'grid_view' => [
        'english' => 'Grid view',
        'arabic' => 'عرض شبكي'
    ],
    'list_view' => [
        'english' => 'List view',
        'arabic' => 'عرض قائمة'
    ]
];

foreach ($phrases as $title => $texts) {
    // Check if master phrase exists
    $masterExists = $db->fetchOne("SELECT COUNT(*) FROM xf_phrase WHERE title = ? AND language_id = 0", $title);

    if (!$masterExists) {
        // Create master phrase
        $db->insert('xf_phrase', [
            'language_id' => 0,
            'title' => $title,
            'phrase_text' => $texts['english'],
            'global_cache' => 0,
            'addon_id' => 'XenVibe/Core',
            'version_id' => 0,
            'version_string' => ''
        ]);
        echo "Created master phrase: $title = {$texts['english']}\n";
    }

    // Check if Arabic translation exists
    $arabicExists = $db->fetchOne("SELECT COUNT(*) FROM xf_phrase WHERE title = ? AND language_id = ?", [$title, $arabicLangId]);

    if ($arabicExists) {
        // Update existing
        $db->update('xf_phrase',
            ['phrase_text' => $texts['arabic']],
            "title = ? AND language_id = ?",
            [$title, $arabicLangId]
        );
        echo "Updated Arabic phrase: $title = {$texts['arabic']}\n";
    } else {
        // Insert new Arabic translation
        $db->insert('xf_phrase', [
            'language_id' => $arabicLangId,
            'title' => $title,
            'phrase_text' => $texts['arabic'],
            'global_cache' => 0,
            'addon_id' => 'XenVibe/Core',
            'version_id' => 0,
            'version_string' => ''
        ]);
        echo "Created Arabic phrase: $title = {$texts['arabic']}\n";
    }
}

echo "\nDone! Run phrase rebuild to apply changes.\n";
