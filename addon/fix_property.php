<?php
/**
 * Fix nodeListDescriptionDisplay style property for style 11
 */

require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

// Update with proper JSON format
$db->update('xf_style_property',
    ['property_value' => '"inline"'],  // JSON string format
    "style_id = 11 AND property_name = 'nodeListDescriptionDisplay'"
);

echo "Updated property value to JSON format\n";

// Verify
$result = $db->fetchRow("SELECT property_value FROM xf_style_property WHERE style_id = 11 AND property_name = 'nodeListDescriptionDisplay'");
echo "New value: " . $result['property_value'] . "\n";
