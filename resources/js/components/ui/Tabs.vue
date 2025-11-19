<script setup lang="ts">
import { provide, ref, watch } from 'vue'

interface Props {
  defaultValue?: string
  value?: string
  orientation?: 'horizontal' | 'vertical'
}

const props = withDefaults(defineProps<Props>(), {
  orientation: 'horizontal',
})

const emit = defineEmits<{
  'update:value': [value: string]
}>()

const activeTab = ref(props.value || props.defaultValue || 'pending')

const setActiveTab = (value: string) => {
  activeTab.value = value
  emit('update:value', value)
}

// Watch for prop changes
watch(() => props.value, (newValue) => {
  if (newValue !== undefined) {
    activeTab.value = newValue
  }
})

provide('activeTab', activeTab)
provide('setActiveTab', setActiveTab)
provide('orientation', props.orientation)
</script>

<template>
  <div class="w-full">
    <slot />
  </div>
</template>
