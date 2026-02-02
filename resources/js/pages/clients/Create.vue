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
import { index, create as createRoute } from '@/routes/clients';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Clients',
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
        <Head title="Create Client" />

        <div class="max-w-2xl">
            <Heading
                title="Create Client"
                description="Add a new client to your system"
            />

            <Form v-bind="ClientController.store.form()" class="space-y-6">
                <div class="space-y-2">
                    <Label for="name" required>Name</Label>
                    <Input
                        id="name"
                        name="name"
                        type="text"
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
                    />
                    <InputError name="email" />
                </div>

                <div class="space-y-2">
                    <Label for="phone_number">Phone Number</Label>
                    <Input
                        id="phone_number"
                        name="phone_number"
                        type="text"
                    />
                    <InputError name="phone_number" />
                </div>

                <div class="space-y-2">
                    <Label for="address">Address</Label>
                    <Textarea
                        id="address"
                        name="address"
                        rows="3"
                    />
                    <InputError name="address" />
                </div>

                <div class="flex items-center gap-4">
                    <Button type="submit">Create Client</Button>
                    <Button variant="outline" as-child>
                        <Link :href="index().url">Cancel</Link>
                    </Button>
                </div>
            </Form>
        </div>
    </AppLayout>
</template>
