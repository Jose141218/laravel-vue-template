import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { SessionDevice } from '@/interfaces';

export function useSessions(sessions: () => SessionDevice[]) {
    const isRevokeDialogOpen = ref(false);
    const sessionToRevoke = ref<SessionDevice | null>(null);
    const isRevoking = ref(false);

    const isLogoutOthersDialogOpen = ref(false);

    const currentSession = computed(() =>
        sessions().find((s) => s.is_current_device),
    );

    const otherSessions = computed(() =>
        sessions().filter((s) => !s.is_current_device),
    );

    const hasOtherSessions = computed(() => otherSessions.value.length > 0);

    const confirmRevokeSession = (session: SessionDevice) => {
        sessionToRevoke.value = session;
        isRevokeDialogOpen.value = true;
    };

    const cancelRevokeSession = () => {
        isRevokeDialogOpen.value = false;
        sessionToRevoke.value = null;
    };

    const handleRevokeSession = () => {
        if (!sessionToRevoke.value) {
            return;
        }

        isRevoking.value = true;
        router.delete(route('sessions.destroy', sessionToRevoke.value.id), {
            preserveScroll: true,
            onFinish: () => {
                isRevoking.value = false;
                cancelRevokeSession();
            },
        });
    };

    const openLogoutOthersDialog = () => {
        isLogoutOthersDialogOpen.value = true;
    };

    const closeLogoutOthersDialog = () => {
        isLogoutOthersDialogOpen.value = false;
    };

    return {
        isRevokeDialogOpen,
        sessionToRevoke,
        isRevoking,
        isLogoutOthersDialogOpen,
        currentSession,
        otherSessions,
        hasOtherSessions,
        confirmRevokeSession,
        cancelRevokeSession,
        handleRevokeSession,
        openLogoutOthersDialog,
        closeLogoutOthersDialog,
    };
}
