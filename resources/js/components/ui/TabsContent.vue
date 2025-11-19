<script setup lang="ts">
import { inject, computed } from 'vue'

interface Props {
  value: string
  class?: string
}

const props = defineProps<Props>()

const activeTab = inject<any>('activeTab')

const isActive = computed(() => activeTab?.value === props.value)
</script>

<template>
  <div
    v-show="isActive"
    :class="['mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2', props.class]"
    :data-state="isActive ? 'active' : 'inactive'"
    tabindex="0"
  >
    <slot />
  </div>
</template>
