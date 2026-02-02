<script setup lang="ts">
import { Form, Head, Link, router } from '@inertiajs/vue3';
import { FileText, Upload as UploadIcon, Calendar, Trash2, ArrowLeft } from 'lucide-vue-next';
import UploadController from '@/actions/App/Http/Controllers/UploadController';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { type BreadcrumbItem } from '@/types';
import { index, show as showRoute } from '@/routes/documents';

type Client = {
    id: number;
    name: string;
    email: string | null;
};

type Upload = {
    id: number;
    file_path: string;
    exp_date: string;
    created_at: string;
};

type Document = {
    id: number;
    name: string;
    client_id: number;
    client: Client;
    uploads: Upload[];
};

type Props = {
    document: Document;
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Documents',
        href: index().url,
    },
    {
        title: props.document.name,
        href: showRoute(props.document.id).url,
    },
];

function deleteUpload(upload: Upload) {
    if (confirm('Are you sure you want to delete this upload?')) {
        router.delete(`/uploads/${upload.id}`);
    }
}

function isExpiringSoon(expDate: string): boolean {
    const date = new Date(expDate);
    const today = new Date();
    const daysUntilExpiry = Math.floor((date.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
    return daysUntilExpiry <= 30 && daysUntilExpiry >= 0;
}

function isExpired(expDate: string): boolean {
    const date = new Date(expDate);
    const today = new Date();
    return date < today;
}

function getExpiryBadgeVariant(expDate: string): 'default' | 'secondary' | 'destructive' {
    if (isExpired(expDate)) return 'destructive';
    if (isExpiringSoon(expDate)) return 'secondary';
    return 'default';
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="props.document.name" />

        <div class="space-y-6">
            <!-- Document Header -->
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="outline" size="icon" as-child>
                        <Link :href="index().url">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight flex items-center gap-3">
                            <FileText class="h-8 w-8" />
                            {{ props.document.name }}
                        </h1>
                        <p class="text-muted-foreground mt-1">
                            Client: {{ props.document.client.name }}
                            <span v-if="props.document.client.email">
                                ({{ props.document.client.email }})
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <Separator />

            <!-- Upload Form -->
            <Card>
                <CardHeader>
                    <CardTitle>Upload New Version</CardTitle>
                    <CardDescription>
                        Upload a new version of this document with an expiration date
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Form
                        v-bind="UploadController.store.form(props.document.id)"
                        enctype="multipart/form-data"
                        class="space-y-4"
                    >
                        <div class="space-y-2">
                            <Label for="file" required>File</Label>
                            <Input
                                id="file"
                                name="file"
                                type="file"
                                required
                            />
                            <InputError name="file" />
                            <p class="text-xs text-muted-foreground">
                                Maximum file size: 10MB
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="exp_date" required>Expiration Date</Label>
                            <Input
                                id="exp_date"
                                name="exp_date"
                                type="date"
                                required
                            />
                            <InputError name="exp_date" />
                        </div>

                        <Button type="submit">
                            <UploadIcon class="mr-2 h-4 w-4" />
                            Upload Document
                        </Button>
                    </Form>
                </CardContent>
            </Card>

            <!-- Uploads List -->
            <div>
                <Heading
                    variant="small"
                    title="Document Uploads"
                    :description="`${props.document.uploads?.length || 0} upload(s)`"
                />

                <div v-if="props.document.uploads?.length === 0" class="text-center py-12 border rounded-lg">
                    <p class="text-muted-foreground">No uploads yet</p>
                </div>

                <div v-else class="grid gap-4 mt-4">
                    <Card v-for="upload in props.document.uploads" :key="upload.id">
                        <CardContent class="pt-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <FileText class="h-4 w-4 text-muted-foreground" />
                                        <span class="font-mono text-sm">
                                            {{ upload.file_path.split('/').pop() }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                        <span class="flex items-center gap-1">
                                            <Calendar class="h-3 w-3" />
                                            Expires: {{ upload.exp_date }}
                                        </span>
                                        <Badge :variant="getExpiryBadgeVariant(upload.exp_date)">
                                            <span v-if="isExpired(upload.exp_date)">Expired</span>
                                            <span v-else-if="isExpiringSoon(upload.exp_date)">Expiring Soon</span>
                                            <span v-else>Active</span>
                                        </Badge>
                                    </div>
                                    <p class="text-xs text-muted-foreground">
                                        Uploaded: {{ new Date(upload.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    @click="deleteUpload(upload)"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
