<?php

namespace App\Http\Controllers;

use App\Services\ClientService;
use App\Services\DocumentService;
use App\Services\UploadService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected ClientService $clientService,
        protected DocumentService $documentService,
        protected UploadService $uploadService
    ) {}

    /**
     * Display the dashboard with statistics.
     */
    public function index(): Response
    {
        $totalClients = $this->clientService->all()->count();
        $totalDocuments = $this->documentService->all()->count();
        $expiringSoon = $this->uploadService->getExpiringSoon(30)->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalClients' => $totalClients,
                'totalDocuments' => $totalDocuments,
                'expiringSoon' => $expiringSoon,
            ],
        ]);
    }
}
