<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { BookOpen, Code, Key, Lock, Server } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';

const page = usePage();
const baseUrl = computed(() => {
    const url = page.url;
    // Extract protocol and host from the current URL
    if (typeof window !== 'undefined') {
        return window.location.origin;
    }
    // Fallback for SSR
    return 'https://your-domain.com';
});

interface Endpoint {
    method: string;
    path: string;
    description: string;
    authentication: boolean;
    parameters?: { name: string; type: string; description: string; required: boolean }[];
    response?: string;
}

interface EndpointSection {
    title: string;
    endpoints: Endpoint[];
}

const sections: EndpointSection[] = [
    {
        title: 'Authentication',
        endpoints: [
            {
                method: 'GET',
                path: '/api/user',
                description: 'Get the authenticated user information',
                authentication: true,
                response: `{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-02-02T00:00:00.000000Z"
}`,
            },
        ],
    },
    {
        title: 'Clients',
        endpoints: [
            {
                method: 'GET',
                path: '/api/clients',
                description: 'List all clients',
                authentication: true,
                response: `{
    "data": [
        {
            "id": 1,
            "name": "Acme Corp",
            "email": "contact@acme.com",
            "phone_number": "+1234567890",
            "address": "123 Main St",
            "created_at": "2026-02-02T00:00:00.000000Z"
        }
    ]
}`,
            },
            {
                method: 'POST',
                path: '/api/clients',
                description: 'Create a new client',
                authentication: true,
                parameters: [
                    { name: 'name', type: 'string', description: 'Client name', required: true },
                    { name: 'email', type: 'string', description: 'Client email', required: false },
                    { name: 'phone_number', type: 'string', description: 'Client phone number', required: false },
                    { name: 'address', type: 'text', description: 'Client address', required: false },
                ],
                response: `{
    "data": {
        "id": 1,
        "name": "Acme Corp",
        "email": "contact@acme.com",
        "phone_number": "+1234567890",
        "address": "123 Main St",
        "created_at": "2026-02-02T00:00:00.000000Z"
    }
}`,
            },
            {
                method: 'GET',
                path: '/api/clients/{client}',
                description: 'Get a specific client',
                authentication: true,
                parameters: [
                    { name: 'client', type: 'integer', description: 'Client ID', required: true },
                ],
                response: `{
    "data": {
        "id": 1,
        "name": "Acme Corp",
        "email": "contact@acme.com",
        "phone_number": "+1234567890",
        "address": "123 Main St",
        "created_at": "2026-02-02T00:00:00.000000Z"
    }
}`,
            },
            {
                method: 'PUT',
                path: '/api/clients/{client}',
                description: 'Update a client',
                authentication: true,
                parameters: [
                    { name: 'client', type: 'integer', description: 'Client ID', required: true },
                    { name: 'name', type: 'string', description: 'Client name', required: false },
                    { name: 'email', type: 'string', description: 'Client email', required: false },
                    { name: 'phone_number', type: 'string', description: 'Client phone number', required: false },
                    { name: 'address', type: 'text', description: 'Client address', required: false },
                ],
            },
            {
                method: 'DELETE',
                path: '/api/clients/{client}',
                description: 'Delete a client',
                authentication: true,
                parameters: [
                    { name: 'client', type: 'integer', description: 'Client ID', required: true },
                ],
            },
        ],
    },
    {
        title: 'Documents',
        endpoints: [
            {
                method: 'GET',
                path: '/api/clients/{client}/documents',
                description: "List all documents for a specific client",
                authentication: true,
                parameters: [
                    { name: 'client', type: 'integer', description: 'Client ID', required: true },
                ],
                response: `{
    "data": [
        {
            "id": 1,
            "name": "Passport",
            "client_id": 1,
            "created_at": "2026-02-02T00:00:00.000000Z"
        }
    ]
}`,
            },
            {
                method: 'POST',
                path: '/api/clients/{client}/documents',
                description: 'Create a new document for a client',
                authentication: true,
                parameters: [
                    { name: 'client', type: 'integer', description: 'Client ID', required: true },
                    { name: 'name', type: 'string', description: 'Document name', required: true },
                    { name: 'file', type: 'file', description: 'Optional file upload for first upload', required: false },
                    { name: 'exp_date', type: 'date', description: 'Expiration date (required if file provided)', required: false },
                ],
            },
            {
                method: 'GET',
                path: '/api/documents/{document}',
                description: 'Get a specific document with uploads',
                authentication: true,
                parameters: [
                    { name: 'document', type: 'integer', description: 'Document ID', required: true },
                ],
                response: `{
    "data": {
        "id": 1,
        "name": "Passport",
        "client_id": 1,
        "uploads": [
            {
                "id": 1,
                "file_path": "uploads/abc123.pdf",
                "exp_date": "2027-02-02",
                "created_at": "2026-02-02T00:00:00.000000Z"
            }
        ],
        "created_at": "2026-02-02T00:00:00.000000Z"
    }
}`,
            },
            {
                method: 'PUT',
                path: '/api/documents/{document}',
                description: 'Update a document',
                authentication: true,
                parameters: [
                    { name: 'document', type: 'integer', description: 'Document ID', required: true },
                    { name: 'name', type: 'string', description: 'Document name', required: false },
                ],
            },
            {
                method: 'DELETE',
                path: '/api/documents/{document}',
                description: 'Delete a document and all its uploads',
                authentication: true,
                parameters: [
                    { name: 'document', type: 'integer', description: 'Document ID', required: true },
                ],
            },
        ],
    },
    {
        title: 'Uploads',
        endpoints: [
            {
                method: 'GET',
                path: '/api/documents/{document}/uploads',
                description: 'List all uploads for a specific document',
                authentication: true,
                parameters: [
                    { name: 'document', type: 'integer', description: 'Document ID', required: true },
                ],
                response: `{
    "data": [
        {
            "id": 1,
            "document_id": 1,
            "file_path": "uploads/abc123.pdf",
            "exp_date": "2027-02-02",
            "created_at": "2026-02-02T00:00:00.000000Z"
        }
    ]
}`,
            },
            {
                method: 'POST',
                path: '/api/documents/{document}/uploads',
                description: 'Upload a new file to a document',
                authentication: true,
                parameters: [
                    { name: 'document', type: 'integer', description: 'Document ID', required: true },
                    { name: 'file', type: 'file', description: 'File to upload (max 10MB)', required: true },
                    { name: 'exp_date', type: 'date', description: 'Expiration date (YYYY-MM-DD)', required: true },
                ],
            },
            {
                method: 'GET',
                path: '/api/uploads/{upload}',
                description: 'Get a specific upload',
                authentication: true,
                parameters: [
                    { name: 'upload', type: 'integer', description: 'Upload ID', required: true },
                ],
            },
            {
                method: 'PUT',
                path: '/api/uploads/{upload}',
                description: 'Update an upload expiration date',
                authentication: true,
                parameters: [
                    { name: 'upload', type: 'integer', description: 'Upload ID', required: true },
                    { name: 'exp_date', type: 'date', description: 'New expiration date', required: true },
                ],
            },
            {
                method: 'DELETE',
                path: '/api/uploads/{upload}',
                description: 'Delete an upload and its file',
                authentication: true,
                parameters: [
                    { name: 'upload', type: 'integer', description: 'Upload ID', required: true },
                ],
            },
        ],
    },
];

const getMethodColor = (method: string) => {
    const colors: Record<string, string> = {
        GET: 'bg-blue-500',
        POST: 'bg-green-500',
        PUT: 'bg-yellow-500',
        DELETE: 'bg-red-500',
    };
    return colors[method] || 'bg-gray-500';
};
</script>

<template>
    <Head title="API Documentation" />

    <AppLayout>
        <div class="mx-auto max-w-5xl space-y-6 p-6">
            <!-- Header -->
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <Code class="h-8 w-8" />
                    <h1 class="text-3xl font-bold">API Documentation</h1>
                </div>
                <p class="text-muted-foreground">
                    RESTful API for managing clients, documents, and file uploads with expiration tracking.
                </p>
            </div>

            <!-- Authentication Info -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Lock class="h-5 w-5" />
                        Authentication
                    </CardTitle>
                    <CardDescription>
                        All API endpoints require authentication using Laravel Sanctum
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <h4 class="mb-2 font-semibold">Getting Started</h4>
                        <ol class="ml-4 list-decimal space-y-2 text-sm">
                            <li>Create an account and login through the web interface</li>
                            <li>Generate an API token from your profile settings</li>
                            <li>Include the token in your requests as a Bearer token</li>
                        </ol>
                    </div>
                    <Separator />
                    <div>
                        <h4 class="mb-2 font-semibold">Example Request</h4>
                        <pre
                            class="rounded-lg bg-muted p-4 text-sm"><code>curl -X GET {{ baseUrl }}/api/clients \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Accept: application/json"</code></pre>
                    </div>
                </CardContent>
            </Card>

            <!-- Base URL -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Server class="h-5 w-5" />
                        Base URL
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <code class="rounded bg-muted px-2 py-1">{{ baseUrl }}/api</code>
                </CardContent>
            </Card>

            <!-- Endpoints -->
            <div v-for="section in sections" :key="section.title" class="space-y-4">
                <h2 class="text-2xl font-bold">{{ section.title }}</h2>

                <Card v-for="endpoint in section.endpoints" :key="endpoint.path">
                    <CardHeader>
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <Badge :class="getMethodColor(endpoint.method)" class="text-white">
                                        {{ endpoint.method }}
                                    </Badge>
                                    <code class="text-sm">{{ endpoint.path }}</code>
                                    <Lock v-if="endpoint.authentication" class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <CardDescription>{{ endpoint.description }}</CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Parameters -->
                        <div v-if="endpoint.parameters && endpoint.parameters.length > 0">
                            <h4 class="mb-2 font-semibold">Parameters</h4>
                            <div class="space-y-2">
                                <div
                                    v-for="param in endpoint.parameters"
                                    :key="param.name"
                                    class="flex items-start gap-4 rounded-lg border p-3"
                                >
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <code class="text-sm font-medium">{{ param.name }}</code>
                                            <Badge v-if="param.required" variant="destructive" class="text-xs">
                                                required
                                            </Badge>
                                            <Badge v-else variant="secondary" class="text-xs">optional</Badge>
                                        </div>
                                        <p class="mt-1 text-sm text-muted-foreground">{{ param.description }}</p>
                                    </div>
                                    <Badge variant="outline" class="text-xs">{{ param.type }}</Badge>
                                </div>
                            </div>
                        </div>

                        <!-- Response Example -->
                        <div v-if="endpoint.response">
                            <h4 class="mb-2 font-semibold">Response Example</h4>
                            <pre class="overflow-x-auto rounded-lg bg-muted p-4 text-sm"><code>{{ endpoint.response }}</code></pre>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Additional Info -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <BookOpen class="h-5 w-5" />
                        Additional Information
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div>
                        <h4 class="mb-2 font-semibold">Response Format</h4>
                        <p class="text-sm text-muted-foreground">
                            All responses are returned in JSON format. Resource responses are wrapped in a
                            <code class="rounded bg-muted px-1">data</code> object.
                        </p>
                    </div>
                    <Separator />
                    <div>
                        <h4 class="mb-2 font-semibold">Error Handling</h4>
                        <p class="text-sm text-muted-foreground">
                            Errors return appropriate HTTP status codes (4xx for client errors, 5xx for server errors)
                            with a JSON response containing a <code class="rounded bg-muted px-1">message</code> field.
                        </p>
                    </div>
                    <Separator />
                    <div>
                        <h4 class="mb-2 font-semibold">File Uploads</h4>
                        <p class="text-sm text-muted-foreground">
                            File uploads must use <code class="rounded bg-muted px-1">multipart/form-data</code>
                            encoding. Maximum file size is 10MB.
                        </p>
                    </div>
                    <Separator />
                    <div>
                        <h4 class="mb-2 font-semibold">Notifications</h4>
                        <p class="text-sm text-muted-foreground">
                            The system automatically sends email and SMS notifications (via Twilio) to clients when
                            documents are expiring within 30 days. Notifications run daily at 9 AM.
                        </p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
