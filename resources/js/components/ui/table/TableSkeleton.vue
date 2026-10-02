<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';

interface Props {
    columns?: number;
    rows?: number;
}

withDefaults(defineProps<Props>(), {
    columns: 4,
    rows: 5,
});
</script>

<template>
    <tbody>
        <tr v-for="r in rows" :key="`skeleton-row-${r}`">
            <td v-for="c in columns" :key="`skeleton-col-${c}`" class="py-3.5">
                <Skeleton :class="[
                    'h-4',
                    c === 1
                        ? 'w-3/4'
                        : c === columns
                            ? 'ml-auto w-16'
                            : 'w-1/2',
                ]" />
            </td>
        </tr>
    </tbody>
</template>
