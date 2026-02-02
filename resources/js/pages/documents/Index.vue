<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { FileText, Plus, Pencil, Trash2, Eye } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { type BreadcrumbItem } from '@/types';
import { index, create as createRoute, edit, show } from '@/routes/documents';

type Client = {
    id: number;
    name: string;
};

type Upload = {
    id: number;
    exp_date: string;
};

type Document = {
    id: number;
    name: string;
    client_id: number;
    client: Client;
    uploads: Upload[];
    created_at: string;
    updated_at: string;
};

type Props = {
    documents: Document[];
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Documents',
        href: index().url,
    },
];

function deleteDocument(document: Document) {
    if (confirm(`Are you sure you want to delete ${document.name}?`)) {
        router.delete(edit(document.id).url);
    }
}

function getLatestUpload(document: Document): Upload | null {
    if (!document.uploads || document.uploads.length === 0) return null;
    return document.uploads[0];
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Documents" />

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Documents</h1>
                <p class="text-muted-foreground">
                    Manage client documents and their uploads
                </p>
            </div>
            <Button as-child>
                <Link :href="createRoute().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Document
                </Link>
            </Button>
        </div>

        <div v-if="props.documents.length === 0" class="text-center py-12">
            <p class="text-muted-foreground mb-4">No documents found</p>
            <Button as-child>
                <Link :href="createRoute().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Add your first document
                </Link>
            </Button>
        </div>

        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="document in props.documents" :key="document.id">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <CardTitle class="flex items-center gap-2">
                                <FileText class="h-5 w-5" />
                                {{ document.name }}
                            </CardTitle>
                            <CardDescription>
                                Client: {{ document.client.name }}
                            </CardDescription>
                        </div>
                        <div class="flex gap-2">
                            <Button
                                variant="ghost"
                                size="icon"
                                as-child
                            >
                                <Link :href="show(document.id).url">
                                    <Eye class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                as-child
                            >
                                <Link :href="edit(document.id).url">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                @click="deleteDocument(document)"
                            >
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-muted-foreground">Uploads:</span>
                        <Badge variant="secondary">
                            {{ document.uploads?.length || 0 }}
                        </Badge>
                    </div>
                    <div v-if="getLatestUpload(document)" class="mt-2 text-sm">
                        <span class="text-muted-foreground">Latest expires:</span>
                        <span class="ml-2">{{ getLatestUpload(document)?.exp_date }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
