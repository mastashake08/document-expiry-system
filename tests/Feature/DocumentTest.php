<?php

use App\Models\Client;
use App\Models\Document;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// API Tests
test('can list documents via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    Document::factory()->count(3)->create(['client_id' => $client->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/documents');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('can list documents for specific client via api', function () {
    $user = User::factory()->create();
    $client1 = Client::factory()->create();
    $client2 = Client::factory()->create();
    Document::factory()->count(2)->create(['client_id' => $client1->id]);
    Document::factory()->count(3)->create(['client_id' => $client2->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/clients/{$client1->id}/documents");

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

test('can create document via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/documents', [
            'client_id' => $client->id,
            'name' => 'Test Document',
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'client_id', 'created_at'],
        ]);

    $this->assertDatabaseHas('documents', [
        'client_id' => $client->id,
        'name' => 'Test Document',
    ]);
});

test('can show document via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/documents/{$document->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $document->id,
                'name' => $document->name,
                'client_id' => $client->id,
            ],
        ]);
});

test('can update document via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id, 'name' => 'Old Name']);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/documents/{$document->id}", [
            'client_id' => $client->id,
            'name' => 'Updated Name',
        ]);

    $response->assertOk();

    $document->refresh();
    expect($document->name)->toBe('Updated Name');
});

test('can delete document via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/documents/{$document->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('requires name when creating document via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/documents', [
            'client_id' => $client->id,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('requires valid client_id when creating document via api', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')
        ->postJson('/api/documents', [
            'client_id' => 999,
            'name' => 'Test Document',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['client_id']);
});

// Web/Inertia Tests
test('documents index page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('documents.index'));

    $response->assertOk();
});

test('document create page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('documents.create'));

    $response->assertOk();
});

test('document edit page is displayed', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($user)
        ->get(route('documents.edit', $document));

    $response->assertOk();
});

test('can create document via web', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('documents.store'), [
            'client_id' => $client->id,
            'name' => 'Web Document',
        ]);

    $response->assertRedirect(route('documents.index'));

    $this->assertDatabaseHas('documents', [
        'name' => 'Web Document',
        'client_id' => $client->id,
    ]);
});

test('can update document via web', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id, 'name' => 'Old Name']);

    $response = $this->actingAs($user)
        ->put(route('documents.update', $document), [
            'client_id' => $client->id,
            'name' => 'Updated via Web',
        ]);

    $response->assertRedirect(route('documents.index'));

    $document->refresh();
    expect($document->name)->toBe('Updated via Web');
});

test('can delete document via web', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($user)
        ->delete(route('documents.destroy', $document));

    $response->assertRedirect(route('documents.index'));

    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
});

test('can create document with first upload', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    
    \Illuminate\Support\Facades\Storage::fake('local');
    
    $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 1024);
    $expDate = now()->addMonths(6)->format('Y-m-d');

    $response = $this->actingAs($user)
        ->post(route('documents.store'), [
            'client_id' => $client->id,
            'name' => 'Document with Upload',
            'file' => $file,
            'exp_date' => $expDate,
        ]);

    $response->assertRedirect(route('documents.index'));

    $this->assertDatabaseHas('documents', [
        'name' => 'Document with Upload',
        'client_id' => $client->id,
    ]);
    
    $document = Document::where('name', 'Document with Upload')->first();
    
    $this->assertDatabaseHas('uploads', [
        'document_id' => $document->id,
        'exp_date' => $expDate,
    ]);
    
    \Illuminate\Support\Facades\Storage::disk('local')->assertExists($document->uploads->first()->file_path);
});

test('can create document without upload', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('documents.store'), [
            'client_id' => $client->id,
            'name' => 'Document without Upload',
        ]);

    $response->assertRedirect(route('documents.index'));

    $document = Document::where('name', 'Document without Upload')->first();
    
    expect($document)->not->toBeNull();
    expect($document->uploads)->toHaveCount(0);
});

test('requires expiration date when file is provided', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    
    \Illuminate\Support\Facades\Storage::fake('local');
    
    $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 1024);

    $response = $this->actingAs($user)
        ->post(route('documents.store'), [
            'client_id' => $client->id,
            'name' => 'Document with Upload',
            'file' => $file,
            // missing exp_date
        ]);

    $response->assertSessionHasErrors(['exp_date']);
});
