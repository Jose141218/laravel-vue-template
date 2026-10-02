import { router } from '@inertiajs/vue3';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import type { SessionDevice } from '@/interfaces';
import { useSessions } from './useSessions';

vi.mock('@inertiajs/vue3', () => ({
    router: {
        delete: vi.fn(),
    },
}));

describe('useSessions', () => {
    const mockSessions: SessionDevice[] = [
        {
            id: 'current-session-id',
            agent: {
                is_desktop: true,
                is_mobile: false,
                is_tablet: false,
                platform: 'Windows',
                browser: 'Chrome',
            },
            ip_address: '127.0.0.1',
            is_current_device: true,
            last_active: 'Active now',
        },
        {
            id: 'remote-session-id',
            agent: {
                is_desktop: false,
                is_mobile: true,
                is_tablet: false,
                platform: 'iOS',
                browser: 'Safari',
            },
            ip_address: '192.168.1.100',
            is_current_device: false,
            last_active: '1 hour ago',
        },
    ];

    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('computes currentSession, otherSessions, and hasOtherSessions correctly', () => {
        const { currentSession, otherSessions, hasOtherSessions } = useSessions(
            () => mockSessions,
        );

        expect(currentSession.value?.id).toBe('current-session-id');
        expect(otherSessions.value.length).toBe(1);
        expect(otherSessions.value[0].id).toBe('remote-session-id');
        expect(hasOtherSessions.value).toBe(true);
    });

    it('handles no other sessions correctly', () => {
        const { otherSessions, hasOtherSessions } = useSessions(() => [
            mockSessions[0],
        ]);

        expect(otherSessions.value.length).toBe(0);
        expect(hasOtherSessions.value).toBe(false);
    });

    it('handles confirm and cancel revoke session dialog', () => {
        const {
            isRevokeDialogOpen,
            sessionToRevoke,
            confirmRevokeSession,
            cancelRevokeSession,
        } = useSessions(() => mockSessions);

        expect(isRevokeDialogOpen.value).toBe(false);
        expect(sessionToRevoke.value).toBeNull();

        confirmRevokeSession(mockSessions[1]);
        expect(isRevokeDialogOpen.value).toBe(true);
        expect(sessionToRevoke.value).toEqual(mockSessions[1]);

        cancelRevokeSession();
        expect(isRevokeDialogOpen.value).toBe(false);
        expect(sessionToRevoke.value).toBeNull();
    });

    it('handles revoking a session via router.delete', () => {
        (globalThis as any).route = vi.fn(
            (name: string, id: string) => `/settings/sessions/${id}`,
        );

        const {
            isRevoking,
            confirmRevokeSession,
            handleRevokeSession,
            isRevokeDialogOpen,
            sessionToRevoke,
        } = useSessions(() => mockSessions);

        confirmRevokeSession(mockSessions[1]);
        expect(isRevoking.value).toBe(false);

        handleRevokeSession();
        expect(isRevoking.value).toBe(true);

        expect(router.delete).toHaveBeenCalledWith(
            '/settings/sessions/remote-session-id',
            expect.objectContaining({
                preserveScroll: true,
                onFinish: expect.any(Function),
            }),
        );

        const deleteOptions = vi.mocked(router.delete).mock.calls[0][1];
        deleteOptions?.onFinish?.({} as any);

        expect(isRevoking.value).toBe(false);
        expect(isRevokeDialogOpen.value).toBe(false);
        expect(sessionToRevoke.value).toBeNull();
    });

    it('controls logout others dialog state', () => {
        const {
            isLogoutOthersDialogOpen,
            openLogoutOthersDialog,
            closeLogoutOthersDialog,
        } = useSessions(() => mockSessions);

        expect(isLogoutOthersDialogOpen.value).toBe(false);

        openLogoutOthersDialog();
        expect(isLogoutOthersDialogOpen.value).toBe(true);

        closeLogoutOthersDialog();
        expect(isLogoutOthersDialogOpen.value).toBe(false);
    });
});
