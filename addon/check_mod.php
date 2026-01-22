<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$mod = \XF::finder('XF:TemplateModification')
    ->where('modification_key', 'xv_member_card_redesign')
    ->fetchOne();

if ($mod) {
    echo "=== REPLACE VALUE ===\n";
    echo $mod->replace;
    echo "\n";
} else {
    echo "Modification NOT FOUND\n";
}
