<template>
  <div v-if="show" class="fixed inset-0 bg-opacity-90 z-50 flex items-center justify-center flex-col">
    <div class="absolute top-5 right-5 text-white text-3xl cursor-pointer" @click="$emit('close')">
      <i class="fas fa-times"></i>
    </div>
    <div class="relative max-w-5xl max-h-5xl">
      <img
        :src="imageUrl"
        :alt="imageTitle"
        class="max-w-full max-h-screen object-contain rounded shadow-2xl"
        :style="{ transform: `rotate(${rotation}deg) scale(${zoom})` }"
      >
      <div class="absolute top-2 right-2 flex gap-2">
        <button @click="rotateLeft" class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">
          <i class="fas fa-undo"></i>
        </button>
        <button @click="rotateRight" class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">
          <i class="fas fa-redo"></i>
        </button>
        <button @click="zoomIn" class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">
          <i class="fas fa-search-plus"></i>
        </button>
        <button @click="zoomOut" class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">
          <i class="fas fa-search-minus"></i>
        </button>
        <button @click="$emit('download')" class="bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70">
          <i class="fas fa-download"></i>
        </button>
      </div>
    </div>
    <div class="text-white mt-4 text-center">{{ imageTitle }}</div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  imageUrl: String,
  imageTitle: String
})

defineEmits(['close', 'download'])

const rotation = ref(0)
const zoom = ref(1)

const rotateLeft = () => {
  rotation.value -= 90
}

const rotateRight = () => {
  rotation.value += 90
}

const zoomIn = () => {
  zoom.value = Math.min(3, zoom.value + 0.1)
}

const zoomOut = () => {
  zoom.value = Math.max(0.1, zoom.value - 0.1)
}

// Reset transform quando fechar
watch(() => props.show, (newVal) => {
  if (!newVal) {
    rotation.value = 0
    zoom.value = 1
  }
})
</script>
