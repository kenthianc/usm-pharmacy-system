<?php

use App\Models\User;

it('renders the welcome landing page with AI health assistant and guest counter without embedded store catalog', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('USM Pharmacy');
    $response->assertSee('Your health,');
    $response->assertSee('15/15 messages left');
    $response->assertSee('Pharmacy Store');
    $response->assertDontSee('Showing 34 products');
});

it('renders the pharmacy storefront on its own dedicated page with guest banner and inquiry login prompts for guests', function () {
    $response = $this->get(route('medicines'));

    $response->assertOk();
    $response->assertSee('USM Hospital Pharmacy');
    $response->assertSee('Product Catalog');
    $response->assertSee('You are browsing as a guest');
    $response->assertSee('Sign in to Inquire');
    $response->assertSee('Showing');
    $response->assertSee('products');
});

it('renders the pharmacy storefront with full member access for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('medicines'));

    $response->assertOk();
    $response->assertDontSee('You are browsing as a guest');
    $response->assertDontSee('Sign in to Inquire');
    $response->assertSee('Inquire at Pharmacy');
});
