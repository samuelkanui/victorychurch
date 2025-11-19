<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);
</script>

<template>
    <AuthBase
        title="Register"
        description=""
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Enter your full name"
                        class="bg-transparent border-0 border-b-2 border-white/30 rounded-none px-0 py-3 text-white placeholder:text-white/50 focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0"
                    />
                    <InputError :message="errors.name" class="text-red-400" />
                </div>

                <div class="grid gap-2">
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="Enter your email"
                        class="bg-transparent border-0 border-b-2 border-white/30 rounded-none px-0 py-3 text-white placeholder:text-white/50 focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0"
                    />
                    <InputError :message="errors.email" class="text-red-400" />
                </div>

                <div class="grid gap-2">
                    <div class="relative">
                        <Input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password"
                            placeholder="Enter your password"
                            class="bg-transparent border-0 border-b-2 border-white/30 rounded-none px-0 py-3 pr-10 text-white placeholder:text-white/50 focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-0 top-1/2 -translate-y-1/2 text-white/50 hover:text-white/80 transition-colors"
                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        >
                            <Eye v-if="!showPassword" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <InputError :message="errors.password" class="text-red-400" />
                </div>

                <div class="grid gap-2">
                    <div class="relative">
                        <Input
                            id="password_confirmation"
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="Confirm your password"
                            class="bg-transparent border-0 border-b-2 border-white/30 rounded-none px-0 py-3 pr-10 text-white placeholder:text-white/50 focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0"
                        />
                        <button
                            type="button"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute right-0 top-1/2 -translate-y-1/2 text-white/50 hover:text-white/80 transition-colors"
                            :aria-label="showPasswordConfirmation ? 'Hide password' : 'Show password'"
                        >
                            <Eye v-if="!showPasswordConfirmation" class="h-5 w-5" />
                            <EyeOff v-else class="h-5 w-5" />
                        </button>
                    </div>
                    <InputError :message="errors.password_confirmation" class="text-red-400" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full bg-white hover:bg-white/90 text-slate-900 font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-5 w-5 animate-spin mr-2"
                    />
                    {{ processing ? 'Creating account...' : 'Create account' }}
                </Button>
            </div>

            <div class="text-center text-sm text-white/70">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="text-white hover:text-white/80 transition-colors font-semibold"
                    :tabindex="6"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthBase>
</template>
