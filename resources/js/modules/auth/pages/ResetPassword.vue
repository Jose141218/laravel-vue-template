<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/common/InputError.vue';
import PasswordInput from '@/components/common/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/AuthLayout.vue';

defineOptions({
    layout: (h: any, page: any) => {
        const isInvitation = Boolean(page.props?.isInvitation);

        return h(
            AuthLayout,
            {
                title: isInvitation ? 'Activate account' : 'Reset password',
                description: isInvitation
                    ? 'Create a secure password to activate your account'
                    : 'Please enter your new password below',
            },
            () => page,
        );
    },
});

const props = defineProps<{
    token: string;
    email: string;
    passwordRules: string;
    isInvitation?: boolean;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head :title="isInvitation ? 'Activate account' : 'Reset password'" />

    <Form
        :action="route('password.update')"
        method="post"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="inputEmail"
                    class="mt-1 block w-full"
                    readonly
                />
                <InputError :message="errors.email" class="mt-2" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    autofocus
                    placeholder="Password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation"> Confirm password </Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="mt-1 block w-full"
                    placeholder="Confirm password"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                :disabled="processing"
                data-test="reset-password-button"
            >
                <Spinner v-if="processing" />
                {{ isInvitation ? 'Activate account' : 'Reset password' }}
            </Button>
        </div>
    </Form>
</template>
