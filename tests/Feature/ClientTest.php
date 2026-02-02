<?php

use App\Models\Client;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// API Tests
test('can list clients via api', function () {
    $user = User::factory()->create();
    Client::factory()->count(3)->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/clients');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('can create client via api', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/clients', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone_number' => '1234567890',
            'address' => '123 Main St',
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'email', 'phone_number', 'address', 'created_at'],
        ]);

    $this->assertDatabaseHas('clients', [
        'name' => 'Test Client',
        'email' => 'client@example.com',
    ]);
});

test('can create client with nullable fields via api', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/clients', [
            'name' => 'Minimal Client',
        ]);

    $response->assertCreated();

    $this->assertDatabaseHas('clients', [
        'name' => 'Minimal Client',
        'email' => null,
        'phone_number' => null,
        'address' => null,
    ]);
});

test('can show client via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/clients/{$client->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $client->id,
                'name' => $client->name,
            ],
        ]);
});

test('can update client via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/clients/{$client->id}", [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

    $response->assertOk();

    $client->refresh();
    expect($client->name)->toBe('Updated Name');
    expect($client->email)->toBe('updated@example.com');
});

test('can delete client via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/clients/{$client->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});

test('requires name when creating client via api', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/clients', [
            'email' => 'test@example.com',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('validates email format via api', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/clients', [
            'name' => 'Test Client',
            'email' => 'invalid-email',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

// Web/Inertia Tests
test('clients index page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('clients.index'));

    $response->assertOk();
});

test('client create page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('clients.create'));

    $response->assertOk();
});

test('client edit page is displayed', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('clients.edit', $client));

    $response->assertOk();
});

test('can create client via web', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('clients.store'), [
            'name' => 'Web Client',
            'email' => 'web@example.com',
        ]);

    $response->assertRedirect(route('clients.index'));

    $this->assertDatabaseHas('clients', [
        'name' => 'Web Client',
        'email' => 'web@example.com',
    ]);
});

test('can update client via web', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create(['name' => 'Old Name']);

    $response = $this->actingAs($user)
        ->put(route('clients.update', $client), [
            'name' => 'Updated via Web',
        ]);

    $response->assertRedirect(route('clients.index'));

    $client->refresh();
    expect($client->name)->toBe('Updated via Web');
});

test('can delete client via web', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)
        ->delete(route('clients.destroy', $client));

    $response->assertRedirect(route('clients.index'));

    $this->assertDatabaseMissing('clients', ['id' => $client->id]);
});
