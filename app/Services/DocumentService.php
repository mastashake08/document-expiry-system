<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Database\Eloquent\Collection;

class DocumentService
{
    /**
     * Get all documents.
     */
    public function all(): Collection
    {
        return Document::with('client', 'uploads')->get();
    }

    /**
     * Get documents for a specific client.
     */
    public function forClient(int $clientId): Collection
    {
        return Document::where('client_id', $clientId)
            ->with('uploads')
            ->get();
    }

    /**
     * Create a new document.
     */
    public function create(array $data): Document
    {
        return Document::create($data);
    }

    /**
     * Create a new document with optional first upload.
     */
    public function createWithUpload(array $data, ?\Illuminate\Http\UploadedFile $file = null, ?string $expDate = null): Document
    {
        $document = Document::create([
            'client_id' => $data['client_id'],
            'name' => $data['name'],
        ]);

        if ($file && $expDate) {
            $uploadService = app(UploadService::class);
            $uploadService->create($document, $file, $expDate);
        }

        return $document->load('uploads');
    }

    /**
     * Find a document by ID.
     */
    public function find(int $id): ?Document
    {
        return Document::with('client', 'uploads')->find($id);
    }

    /**
     * Update a document.
     */
    public function update(Document $document, array $data): Document
    {
        $document->update($data);

        return $document->fresh(['client', 'uploads']);
    }

    /**
     * Delete a document.
     */
    public function delete(Document $document): bool
    {
        return $document->delete();
    }
}
