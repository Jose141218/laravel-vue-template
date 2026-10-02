<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import ConfirmDialog from '@/components/common/ConfirmDialog.vue';
import Heading from '@/components/common/Heading.vue';
import InputError from '@/components/common/InputError.vue';
import PasswordInput from '@/components/common/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import type { SessionDevice } from '@/interfaces';
import { useSessions } from '../composables/useSessions';
import SessionItem from './SessionItem.vue';

export type Props = {
    sessions?: SessionDevice[];
};

const props = withDefaults(defineProps<Props>(), {
    sessions: () => [],
});

const passwordInput = useTemplateRef('passwordInput');

const {
    isRevokeDialogOpen,
    sessionToRevoke,
    isRevoking,
    isLogoutOthersDialogOpen,
    hasOtherSessions,
    confirmRevokeSession,
    cancelRevokeSession,
    handleRevokeSession,
    openLogoutOthersDialog,
    closeLogoutOthersDialog,
} = useSessions(() => props.sessions);
</script>

<template>
    <div class="space-y-6">
        <Heading
            variant="small"
            title="Browser sessions"
            description="Manage and log out your active sessions on other browsers and devices."
        />

        <p class="text-sm text-muted-foreground">
            If necessary, you may log out of all of your other browser sessions
            across all of your devices. Some of your recent sessions are listed
            below; however, this list may not be exhaustive. If you feel your
            account has been compromised, you should also update your password.
        </p>

        <div class="overflow-hidden rounded-lg border border-border">
            <template v-if="sessions.length">
                <SessionItem
                    v-for="session in sessions"
                    :key="session.id"
                    :session="session"
                    @revoke="confirmRevokeSession"
                />
            </template>

            <div v-else class="p-8 text-center text-muted-foreground">
                No active sessions found.
            </div>
        </div>

        <div v-if="hasOtherSessions" class="flex items-center">
            <Button
                variant="outline"
                type="button"
                @click="openLogoutOthersDialog"
            >
                Log out other browser sessions
            </Button>
        </div>

        <!-- Confirm Single Session Revocation Dialog -->
        <ConfirmDialog
            v-model:open="isRevokeDialogOpen"
            title="Revoke session?"
            :description="`This will terminate the session on ${sessionToRevoke?.agent.platform ?? 'this device'} (${sessionToRevoke?.agent.browser ?? 'Browser'}). The device will be required to log in again.`"
            confirm-text="Revoke session"
            cancel-text="Cancel"
            variant="destructive"
            :loading="isRevoking"
            @confirm="handleRevokeSession"
            @cancel-delete="cancelRevokeSession"
        />

        <!-- Confirm Logout Other Sessions with Password Dialog -->
        <Dialog v-model:open="isLogoutOthersDialogOpen">
            <DialogContent>
                <Form
                    :action="route('sessions.destroy-other')"
                    method="delete"
                    reset-on-success
                    @error="() => passwordInput?.focus()"
                    @success="closeLogoutOthersDialog"
                    :options="{
                        preserveScroll: true,
                    }"
                    class="space-y-6"
                    v-slot="{ errors, processing, reset, clearErrors }"
                >
                    <DialogHeader class="space-y-3">
                        <DialogTitle
                            >Log out other browser sessions</DialogTitle
                        >
                        <DialogDescription>
                            Please enter your password to confirm you would like
                            to log out of your other browser sessions across all
                            of your devices.
                        </DialogDescription>
                    </DialogHeader>

                    <div class="grid gap-2">
                        <Label for="password" class="sr-only">Password</Label>
                        <PasswordInput
                            id="password"
                            name="password"
                            ref="passwordInput"
                            placeholder="Password"
                            autocomplete="current-password"
                        />
                        <InputError :message="errors.password" />
                    </div>

                    <DialogFooter class="gap-2">
                        <DialogClose as-child>
                            <Button
                                variant="secondary"
                                type="button"
                                @click="
                                    () => {
                                        clearErrors();
                                        reset();
                                    }
                                "
                            >
                                Cancel
                            </Button>
                        </DialogClose>

                        <Button
                            type="submit"
                            variant="destructive"
                            :disabled="processing"
                        >
                            {{
                                processing
                                    ? 'Logging out...'
                                    : 'Log out other sessions'
                            }}
                        </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>
