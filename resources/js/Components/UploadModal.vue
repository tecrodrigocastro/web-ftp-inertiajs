<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96 shadow-xl border-2 border-gray-300">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Upload de Arquivos</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form @submit.prevent="$emit('upload', selectedFiles)">
        <div
          @drop="handleDrop"
          @dragover.prevent
          @dragenter.prevent
          class="mb-4 border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors"
          :class="{ 'border-blue-400 bg-blue-50': isDragging }"
        >
          <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
          <p class="text-sm text-gray-500 mb-2">Arraste arquivos ou clique para selecionar</p>
          <input ref="fileInput" @change="handleFileSelect" type="file" multiple class="hidden">
          <button type="button" @click="$refs.fileInput.click()" class="bg-blue-100 text-blue-600 px-4 py-2 rounded hover:bg-blue-200 font-medium">
            {{ selectedFiles.length > 0 ? `${selectedFiles.length} arquivo(s) selecionado(s)` : 'Selecionar arquivos' }}
          </button>

          <!-- Lista de arquivos selecionados -->
          <div v-if="selectedFiles.length > 0" class="mt-4 text-left">
            <div class="text-sm font-medium text-gray-700 mb-2">Arquivos selecionados:</div>
            <div class="max-h-32 overflow-y-auto">
              <div v-for="(file, index) in selectedFiles" :key="index" class="flex items-center justify-between text-sm text-gray-600 py-1">
                <span class="truncate">{{ file.name }}</span>
                <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700 ml-2">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-2">
          <button type="button" @click="$emit('close')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Cancelar</button>
          <button type="submit" :disabled="selectedFiles.length === 0 || uploading" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50">
            {{ uploading ? 'Enviando...' : 'Upload' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  uploading: Boolean
})

defineEmits(['close', 'upload'])

const selectedFiles = ref([])
const isDragging = ref(false)

const handleFileSelect = (event) => {
  selectedFiles.value = Array.from(event.target.files)
}

const handleDrop = (event) => {
  event.preventDefault()
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files)
  selectedFiles.value = [...selectedFiles.value, ...files]
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

// Limpar arquivos quando o modal fechar
watch(() => props.show, (newVal) => {
  if (!newVal) {
    selectedFiles.value = []
  }
})
</script>
