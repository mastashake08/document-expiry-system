<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import AppLayout from '@/layouts/AppLayout.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { type BreadcrumbItem } from '@/types';
import { index, edit as editRoute } from '@/routes/clients';

type Client = {
    id: number;
    name: string;
    email: string | null;
    phone_number: string | null;
    address: string | null;
};

type Props = {
    client: Client;
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Clients',
        href: index().url,
    },
    {
        title: props.client.name,
        href: editRoute(props.client.id).url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Edit ${props.client.name}`" />

        <div class="max-w-2xl">
            <Heading
                title="Edit Client"
                :description="`Update ${props.client.name}'s information`"
            />

            <Form
                v-bind="ClientController.update.form(props.client.id)"
                class="space-y-6"
            >
                <div class="space-y-2">
                    <Label for="name" required>Name</Label>
                    <Input
                        id="name"
                        name="name"
                        type="text"
                        :model-value="props.client.name"
                        required
                        autofocus
                    />
                    <InputError name="name" />
                </div>

                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        :model-value="props.client.email"
                    />
                    <InputError name="email" />
                </div>

                <div class="space-y-2">
                    <Label for="phone_number">Phone Number</Label>
                    <Input
                        id="phone_number"
                        name="phone_number"
                        type="text"
                        :model-value="props.client.phone_number"
                    />
                    <InputError name="phone_number" />
                </div>

                <div class="space-y-2">
                    <Label for="address">Address</Label>
                    <Textarea
                        id="address"
                        name="address"
                        rows="3"
                        :model-value="props.client.address"
                    />
                    <InputError name="address" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit">Update Client</Button>
                    <Button variant="outline" as-child>
                        <Link :href="index().url">Cancel</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
