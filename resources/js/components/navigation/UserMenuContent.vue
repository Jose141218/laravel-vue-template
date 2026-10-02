<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from '@lucide/vue';
import UserInfo from '@/components/navigation/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import type { User } from '@/interfaces';
import { setClientAuthenticated } from '@/lib/authHistoryGuard';

type Props = {
    user: User;
};

const handleLogout = () => {
    setClientAuthenticated(false);
    router.flushAll();
    router.post(
        route('logout'),
        {},
        {
            replace: true,
        },
    );
};

defineProps<Props>();
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :user="user" :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link
                class="block w-full cursor-pointer"
                :href="route('profile.edit')"
                prefetch
            >
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <button
            type="button"
            class="flex w-full cursor-pointer items-center"
            @click="handleLogout"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </button>
    </DropdownMenuItem>
</template>
