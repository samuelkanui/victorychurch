<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, Eye, EyeOff } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const showPassword = ref(false);
</script>

<template>
    <AuthBase
        title="Login"
        description=""
    >
        <Head title="Log in" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-400"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <!-- Google Sign In Button -->
            <a
                href="/auth/google"
                class="flex items-center justify-center w-full bg-white text-gray-700 font-semibold py-3 rounded-lg shadow-md hover:shadow-lg transition-all hover:bg-gray-50 mb-2"
            >
                <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="h-5 w-5 mr-3" alt="Google Logo" />
                Sign in with Google
            </a>

            <div class="relative flex items-center justify-center text-sm">
                <div class="absolute inset-0 flex items-center">
                    <span class="w-full border-t border-white/20"></span>
                </div>
                <span class="relative bg-transparent px-2 text-white/50 uppercase">Or continue with</span>
            </div>

            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
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
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
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

                <div class="flex items-center justify-between text-sm">
                    <Label for="remember" class="flex items-center space-x-2 text-white/70 cursor-pointer">
                        <Checkbox id="remember" name="remember" :tabindex="3" class="border-white/30 data-[state=checked]:bg-white data-[state=checked]:text-slate-900" />
                        <span>Remember me</span>
                    </Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-white/70 hover:text-white transition-colors"
                        :tabindex="5"
                    >
                        Forgot password?
                    </TextLink>
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full bg-white hover:bg-white/90 text-slate-900 font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-5 w-5 animate-spin mr-2"
                    />
                    {{ processing ? 'Logging in...' : 'Log in' }}
                </Button>
            </div>

            <div
                class="text-center text-sm text-white/70"
                v-if="canRegister"
            >
                Don't have an account? 
                <TextLink :href="register()" :tabindex="5" class="text-white hover:text-white/80 transition-colors font-semibold">Register</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
