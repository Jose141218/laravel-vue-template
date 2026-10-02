import { beforeEach, describe, expect, test, vi } from 'vitest';
import type { User } from '../interfaces';
import { useUserInvitation } from './useUserInvitation';

const mockPost = vi.fn();
vi.mock('@inertiajs/vue3', () => ({
    router: {
        post: (...args: unknown[]) => mockPost(...args),
    },
}));

(globalThis as any).route = vi.fn(
    (name: string, id: number | string) =>
        `/${name.replaceAll('.', '/')}/${id}`,
);

describe('useUserInvitation composable', () => {
    const mockUser: User = {
        id: 'user-uuid-1',
        name: 'Invitado',
        email: 'invitado@sw.com',
        roles: [],
        email_verified_at: null,
        status: 'pending',
        created_at: {
            raw: '2026-01-01',
            formatted: '01/01/2026',
            human: 'hace 1 día',
            diff: '1 day ago',
        },
    };

    beforeEach(() => {
        vi.clearAllMocks();
    });

    test('initializes with default state', () => {
        const { itemToResend, isResendDialogOpen, isResending } =
            useUserInvitation('security.users.');

        expect(itemToResend.value).toBeNull();
        expect(isResendDialogOpen.value).toBe(false);
        expect(isResending.value).toBe(false);
    });

    test('opens dialog on confirmResend and sets item', () => {
        const { itemToResend, isResendDialogOpen, confirmResend } =
            useUserInvitation('security.users.');

        confirmResend(mockUser);

        expect(itemToResend.value).toEqual(mockUser);
        expect(isResendDialogOpen.value).toBe(true);
    });

    test('closes dialog on cancelResend and clears item', () => {
        const {
            itemToResend,
            isResendDialogOpen,
            confirmResend,
            cancelResend,
        } = useUserInvitation('security.users.');

        confirmResend(mockUser);
        cancelResend();

        expect(itemToResend.value).toBeNull();
        expect(isResendDialogOpen.value).toBe(false);
    });

    test('dispatches router.post on handleResend', () => {
        const { confirmResend, handleResend, isResending } =
            useUserInvitation('security.users.');

        confirmResend(mockUser);
        handleResend();

        expect(isResending.value).toBe(true);
        expect(mockPost).toHaveBeenCalledWith(
            '/security/users/resend-invitation/user-uuid-1',
            {},
            expect.objectContaining({ preserveScroll: true }),
        );
    });

    test('does nothing if handleResend is called without an item', () => {
        const { handleResend } = useUserInvitation('security.users.');

        handleResend();

        expect(mockPost).not.toHaveBeenCalled();
    });
});
