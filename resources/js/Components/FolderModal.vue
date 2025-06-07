<template>
  <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg w-96 shadow-xl border-2 border-gray-300">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Nova Pasta</h2>
        <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form @submit.prevent="$emit('create', folderName)">
        <div class="mb-4">
          <label class="block text-gray-700 text-sm font-medium mb-2">Nome da pasta</label>
          <input
            v-model="folderName"
            type="text"
            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            placeholder="Digite o nome da pasta"
            required
          >
        </div>
        <div class="flex justify-end space-x-2">
          <button type="button" @click="$emit('close')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Cancelar</button>
          <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Criar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: Boolean
})

defineEmits(['close', 'create'])

const folderName = ref('')

// Limpar campo quando o modal fechar
watch(() => props.show, (newVal) => {
  if (!newVal) {
    folderName.value = ''
  }
})
</script>
