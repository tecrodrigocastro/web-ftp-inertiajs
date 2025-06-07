<template>
  <div
    v-if="show"
    class="absolute inset-0 bg-blue-50 bg-opacity-90 border-4 border-dashed border-blue-400 flex items-center justify-center z-50"
  >
    <div class="text-center">
      <div v-if="uploading">
        <i class="fas fa-spinner fa-spin text-blue-500 text-6xl mb-4"></i>
        <div class="text-xl font-bold text-blue-700 mb-2">Fazendo Upload...</div>
        <div class="text-blue-600">Enviando arquivos para: {{ currentPath }}</div>
      </div>
      <div v-else>
        <i class="fas fa-cloud-upload-alt text-blue-500 text-6xl mb-4"></i>
        <div class="text-xl font-bold text-blue-700 mb-2">Solte os arquivos aqui</div>
        <div class="text-blue-600">Upload para: {{ currentPath }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  uploading: Boolean,
  currentPath: String
})
</script>
