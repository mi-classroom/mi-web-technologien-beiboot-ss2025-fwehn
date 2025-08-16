<script setup lang="ts">
interface Props {
  open: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'cancel'): void
}>()

function close() {
  emit('cancel')
}
</script>

<template>
  <div
    v-if="open"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    @click.self="close"
  >
    <div class="bg-warm-light rounded-lg shadow-lg max-w-lg w-full p-6">
      <slot />
    </div>
  </div>
</template>
