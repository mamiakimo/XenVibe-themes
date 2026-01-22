<?php
/**
 * Check nodeListDescriptionDisplay style property
 */

require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$db = \XF::db();

// Check style property values for all styles
echo "=== nodeListDescriptionDisplay Values ===\n";
$values = $db->fetchAll("SELECT style_id, property_name, property_value FROM xf_style_property WHERE property_name = 'nodeListDescriptionDisplay'");
foreach ($values as $v) {
    echo "Style ID " . $v['style_id'] . ": " . $v['property_value'] . "\n";
}

// Check what style 11 actually has
echo "\n=== All properties for Style ID 11 with 'node' in name ===\n";
$props = $db->fetchAll("SELECT property_name, property_value FROM xf_style_property WHERE style_id = 11 AND property_name LIKE '%node%'");
foreach ($props as $p) {
    echo $p['property_name'] . " = " . $p['property_value'] . "\n";
}
