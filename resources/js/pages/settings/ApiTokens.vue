<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { Copy, Key, Plus, Trash2 } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy } from '@/routes/settings/api_tokens';

interface Token {
    id: number;
    name: string;
    last_used_at: string | null;
    created_at: string;
}

interface Props {
    tokens: Token[];
}

const props = defineProps<Props>();

const page = usePage();
const flash = computed(() => page.props.flash as { token?: string; message?: string } | null);
const status = computed(() => page.props.status as string | null);

const showCreateDialog = ref(false);
const showTokenDialog = ref(false);
const newToken = ref('');

const form = useForm({
    name: '',
});

const createToken = () => {
    form.post('/settings/api-tokens', {
        preserveScroll: true,
        onSuccess: () => {
            if (flash.value?.token) {
                newToken.value = flash.value.token;
                showTokenDialog.value = true;
            }
            form.reset();
            showCreateDialog.value = false;
        },
    });
};

const deleteToken = (tokenId: number) => {
    if (confirm('Are you sure you want to delete this API token? This action cannot be undone.')) {
        useForm({}).delete(destroy(tokenId), {
            preserveScroll: true,
        });
    }
};

const copyToken = async () => {
    try {
        await navigator.clipboard.writeText(newToken.value);
    } catch (err) {
        console.error('Failed to copy token:', err);
    }
};

const closeTokenDialog = () => {
    showTokenDialog.value = false;
    newToken.value = '';
};
</script>

<template>
    <Head title="API Tokens" />

    <AppLayout>
        <div class="mx-auto max-w-3xl space-y-6 p-6">
            <div>
                <Heading>API Tokens</Heading>
                <p class="mt-1 text-sm text-muted-foreground">
                    Create and manage API tokens for authenticating with the API
                </p>
            </div>

            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Manage API Tokens</CardTitle>
                            <CardDescription>
                                API tokens allow third-party services to authenticate with our application
                            </CardDescription>
                        </div>
                        <Button @click="showCreateDialog = true">
                            <Plus class="mr-2 h-4 w-4" />
                            Create Token
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="status" class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-800 dark:bg-green-900/20 dark:text-green-300">
                        {{ status }}
                    </div>

                    <div v-if="props.tokens.length === 0" class="py-8 text-center text-sm text-muted-foreground">
                        <Key class="mx-auto mb-2 h-12 w-12 opacity-50" />
                        <p>You haven't created any API tokens yet.</p>
                        <p>Click "Create Token" to get started.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            v-for="token in props.tokens"
                            :key="token.id"
                            class="flex items-center justify-between rounded-lg border p-4"
                        >
                            <div class="flex-1">
                                <h4 class="font-medium">{{ token.name }}</h4>
                                <div class="mt-1 flex items-center gap-4 text-sm text-muted-foreground">
                                    <span>Created {{ token.created_at }}</span>
                                    <Separator orientation="vertical" class="h-4" />
                                    <span v-if="token.last_used_at">
                                        Last used {{ token.last_used_at }}
                                    </span>
                                    <span v-else>Never used</span>
                                </div>
                            </div>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="deleteToken(token.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Create Token Dialog -->
            <Dialog v-model:open="showCreateDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Create API Token</DialogTitle>
                        <DialogDescription>
                            Enter a name for your API token to help you identify it later.
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="createToken">
                        <div class="space-y-4 py-4">
                            <div class="space-y-2">
                                <Label for="name">Token Name</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    placeholder="My API Token"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="showCreateDialog = false"
                                :disabled="form.processing"
                            >
                                Cancel
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                Create Token
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Show New Token Dialog -->
            <Dialog v-model:open="showTokenDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>API Token Created</DialogTitle>
                        <DialogDescription>
                            {{ flash?.message }}
                        </DialogDescription>
                    </DialogHeader>

                    <div class="space-y-4 py-4">
                        <div class="space-y-2">
                            <Label>Your API Token</Label>
                            <div class="flex gap-2">
                                <Input
                                    :value="newToken"
                                    readonly
                                    class="font-mono text-sm"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="icon"
                                    @click="copyToken"
                                >
                                    <Copy class="h-4 w-4" />
                                </Button>
                            </div>
                            <p class="text-sm text-muted-foreground">
                                Make sure to copy your API token now. You won't be able to see it again!
                            </p>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button @click="closeTokenDialog">
                            Done
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
