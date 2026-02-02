<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateUploadRequest;
use App\Http\Resources\UploadResource;
use App\Models\Document;
use App\Models\Upload;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UploadController extends Controller
{
    public function __construct(
        protected UploadService $uploadService
    ) {}

    /**
     * Display a listing of uploads for a document.
     */
    public function index(Document $document): AnonymousResourceCollection
    {
        $uploads = $this->uploadService->forDocument($document->id);

        return UploadResource::collection($uploads);
    }

    /**
     * Store a newly created upload.
     */
    public function store(StoreUploadRequest $request, Document $document): UploadResource
    {
        $upload = $this->uploadService->create(
            $document,
            $request->file('file'),
            $request->input('exp_date')
        );

        return new UploadResource($upload);
    }

    /**
     * Display the specified upload.
     */
    public function show(Upload $upload): UploadResource
    {
        $upload->load('document.client');

        return new UploadResource($upload);
    }

    /**
     * Update the specified upload.
     */
    public function update(UpdateUploadRequest $request, Upload $upload): UploadResource
    {
        $upload = $this->uploadService->update($upload, $request->validated());

        return new UploadResource($upload);
    }

    /**
     * Remove the specified upload.
     */
    public function destroy(Upload $upload): JsonResponse
    {
        $this->uploadService->delete($upload);

        return response()->json(null, 204);
    }
}
