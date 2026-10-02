import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { User } from '../interfaces';

export function useUserInvitation(routeName: string) {
    const itemToResend = ref<User | null>(null);
    const isResendDialogOpen = ref(false);
    const isResending = ref(false);

    const confirmResend = (user: User) => {
        itemToResend.value = user;
        isResendDialogOpen.value = true;
    };

    const cancelResend = () => {
        isResendDialogOpen.value = false;
        itemToResend.value = null;
    };

    const handleResend = () => {
        if (!itemToResend.value) {
            return;
        }

        isResending.value = true;

        router.post(
            route(`${routeName}resend-invitation`, itemToResend.value.id),
            {},
            {
                preserveScroll: true,
                onFinish: () => {
                    isResending.value = false;
                    isResendDialogOpen.value = false;
                    itemToResend.value = null;
                },
            },
        );
    };

    return {
        itemToResend,
        isResendDialogOpen,
        isResending,
        confirmResend,
        cancelResend,
        handleResend,
    };
}
