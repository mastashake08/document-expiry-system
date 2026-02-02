<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Client;
use App\Models\Document;
use App\Services\ClientService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $documentService,
        protected ClientService $clientService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $documents = $this->documentService->all();

        return Inertia::render('documents/Index', [
            'documents' => $documents,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $clients = $this->clientService->all();

        return Inertia::render('documents/Create', [
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        $this->documentService->createWithUpload(
            $validated,
            $request->file('file'),
            $validated['exp_date'] ?? null
        );

        return to_route('documents.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document): Response
    {
        $document->load('client', 'uploads');

        return Inertia::render('documents/Show', [
            'document' => $document,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Document $document): Response
    {
        $document->load('client');
        $clients = $this->clientService->all();

        return Inertia::render('documents/Edit', [
            'document' => $document,
            'clients' => $clients,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->documentService->update($document, $request->validated());

        return to_route('documents.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document): RedirectResponse
    {
        $this->documentService->delete($document);

        return to_route('documents.index');
    }
}
