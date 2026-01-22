<?php
require('/var/www/html/src/XF.php');
\XF::start('/var/www/html');

$mod = \XF::finder('XF:TemplateModification')
    ->where('modification_key', 'xv_member_filter_js')
    ->fetchOne();

echo "=== JS Modification Replace ===\n";
echo $mod->replace;
