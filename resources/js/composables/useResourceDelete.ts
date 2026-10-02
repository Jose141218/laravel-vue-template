import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

export interface DeletableItem {
    id: number | string;
    name?: string;
}

export interface UseResourceDeleteOptions {
    preserveScroll?: boolean;
    onSuccess?: () => void;
    onError?: (errors: unknown) => void;
}

export function useResourceDelete<T extends DeletableItem = DeletableItem>(
    routeName: string,
    options: UseResourceDeleteOptions = {},
) {
    const { preserveScroll = true, onSuccess, onError } = options;

    const itemToDelete = ref<T | null>(null);
    const isDeleteDialogOpen = ref(false);
    const isDeleting = ref(false);

    const confirmDelete = (item: T) => {
        itemToDelete.value = item;
        isDeleteDialogOpen.value = true;
    };

    const cancelDelete = () => {
        isDeleteDialogOpen.value = false;
        itemToDelete.value = null;
    };

    const handleDelete = () => {
        if (!itemToDelete.value) {
            return;
        }

        const id = itemToDelete.value.id;
        isDeleting.value = true;

        router.delete(route(`${routeName}destroy`, id), {
            preserveScroll,
            onSuccess: () => {
                onSuccess?.();
            },
            onError: (errors) => {
                onError?.(errors);
            },
            onFinish: () => {
                isDeleting.value = false;
                isDeleteDialogOpen.value = false;
                itemToDelete.value = null;
            },
        });
    };

    return {
        itemToDelete,
        isDeleteDialogOpen,
        isDeleting,
        confirmDelete,
        cancelDelete,
        handleDelete,
    };
}
