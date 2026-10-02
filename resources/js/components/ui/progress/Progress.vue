<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import type { ProgressRootProps } from "reka-ui"
import { ProgressIndicator, ProgressRoot } from "reka-ui"
import { cn } from "@/lib/utils"

interface Props extends ProgressRootProps {
  class?: HTMLAttributes["class"]
  indicatorClass?: HTMLAttributes["class"]
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: 0,
})
</script>

<template>
  <ProgressRoot
    data-slot="progress"
    :class="
      cn(
        'relative h-2 w-full overflow-hidden rounded-full bg-primary/20',
        props.class,
      )
    "
    v-bind="props"
  >
    <ProgressIndicator
      data-slot="progress-indicator"
      :class="
        cn(
          'h-full w-full flex-1 bg-primary transition-all duration-300',
          props.indicatorClass,
        )
      "
      :style="`transform: translateX(-${100 - (props.modelValue ?? 0)}%);`"
    />
  </ProgressRoot>
</template>
