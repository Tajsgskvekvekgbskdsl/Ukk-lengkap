<?php
$out = [];
exec('php artisan route:list --no-ansi 2>&1', $out);
file_put_contents(__DIR__.'/rlx.txt', implode("\n",$out));
foreach ($out as $l) if (stripos($l,'tenteng')!==false || stripos($l,'settings')!==false) echo $l."\n";
echo "lint TentuController: "; echo shell_exec('php -l '.__DIR__.'/app/Http/Controllers/TentuController.php');
echo "lint SettingsController: "; echo shell_exec('php -l '.__DIR__.'/app/Http/Controllers/SettingsController.php');
echo "DONE\n";