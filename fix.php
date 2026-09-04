<?php

$replacements = [
    'batch_number' => 'batch_no',
    '->price' => '->unit_price',
    'first_name' => 'name',
    '{{ $rx->patient->last_name }}' => '',
    '{{ $prescription->patient->last_name }}' => '',
    '{{ $transaction->prescription->patient->last_name }}' => '',
    '->brand_name' => '->name',
    'prescription_number' => 'id',
];
foreach (['app/Services/DispensingService.php', 'resources/views/pos/index.blade.php', 'resources/views/pos/process.blade.php', 'resources/views/pos/otc.blade.php', 'resources/views/pos/receipt.blade.php'] as $file) {
    if (! file_exists($file)) {
        continue;
    }
    $content = file_get_contents($file);
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    file_put_contents($file, $content);
}
echo 'Done.';
