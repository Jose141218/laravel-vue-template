<script setup lang="ts">
import type { SelectValueProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { SelectValue } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<SelectValueProps & { class?: HTMLAttributes["class"] }>()
</script>

<template>
  <SelectValue
    data-slot="select-value"
    v-bind="props"
    :class="cn(props.class)"
  >
    <slot />
  </SelectValue>
</template>
