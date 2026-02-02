<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Mail, Phone, MapPin, Plus, Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { index, create as createRoute, edit } from '@/routes/clients';

type Client = {
    id: number;
    name: string;
    email: string | null;
    phone_number: string | null;
    address: string | null;
    created_at: string;
    updated_at: string;
};

type Props = {
    clients: Client[];
};

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Clients',
        href: index().url,
    },
];

function deleteClient(client: Client) {
    if (confirm(`Are you sure you want to delete ${client.name}?`)) {
        router.delete(edit(client.id).url);
    }
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Clients" />

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Clients</h1>
                <p class="text-muted-foreground">
                    Manage your client information
                </p>
            </div>
            <Button as-child>
                <Link :href="createRoute().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Add Client
                </Link>
            </Button>
        </div>

        <div v-if="props.clients.length === 0" class="text-center py-12">
            <p class="text-muted-foreground mb-4">No clients found</p>
            <Button as-child>
                <Link :href="createRoute().url">
                    <Plus class="mr-2 h-4 w-4" />
                    Add your first client
                </Link>
            </Button>
        </div>

        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="client in props.clients" :key="client.id">
                <CardHeader>
                    <div class="flex items-start justify-between">
                        <div>
                            <CardTitle>{{ client.name }}</CardTitle>
                            <CardDescription v-if="client.email">
                                <Mail class="inline h-3 w-3 mr-1" />
                                {{ client.email }}
                            </CardDescription>
                        </div>
                        <div class="flex gap-2">
                            <Button
                                variant="ghost"
                                size="icon"
                                as-child
                            >
                                <Link :href="edit(client.id).url">
                                    <Pencil class="h-4 w-4" />
                                </Link>
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                @click="deleteClient(client)"
                            >
                                <Trash2 class="h-4 w-4 text-destructive" />
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-if="client.phone_number" class="flex items-center text-sm">
                        <Phone class="h-4 w-4 mr-2 text-muted-foreground" />
                        <span>{{ client.phone_number }}</span>
                    </div>
                    <div v-if="client.address" class="flex items-start text-sm">
                        <MapPin class="h-4 w-4 mr-2 mt-0.5 text-muted-foreground flex-shrink-0" />
                        <span class="line-clamp-2">{{ client.address }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
