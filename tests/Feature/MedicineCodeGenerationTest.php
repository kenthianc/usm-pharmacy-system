<?php

use App\Models\Medicine;

test('it automatically generates a unique product code on creation if not provided', function () {
    $medicine = Medicine::factory()->create([
        'category' => 'Antibiotics',
        'code' => null,
    ]);

    expect($medicine->code)->not->toBeNull()
        ->and($medicine->code)->toStartWith('ANT-')
        ->and($medicine->code)->toHaveLength(8)
        ->and($medicine->sku)->toBe($medicine->code)
        ->and($medicine->item_code)->toBe($medicine->code);
});

test('it preserves custom code if explicitly provided on creation', function () {
    $medicine = Medicine::factory()->create([
        'code' => 'CUSTOM-PAR-500',
    ]);

    expect($medicine->code)->toBe('CUSTOM-PAR-500')
        ->and($medicine->sku)->toBe('CUSTOM-PAR-500');
});

test('it resolves code collisions to guarantee absolute uniqueness', function () {
    // Manually create a medicine with a specific code
    $first = Medicine::factory()->create([
        'category' => 'Vitamins',
        'code' => null,
    ]);

    // Force create another that would have the exact same base code
    $expectedBase = 'VIT-'.str_pad((string) ($first->id + 1), 4, '0', STR_PAD_LEFT);

    // Create a blocker with that exact expected base code
    $blocker = Medicine::factory()->create([
        'code' => $expectedBase,
    ]);

    // Now create next medicine in Vitamins
    $second = Medicine::factory()->create([
        'category' => 'Vitamins',
        'code' => null,
    ]);

    // Since the expected base was already taken by $blocker, $second must be uniquely suffixed
    expect($second->code)->not->toBe($blocker->code)
        ->and(Medicine::where('code', $second->code)->count())->toBe(1);
});

test('it allows updating code via sku attribute alias', function () {
    $medicine = Medicine::factory()->create();

    $medicine->update(['sku' => 'UPDATED-SKU-999']);

    $medicine->refresh();
    expect($medicine->code)->toBe('UPDATED-SKU-999')
        ->and($medicine->sku)->toBe('UPDATED-SKU-999');
});
