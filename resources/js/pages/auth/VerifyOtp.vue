<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle, Mail, RefreshCw } from 'lucide-vue-next';
import { ref, computed } from 'vue';

const props = defineProps<{
    email: string;
    type: 'registration' | 'reset';
    status?: string;
}>();

const resendCooldown = ref(0);
const resending = ref(false);

const title = computed(() => 
    props.type === 'registration' ? 'Verify Your Email' : 'Verify Reset Code'
);

const description = computed(() => 
    props.type === 'registration' 
        ? `We've sent a 6-digit code to ${props.email}. Enter it below to activate your account.`
        : `We've sent a 6-digit code to ${props.email}. Enter it below to reset your password.`
);

const startCooldown = () => {
    resendCooldown.value = 60;
    const interval = setInterval(() => {
        resendCooldown.value--;
        if (resendCooldown.value <= 0) {
            clearInterval(interval);
        }
    }, 1000);
};

const handleResend = async () => {
    resending.value = true;
    
    await fetch('/resend-otp', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
            email: props.email,
            type: props.type,
        }),
    });

    resending.value = false;
    startCooldown();
};
</script>

<template>
    <AuthBase
        :title="title"
        :description="description"
    >
        <Head :title="title" />

        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-400"
        >
            {{ status }}
        </div>

        <Form
            action="/verify-otp"
            method="post"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <input type="hidden" name="email" :value="email" />
            <input type="hidden" name="type" :value="type" />

            <div class="grid gap-2">
                <div class="flex items-center gap-2 text-white/70 text-sm mb-2">
                    <Mail class="h-4 w-4" />
                    <span>{{ email }}</span>
                </div>

                <Input
                    id="code"
                    type="text"
                    name="code"
                    required
                    autofocus
                    maxlength="6"
                    placeholder="Enter 6-digit code"
                    class="bg-transparent border-0 border-b-2 border-white/30 rounded-none px-0 py-3 text-white text-center text-2xl tracking-widest placeholder:text-white/50 focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0 font-mono"
                />
                <InputError :message="errors.code" class="text-red-400" />
            </div>

            <Button
                type="submit"
                class="w-full bg-white hover:bg-white/90 text-slate-900 font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all"
                :disabled="processing"
            >
                <LoaderCircle
                    v-if="processing"
                    class="h-5 w-5 animate-spin mr-2"
                />
                {{ processing ? 'Verifying...' : 'Verify Code' }}
            </Button>

            <div class="text-center">
                <button
                    type="button"
                    @click="handleResend"
                    :disabled="resendCooldown > 0 || resending"
                    class="text-sm text-white/70 hover:text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 mx-auto"
                >
                    <RefreshCw :class="['h-4 w-4', resending && 'animate-spin']" />
                    <span v-if="resendCooldown > 0">
                        Resend code in {{ resendCooldown }}s
                    </span>
                    <span v-else>
                        {{ resending ? 'Sending...' : 'Resend code' }}
                    </span>
                </button>
            </div>
        </Form>
    </AuthBase>
</template>
