<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Users, FileText, AlertCircle } from 'lucide-vue-next';

type Props = {
    stats: {
        totalClients: number;
        totalDocuments: number;
        expiringSoon: number;
    };
};

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Dashboard Overview
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Monitor your documents and client information
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <!-- Total Clients Card -->
                <div
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Total Clients
                            </p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ stats.totalClients }}
                            </p>
                        </div>
                        <div
                            class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/30"
                        >
                            <Users class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </div>

                <!-- Total Documents Card -->
                <div
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Total Documents
                            </p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ stats.totalDocuments }}
                            </p>
                        </div>
                        <div
                            class="rounded-full bg-purple-100 p-3 dark:bg-purple-900/30"
                        >
                            <FileText class="h-8 w-8 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </div>

                <!-- Expiring Soon Card -->
                <div
                    class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                Expiring Soon
                            </p>
                            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ stats.expiringSoon }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                Within 30 days
                            </p>
                        </div>
                        <div
                            :class="[
                                'rounded-full p-3',
                                stats.expiringSoon > 0
                                    ? 'bg-orange-100 dark:bg-orange-900/30'
                                    : 'bg-green-100 dark:bg-green-900/30',
                            ]"
                        >
                            <AlertCircle
                                :class="[
                                    'h-8 w-8',
                                    stats.expiringSoon > 0
                                        ? 'text-orange-600 dark:text-orange-400'
                                        : 'text-green-600 dark:text-green-400',
                                ]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
