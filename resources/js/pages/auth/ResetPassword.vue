<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { update } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    token: string;
    email: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <AuthLayout
        title="Reset password"
        description="Please enter your new password below"
    >
        <Head title="Reset password" />

        <Form
            v-bind="update.form()"
            :transform="(data) => ({ ...data, token, email })"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email" class="text-gray-700 dark:text-white/70">Email</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="inputEmail"
                        class="bg-transparent border-0 border-b-2 border-gray-300 dark:border-white/30 rounded-none px-0 py-3 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/50 focus:border-blue-600 dark:focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0 transition-colors"
                        readonly
                    />
                    <InputError :message="errors.email" class="text-red-500 dark:text-red-400" />
                </div>

                <div class="grid gap-2">
                    <Label for="password" class="text-gray-700 dark:text-white/70">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        class="bg-transparent border-0 border-b-2 border-gray-300 dark:border-white/30 rounded-none px-0 py-3 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/50 focus:border-blue-600 dark:focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0 transition-colors"
                        autofocus
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" class="text-red-500 dark:text-red-400" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation" class="text-gray-700 dark:text-white/70">
                        Confirm Password
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        class="bg-transparent border-0 border-b-2 border-gray-300 dark:border-white/30 rounded-none px-0 py-3 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/50 focus:border-blue-600 dark:focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0 transition-colors"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" class="text-red-500 dark:text-red-400" />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full bg-blue-600 dark:bg-white hover:bg-blue-700 dark:hover:bg-white/90 text-white dark:text-slate-900 font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all"
                    :disabled="processing"
                    data-test="reset-password-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-4 w-4 animate-spin mr-2"
                    />
                    Reset password
                </Button>
            </div>
        </Form>
    </AuthLayout>
</template>
