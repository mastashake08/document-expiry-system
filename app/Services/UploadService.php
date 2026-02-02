<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Upload;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Get all uploads for a document.
     */
    public function forDocument(int $documentId): Collection
    {
        return Upload::where('document_id', $documentId)
            ->orderBy('exp_date', 'desc')
            ->get();
    }

    /**
     * Create a new upload.
     */
    public function create(Document $document, UploadedFile $file, string $expDate): Upload
    {
        $path = $file->store('documents', 'local');

        return Upload::create([
            'document_id' => $document->id,
            'file_path' => $path,
            'exp_date' => $expDate,
        ]);
    }

    /**
     * Find an upload by ID.
     */
    public function find(int $id): ?Upload
    {
        return Upload::with('document.client')->find($id);
    }

    /**
     * Update an upload.
     */
    public function update(Upload $upload, array $data): Upload
    {
        $upload->update($data);

        return $upload->fresh('document');
    }

    /**
     * Delete an upload and its file.
     */
    public function delete(Upload $upload): bool
    {
        if (Storage::disk('local')->exists($upload->file_path)) {
            Storage::disk('local')->delete($upload->file_path);
        }

        return $upload->delete();
    }

    /**
     * Get uploads that are expiring soon.
     */
    public function getExpiringSoon(int $days = 30): Collection
    {
        return Upload::with('document.client')
            ->whereBetween('exp_date', [now(), now()->addDays($days)])
            ->orderBy('exp_date', 'asc')
            ->get();
    }
}
