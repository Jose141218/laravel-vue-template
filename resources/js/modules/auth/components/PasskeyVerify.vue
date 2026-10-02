<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { usePasskeyVerify } from '@laravel/passkeys/vue';
import { KeyRound } from '@lucide/vue';
import InputError from '@/components/common/InputError.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { Spinner } from '@/components/ui/spinner';

export type PasskeyVerifyProps = {
    routes?: {
        options: string | { url: string; method?: string };
        submit: string | { url: string; method?: string };
    };
    label?: string;
    loadingLabel?: string;
    separator?: string;
};

const props = defineProps<PasskeyVerifyProps>();

const { verify, isLoading, error, isSupported } = usePasskeyVerify({
    ...(props.routes
        ? {
              routes: {
                  options:
                      typeof props.routes.options === 'string'
                          ? props.routes.options
                          : props.routes.options.url,
                  submit:
                      typeof props.routes.submit === 'string'
                          ? props.routes.submit
                          : props.routes.submit.url,
              },
          }
        : {}),
    onSuccess: (response) => {
        router.visit(response.redirect ?? '/dashboard');
    },
});
</script>

<template>
    <div v-if="isSupported">
        <div class="grid gap-2">
            <Button
                type="button"
                variant="outline"
                class="w-full"
                @click="verify"
                :disabled="isLoading"
            >
                <Spinner v-if="isLoading" />
                <KeyRound v-else class="h-4 w-4" />
                {{
                    isLoading
                        ? (props.loadingLabel ?? 'Authenticating...')
                        : (props.label ?? 'Sign in with a passkey')
                }}
            </Button>

            <div v-if="error" class="text-center">
                <InputError :message="error" />
            </div>
        </div>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <Separator class="w-full" />
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-background px-2 text-muted-foreground">
                    {{ props.separator ?? 'Or continue with email' }}
                </span>
            </div>
        </div>
    </div>
</template>
