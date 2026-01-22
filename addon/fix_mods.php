<?php
$pdo = new PDO('mysql:host=db;dbname=zyzoom_dev', 'zyzoom_dev_user', 'devuser123456');

// Delete existing XenVibe modifications
$pdo->exec("DELETE FROM xf_template_modification WHERE addon_id = 'XenVibe/Core'");
echo "Deleted existing modifications\n";

// Show all modifications count
$stmt = $pdo->query("SELECT COUNT(*) as cnt FROM xf_template_modification WHERE addon_id = 'XenVibe/Core'");
$row = $stmt->fetch();
echo "Remaining XenVibe mods: " . $row['cnt'] . "\n";
