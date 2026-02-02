<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { FileText, Calendar, Bell, Users, Upload, CheckCircle } from 'lucide-vue-next';
import { dashboard, login, register } from '@/routes';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Document Expiry Management System" />
    
    <div class="min-h-screen bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800">
        <!-- Navigation -->
        <nav class="fixed top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-gray-200 dark:bg-gray-900/80 dark:border-gray-800">
            <div class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <FileText class="h-8 w-8 text-blue-600 dark:text-blue-400" />
                        <span class="text-xl font-bold text-gray-900 dark:text-white">DocExpiry</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="text-sm font-medium text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800 opacity-50" />
            <div class="container relative mx-auto px-6">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 mb-6 px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-medium">
                        <CheckCircle class="h-4 w-4" />
                        Never Miss a Document Expiration Again
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
                        Manage Document Expirations with
                        <span class="text-blue-600 dark:text-blue-400">Confidence</span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        Track, upload, and receive timely notifications for all your client documents. 
                        Stay organized and never let an important document expire.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link
                            v-if="!$page.props.auth.user"
                            :href="register()"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-8 py-4 text-base font-medium text-white hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                        >
                            <Upload class="h-5 w-5" />
                            Start Managing Documents
                        </Link>
                        <Link
                            v-else
                            :href="dashboard()"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-8 py-4 text-base font-medium text-white hover:bg-blue-700 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                        >
                            Go to Dashboard
                        </Link>
                    </div>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute top-20 left-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob" />
            <div class="absolute top-40 right-10 w-72 h-72 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000" />
            <div class="absolute -bottom-8 left-20 w-72 h-72 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000" />
        </section>

        <!-- Features Section -->
        <section class="py-20 bg-white dark:bg-gray-900">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Everything You Need to Stay Organized
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Powerful features designed to make document management effortless
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group relative p-8 rounded-2xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="mb-4 inline-flex p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                            <Users class="h-8 w-8" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Client Management
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Organize all your clients in one place with detailed contact information and document tracking.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group relative p-8 rounded-2xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="mb-4 inline-flex p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                            <FileText class="h-8 w-8" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Document Organization
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Create and categorize documents for each client with intuitive organization tools.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group relative p-8 rounded-2xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="mb-4 inline-flex p-3 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                            <Upload class="h-8 w-8" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Version Control
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Upload multiple versions of documents and track their expiration dates seamlessly.
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="group relative p-8 rounded-2xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="mb-4 inline-flex p-3 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400">
                            <Calendar class="h-8 w-8" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Expiration Tracking
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Set expiration dates for every document and see at-a-glance which ones need attention.
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="group relative p-8 rounded-2xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="mb-4 inline-flex p-3 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                            <Bell class="h-8 w-8" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Smart Notifications
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Receive SMS and email alerts when documents are approaching their expiration dates.
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="group relative p-8 rounded-2xl bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="mb-4 inline-flex p-3 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                            <CheckCircle class="h-8 w-8" />
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">
                            Status Management
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            Easily identify active, expiring soon, and expired documents with visual indicators.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-20 bg-gray-50 dark:bg-gray-800">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        How It Works
                    </h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                        Get started in minutes with our simple 3-step process
                    </p>
                </div>

                <div class="max-w-5xl mx-auto">
                    <div class="relative">
                        <!-- Connection Line -->
                        <div class="hidden md:block absolute left-1/2 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-200 to-purple-200 dark:from-blue-800 dark:to-purple-800 -translate-x-1/2" />

                        <!-- Step 1 -->
                        <div class="relative flex flex-col md:flex-row items-center mb-16 md:mb-24">
                            <div class="md:w-1/2 md:pr-16 mb-8 md:mb-0">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-600 text-white font-bold text-lg mb-4">
                                    1
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                    Add Your Clients
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Start by adding your clients with their contact information. Keep everything organized in one centralized location.
                                </p>
                            </div>
                            <div class="md:w-1/2 md:pl-16">
                                <div class="rounded-2xl bg-white dark:bg-gray-900 p-8 shadow-lg border border-gray-200 dark:border-gray-700">
                                    <Users class="h-16 w-16 text-blue-600 dark:text-blue-400 mb-4" />
                                    <div class="space-y-2">
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-3/4" />
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2" />
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative flex flex-col md:flex-row-reverse items-center mb-16 md:mb-24">
                            <div class="md:w-1/2 md:pl-16 mb-8 md:mb-0">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-purple-600 text-white font-bold text-lg mb-4">
                                    2
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                    Create Documents
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Create document categories for each client and upload files with expiration dates. Track multiple versions effortlessly.
                                </p>
                            </div>
                            <div class="md:w-1/2 md:pr-16">
                                <div class="rounded-2xl bg-white dark:bg-gray-900 p-8 shadow-lg border border-gray-200 dark:border-gray-700">
                                    <FileText class="h-16 w-16 text-purple-600 dark:text-purple-400 mb-4" />
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-2">
                                            <div class="h-8 w-8 rounded bg-purple-100 dark:bg-purple-900/30" />
                                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded flex-1" />
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="h-8 w-8 rounded bg-purple-100 dark:bg-purple-900/30" />
                                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded flex-1" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative flex flex-col md:flex-row items-center">
                            <div class="md:w-1/2 md:pr-16 mb-8 md:mb-0">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-600 text-white font-bold text-lg mb-4">
                                    3
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                                    Get Notified
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                                    Receive automatic notifications via SMS and email when documents are approaching expiration. Never miss a deadline.
                                </p>
                            </div>
                            <div class="md:w-1/2 md:pl-16">
                                <div class="rounded-2xl bg-white dark:bg-gray-900 p-8 shadow-lg border border-gray-200 dark:border-gray-700">
                                    <Bell class="h-16 w-16 text-green-600 dark:text-green-400 mb-4" />
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                            <div class="h-2 w-2 rounded-full bg-green-500" />
                                            <div class="h-2 bg-green-200 dark:bg-green-800 rounded flex-1" />
                                        </div>
                                        <div class="flex items-center gap-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                            <div class="h-2 w-2 rounded-full bg-yellow-500" />
                                            <div class="h-2 bg-yellow-200 dark:bg-yellow-800 rounded flex-1" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-br from-blue-600 to-purple-600 dark:from-blue-800 dark:to-purple-800">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    Ready to Take Control of Your Documents?
                </h2>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Join thousands of professionals who trust DocExpiry to manage their critical documents
                </p>
                <Link
                    v-if="!$page.props.auth.user"
                    :href="register()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-8 py-4 text-base font-medium text-blue-600 hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                >
                    <Upload class="h-5 w-5" />
                    Get Started for Free
                </Link>
                <Link
                    v-else
                    :href="dashboard()"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-white px-8 py-4 text-base font-medium text-blue-600 hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg hover:shadow-xl"
                >
                    Go to Dashboard
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
            <div class="container mx-auto px-6">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center gap-2 mb-4 md:mb-0">
                        <FileText class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        <span class="text-lg font-bold text-gray-900 dark:text-white">DocExpiry</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        © 2026 DocExpiry. Built with Laravel & Vue.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% { transform: translate(0px, 0px) scale(1); }
    33% { transform: translate(30px, -50px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
    100% { transform: translate(0px, 0px) scale(1); }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}
</style>
