<?php

use App\Models\Client;
use App\Models\Document;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// API Tests
test('can list uploads for document via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    Upload::factory()->count(3)->create(['document_id' => $document->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/documents/{$document->id}/uploads");

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('can create upload via api', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/documents/{$document->id}/uploads", [
            'file' => $file,
            'exp_date' => '2026-12-31',
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'document_id', 'file_path', 'exp_date'],
        ]);

    $this->assertDatabaseHas('uploads', [
        'document_id' => $document->id,
        'exp_date' => '2026-12-31',
    ]);
});

test('can show upload via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    $upload = Upload::factory()->create(['document_id' => $document->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->getJson("/api/uploads/{$upload->id}");

    $response->assertOk()
        ->assertJson([
            'data' => [
                'id' => $upload->id,
                'document_id' => $document->id,
            ],
        ]);
});

test('can update upload via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    $upload = Upload::factory()->create([
        'document_id' => $document->id,
        'exp_date' => '2026-01-01',
    ]);

    $response = $this->actingAs($user, 'sanctum')
        ->putJson("/api/uploads/{$upload->id}", [
            'exp_date' => '2027-01-01',
        ]);

    $response->assertOk();

    $upload->refresh();
    expect($upload->exp_date->format('Y-m-d'))->toBe('2027-01-01');
});

test('can delete upload via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    $upload = Upload::factory()->create(['document_id' => $document->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->deleteJson("/api/uploads/{$upload->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('uploads', ['id' => $upload->id]);
});

test('requires file when creating upload via api', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/documents/{$document->id}/uploads", [
            'exp_date' => '2026-12-31',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['file']);
});

test('requires exp_date when creating upload via api', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->actingAs($user, 'sanctum')
        ->postJson("/api/documents/{$document->id}/uploads", [
            'file' => $file,
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['exp_date']);
});

// Web/Inertia Tests
test('document show page with uploads is displayed', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);

    $response = $this->actingAs($user)
        ->get(route('documents.show', $document));

    $response->assertOk();
});

test('can create upload via web', function () {
    Storage::fake('local');
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    $file = UploadedFile::fake()->create('document.pdf', 100);

    $response = $this->actingAs($user)
        ->post(route('documents.uploads.store', $document), [
            'file' => $file,
            'exp_date' => '2026-12-31',
        ]);

    $response->assertRedirect(route('documents.show', $document));

    $this->assertDatabaseHas('uploads', [
        'document_id' => $document->id,
        'exp_date' => '2026-12-31',
    ]);
});

test('can delete upload via web', function () {
    $user = User::factory()->create();
    $client = Client::factory()->create();
    $document = Document::factory()->create(['client_id' => $client->id]);
    $upload = Upload::factory()->create(['document_id' => $document->id]);

    $response = $this->actingAs($user)
        ->delete(route('uploads.destroy', $upload));

    $response->assertRedirect();

    $this->assertDatabaseMissing('uploads', ['id' => $upload->id]);
});
