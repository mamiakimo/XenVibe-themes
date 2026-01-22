<?php
$pdo = new PDO('mysql:host=db;dbname=zyzoom_dev', 'zyzoom_dev_user', 'devuser123456');
$stmt = $pdo->query("SELECT modification_key, enabled, action, template, find, execution_order FROM xf_template_modification WHERE modification_key LIKE 'xv_%' OR addon_id = 'XenVibe/Core'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo 'Key: ' . $row['modification_key'] . ' | Template: ' . $row['template'] . ' | Enabled: ' . $row['enabled'] . ' | Action: ' . $row['action'] . PHP_EOL;
    echo 'Find: ' . substr($row['find'], 0, 100) . '...' . PHP_EOL . PHP_EOL;
}
