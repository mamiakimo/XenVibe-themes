<?php
libxml_use_internal_errors(true);
$xml = simplexml_load_file("/var/www/html/src/addons/XenVibe/Core/_data/template_modifications.xml");
if ($xml === false) {
    echo "Invalid XML:\n";
    foreach (libxml_get_errors() as $e) {
        echo $e->message . "\n";
    }
} else {
    echo "Valid XML\n";
    echo "Found " . count($xml->modification) . " modifications:\n";
    foreach ($xml->modification as $mod) {
        echo "  - " . $mod["modification_key"] . " (" . $mod["template"] . ")\n";
    }
}
