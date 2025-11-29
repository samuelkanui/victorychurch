<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    PinInput,
    PinInputGroup,
    PinInputSlot,
} from '@/components/ui/pin-input';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { store } from '@/routes/two-factor/login';
import { Form, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

interface AuthConfigContent {
    title: string;
    description: string;
    toggleText: string;
}

const authConfigContent = computed<AuthConfigContent>(() => {
    if (showRecoveryInput.value) {
        return {
            title: 'Recovery Code',
            description:
                'Please confirm access to your account by entering one of your emergency recovery codes.',
            toggleText: 'login using an authentication code',
        };
    }

    return {
        title: 'Authentication Code',
        description:
            'Enter the authentication code provided by your authenticator application.',
        toggleText: 'login using a recovery code',
    };
});

const showRecoveryInput = ref<boolean>(false);

const toggleRecoveryMode = (clearErrors: () => void): void => {
    showRecoveryInput.value = !showRecoveryInput.value;
    clearErrors();
    code.value = [];
};

const code = ref<number[]>([]);
const codeValue = computed<string>(() => code.value.join(''));
</script>

<template>
    <AuthLayout
        :title="authConfigContent.title"
        :description="authConfigContent.description"
    >
        <Head title="Two-Factor Authentication" />

        <div class="space-y-6">
            <template v-if="!showRecoveryInput">
                <Form
                    v-bind="store.form()"
                    class="space-y-4"
                    reset-on-error
                    @error="code = []"
                    #default="{ errors, processing, clearErrors }"
                >
                    <input type="hidden" name="code" :value="codeValue" />
                    <div
                        class="flex flex-col items-center justify-center space-y-3 text-center"
                    >
                        <div class="flex w-full items-center justify-center">
                            <PinInput
                                id="otp"
                                placeholder="○"
                                v-model="code"
                                type="number"
                                otp
                                class="text-gray-900 dark:text-white"
                            >
                                <PinInputGroup>
                                    <PinInputSlot
                                        v-for="(id, index) in 6"
                                        :key="id"
                                        :index="index"
                                        :disabled="processing"
                                        autofocus
                                        class="border-gray-300 dark:border-white/30 bg-transparent text-gray-900 dark:text-white focus:border-blue-600 dark:focus:border-white/60"
                                    />
                                </PinInputGroup>
                            </PinInput>
                        </div>
                        <InputError :message="errors.code" class="text-red-500 dark:text-red-400" />
                    </div>
                    <Button type="submit" class="w-full bg-blue-600 dark:bg-white hover:bg-blue-700 dark:hover:bg-white/90 text-white dark:text-slate-900 font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all" :disabled="processing"
                        >Continue</Button
                    >
                    <div class="text-center text-sm text-gray-600 dark:text-white/70">
                        <span>or you can </span>
                        <button
                            type="button"
                            class="text-blue-600 dark:text-white underline decoration-blue-300 dark:decoration-white/30 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </Form>
            </template>

            <template v-else>
                <Form
                    v-bind="store.form()"
                    class="space-y-4"
                    reset-on-error
                    #default="{ errors, processing, clearErrors }"
                >
                    <Input
                        name="recovery_code"
                        type="text"
                        placeholder="Enter recovery code"
                        :autofocus="showRecoveryInput"
                        required
                        class="bg-transparent border-0 border-b-2 border-gray-300 dark:border-white/30 rounded-none px-0 py-3 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-white/50 focus:border-blue-600 dark:focus:border-white/60 focus-visible:ring-0 focus-visible:ring-offset-0 transition-colors"
                    />
                    <InputError :message="errors.recovery_code" class="text-red-500 dark:text-red-400" />
                    <Button type="submit" class="w-full bg-blue-600 dark:bg-white hover:bg-blue-700 dark:hover:bg-white/90 text-white dark:text-slate-900 font-semibold py-3 rounded-lg shadow-lg hover:shadow-xl transition-all" :disabled="processing"
                        >Continue</Button
                    >

                    <div class="text-center text-sm text-gray-600 dark:text-white/70">
                        <span>or you can </span>
                        <button
                            type="button"
                            class="text-blue-600 dark:text-white underline decoration-blue-300 dark:decoration-white/30 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current"
                            @click="() => toggleRecoveryMode(clearErrors)"
                        >
                            {{ authConfigContent.toggleText }}
                        </button>
                    </div>
                </Form>
            </template>
        </div>
    </AuthLayout>
</template>
