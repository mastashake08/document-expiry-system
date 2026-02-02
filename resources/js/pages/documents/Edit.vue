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
import { index, edit as editRoute } from '@/routes/documents';

type Client = {
    id: number;
    name: string;
};

type Document = {
    id: number;
    name: string;
    client_id: number;
    client: Client;
};

type Props = {
    document: Document;
    clients: Client[];
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Documents',
        href: index().url,
    },
    {
        title: props.document.name,
        href: editRoute(props.document.id).url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Edit ${props.document.name}`" />

        <div class="max-w-2xl">
            <Heading
                title="Edit Document"
                :description="`Update ${props.document.name}'s information`"
            />

            <Form
                v-bind="DocumentController.update.form(props.document.id)"
                class="space-y-6"
            >
                <div class="space-y-2">
                    <Label for="client_id" required>Client</Label>
                    <Select
                        name="client_id"
                        :model-value="props.document.client_id.toString()"
                        required
                    >
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
                        :model-value="props.document.name"
                        required
                        autofocus
                    />
                    <InputError name="name" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit">Update Document</Button>
                    <Button variant="outline" as-child>
                        <Link :href="index().url">Cancel</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
