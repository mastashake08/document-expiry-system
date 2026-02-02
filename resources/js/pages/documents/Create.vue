<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import DocumentController from '@/actions/App/Http/Controllers/DocumentController';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { type BreadcrumbItem } from '@/types';
import { index, create as createRoute } from '@/routes/documents';

type Client = {
    id: number;
    name: string;
};

type Props = {
    clients: Client[];
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Documents',
        href: index().url,
    },
    {
        title: 'Create',
        href: createRoute().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Create Document" />

        <div class="max-w-2xl">
            <Heading
                title="Create Document"
                description="Add a new document for a client"
            />

            <Form v-bind="DocumentController.store.form()" class="space-y-6" enctype="multipart/form-data">
                <div class="space-y-2">
                    <Label for="client_id" required>Client</Label>
                    <Select name="client_id" required>
                        <SelectTrigger>
                            <SelectValue placeholder="Select a client" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="client in props.clients"
                                :key="client.id"
                                :value="client.id.toString()"
                            >
                                {{ client.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError name="client_id" />
                </div>

                <div class="space-y-2">
                    <Label for="name" required>Document Name</Label>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        required
                        autofocus
                    />
                    <InputError name="name" />
                </div>

                <div class="rounded-lg border border-gray-200 p-4 space-y-4 dark:border-gray-800">
                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">First Upload (Optional)</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Add the first version of this document now, or add it later.</p>
                    
                    <div class="space-y-2">
                        <Label for="file">File</Label>
                        <Input
                            id="file"
                            name="file"
                            type="file"
                        />
                        <InputError name="file" />
                        <p class="text-xs text-gray-500 dark:text-gray-400">Maximum file size: 10MB</p>
                    </div>

                    <div class="space-y-2">
                        <Label for="exp_date">Expiration Date</Label>
                        <Input
                            id="exp_date"
                            name="exp_date"
                            type="date"
                            :min="new Date().toISOString().split('T')[0]"
                        />
                        <InputError name="exp_date" />
                        <p class="text-xs text-gray-500 dark:text-gray-400">Required if file is provided</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit">Create Document</Button>
                    <Button variant="outline" as-child>
                        <Link :href="index().url">Cancel</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
