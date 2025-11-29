<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/password/confirm';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthLayout
        title="Confirm your password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    >
        <Head title="Confirm password" />

        <Form
            v-bind="store.form()"
            reset-on-success
            v-slot="{ errors, processing }"
        >
            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label htmlFor="password" class="text-gray-700 dark:text-white/70">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        class="bg-transparent border-0 border-b-2 border-gray-300 dark:border-white/30 rounded-none px-0 py-3 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/50 focus:border-blue-600 dark:focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0 transition-colors"
                        required
                        autocomplete="current-password"
                        autofocus
                    />

                    <InputError :message="errors.password" class="text-red-500 dark:text-red-400" />
                </div>

                <div class="flex items-center">
                    <Button
                        class="w-full bg-blue-600 dark:bg-white hover:bg-blue-700 dark:hover:bg-white/90 text-white dark:text-slate-900 font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all"
                        :disabled="processing"
                        data-test="confirm-password-button"
                    >
                        <LoaderCircle
                            v-if="processing"
                            class="h-4 w-4 animate-spin mr-2"
                        />
                        Confirm Password
                    </Button>
                </div>
            </div>
        </Form>
    </AuthLayout>
</template>
