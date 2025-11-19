<script setup lang="ts">
import { inject, computed } from 'vue'

interface Props {
  value: string
  class?: string
  disabled?: boolean
}

const props = defineProps<Props>()

const activeTab = inject<any>('activeTab')
const setActiveTab = inject<any>('setActiveTab')

const isActive = computed(() => activeTab?.value === props.value)

const handleClick = () => {
  if (!props.disabled && setActiveTab) {
    setActiveTab(props.value)
  }
}

const buttonClasses = computed(() => {
  const baseClasses = 'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50'
  const activeClasses = isActive.value ? 'bg-background text-foreground shadow-sm' : ''
  return [baseClasses, activeClasses, props.class].filter(Boolean).join(' ')
})
</script>

<template>
  <button
    :class="buttonClasses"
    :data-state="isActive ? 'active' : 'inactive'"
    :disabled="props.disabled"
    @click="handleClick"
  >
    <slot />
  </button>
</template>
