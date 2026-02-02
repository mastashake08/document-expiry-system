<?php

use App\Models\Client;
use App\Models\Document;
use App\Models\Upload;
use App\Notifications\DocumentExpiringNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('expiring notification is sent to client with email', function () {
    Notification::fake();
    Storage::fake('local');
    
    $client = Client::factory()->create([
        'email' => 'client@example.com',
        'phone_number' => null,
    ]);
    
    $document = Document::factory()->create([
        'client_id' => $client->id,
    ]);
    
    $file = UploadedFile::fake()->create('test.pdf', 100);
    $path = $file->store('uploads');
    
    $upload = Upload::create([
        'document_id' => $document->id,
        'file_path' => $path,
        'exp_date' => now()->addDays(5),
    ]);
    
    $client->notify(new DocumentExpiringNotification($upload));
    
    Notification::assertSentTo($client, DocumentExpiringNotification::class);
});

test('expiring notification is sent to client with email and phone', function () {
    Notification::fake();
    Storage::fake('local');
    
    $client = Client::factory()->create([
        'email' => 'client@example.com',
        'phone_number' => '+1234567890',
    ]);
    
    $document = Document::factory()->create([
        'client_id' => $client->id,
    ]);
    
    $file = UploadedFile::fake()->create('test.pdf', 100);
    $path = $file->store('uploads');
    
    $upload = Upload::create([
        'document_id' => $document->id,
        'file_path' => $path,
        'exp_date' => now()->addDays(3),
    ]);
    
    $client->notify(new DocumentExpiringNotification($upload));
    
    Notification::assertSentTo($client, DocumentExpiringNotification::class, function ($notification, $channels) {
        return in_array('mail', $channels) && in_array(\NotificationChannels\Twilio\TwilioChannel::class, $channels);
    });
});

test('send expiry notifications command processes expiring uploads', function () {
    Storage::fake('local');
    
    $client = Client::factory()->create([
        'email' => 'client@example.com',
        'phone_number' => '+1234567890',
    ]);
    
    $document = Document::factory()->create([
        'client_id' => $client->id,
        'name' => 'Test Document',
    ]);
    
    $file = UploadedFile::fake()->create('test.pdf', 100);
    $path = $file->store('uploads');
    
    // Upload expiring in 5 days
    Upload::create([
        'document_id' => $document->id,
        'file_path' => $path,
        'exp_date' => now()->addDays(5),
    ]);
    
    // Upload expiring in 10 days (outside default 7-day window)
    Upload::create([
        'document_id' => $document->id,
        'file_path' => $path,
        'exp_date' => now()->addDays(10),
    ]);
    
    Notification::fake();
    
    $this->artisan('documents:send-expiry-notifications')
        ->expectsOutput('Sent 1 notifications.')
        ->expectsOutput('Failed to send 0 notifications.')
        ->assertSuccessful();
    
    Notification::assertSentTo($client, DocumentExpiringNotification::class);
});

test('send expiry notifications command respects custom days option', function () {
    Storage::fake('local');
    
    $client = Client::factory()->create([
        'email' => 'client@example.com',
    ]);
    
    $document = Document::factory()->create([
        'client_id' => $client->id,
    ]);
    
    $file = UploadedFile::fake()->create('test.pdf', 100);
    $path = $file->store('uploads');
    
    // Upload expiring in 10 days
    Upload::create([
        'document_id' => $document->id,
        'file_path' => $path,
        'exp_date' => now()->addDays(10),
    ]);
    
    Notification::fake();
    
    // Should send with 14-day window
    $this->artisan('documents:send-expiry-notifications --days=14')
        ->expectsOutput('Sent 1 notifications.')
        ->assertSuccessful();
    
    Notification::assertSentTo($client, DocumentExpiringNotification::class);
});

test('send expiry notifications command skips clients without contact info', function () {
    Storage::fake('local');
    
    $client = Client::factory()->create([
        'email' => null,
        'phone_number' => null,
    ]);
    
    $document = Document::factory()->create([
        'client_id' => $client->id,
    ]);
    
    $file = UploadedFile::fake()->create('test.pdf', 100);
    $path = $file->store('uploads');
    
    Upload::create([
        'document_id' => $document->id,
        'file_path' => $path,
        'exp_date' => now()->addDays(5),
    ]);
    
    Notification::fake();
    
    $this->artisan('documents:send-expiry-notifications')
        ->expectsOutput('Sent 0 notifications.')
        ->assertSuccessful();
    
    Notification::assertNothingSent();
});
