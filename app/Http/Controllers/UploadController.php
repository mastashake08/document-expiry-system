<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUploadRequest;
use App\Models\Document;
use App\Models\Upload;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;

class UploadController extends Controller
{
    public function __construct(
        protected UploadService $uploadService
    ) {}

    /**
     * Store a newly created upload.
     */
    public function store(StoreUploadRequest $request, Document $document): RedirectResponse
    {
        $this->uploadService->create(
            $document,
            $request->file('file'),
            $request->input('exp_date')
        );

        return to_route('documents.show', $document);
    }

    /**
     * Remove the specified upload.
     */
    public function destroy(Upload $upload): RedirectResponse
    {
        $document = $upload->document;
        $this->uploadService->delete($upload);

        return to_route('documents.show', $document);
    }
}
