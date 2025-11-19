<script setup lang="ts">
interface Props {
  class?: string
}

const props = defineProps<Props>()
</script>

<template>
  <div :class="['inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground', props.class]">
    <slot />
  </div>
</template>
