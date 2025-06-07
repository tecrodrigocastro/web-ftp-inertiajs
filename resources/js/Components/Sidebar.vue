<template>
  <div class="bg-gray-50 w-60 border-r border-gray-200 flex flex-col">
    <div class="p-4">
      <div class="rounded-lg p-2 flex items-center text-gray-700 font-medium mb-1 bg-blue-100 text-blue-700">
        <i class="fas fa-folder mr-3"></i>
        <span>Arquivos</span>
      </div>
      <div
        @click="$emit('show-folder-modal')"
        class="rounded-lg p-2 flex items-center text-gray-700 font-medium mb-1 cursor-pointer hover:bg-gray-200 transition-colors"
      >
        <i class="fas fa-folder-plus mr-3"></i>
        <span>Nova pasta</span>
      </div>
      <div
        @click="$emit('show-upload-modal')"
        class="rounded-lg p-2 flex items-center text-gray-700 font-medium mb-1 cursor-pointer hover:bg-gray-200 transition-colors"
      >
        <i class="fas fa-file-upload mr-3"></i>
        <span>Enviar arquivo</span>
      </div>
      <div class="rounded-lg p-2 flex items-center text-gray-700 font-medium cursor-pointer hover:bg-gray-200 transition-colors">
        <i class="fas fa-trash-alt mr-3"></i>
        <span>Lixeira</span>
      </div>
    </div>

    <!-- Seção do Clipboard -->
    <div v-if="clipboard.items.length > 0" class="px-4 py-3 border-t border-gray-200">
      <div class="text-sm font-medium text-gray-700 mb-2">
        <i class="fas fa-clipboard mr-2"></i>
        Área de Transferência
        <span class="text-xs" :class="clipboard.action === 'copy' ? 'text-cyan-600' : 'text-orange-600'">
          ({{ clipboard.action === 'copy' ? 'Copiar' : 'Mover' }})
        </span>
      </div>
      <div class="space-y-1 max-h-32 overflow-y-auto">
        <div
          v-for="item in clipboard.items"
          :key="item.path"
          class="flex items-start text-xs text-gray-600 p-2 rounded bg-gray-100"
        >
          <div
            class="flex items-center justify-center w-4 h-4 rounded mr-2 mt-0.5"
            :class="clipboard.action === 'copy' ? 'bg-cyan-100' : 'bg-orange-100'"
          >
            <i
              :class="clipboard.action === 'copy' ? 'fas fa-copy text-cyan-600' : 'fas fa-cut text-orange-600'"
              class="text-xs"
            ></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="truncate font-medium">{{ item.name }}</div>
            <div class="truncate text-gray-400 text-xs">{{ item.path }}</div>
          </div>
        </div>
      </div>
      <div class="flex space-x-1 mt-2">
        <button
          @click="$emit('clear-clipboard')"
          class="flex-1 text-xs text-red-600 hover:text-red-800 flex items-center justify-center py-1 rounded hover:bg-red-50"
        >
          <i class="fas fa-times mr-1"></i>
          Limpar
        </button>
        <button
          v-if="clipboard.items.length > 0"
          @click="$emit('paste-items')"
          :disabled="pasting"
          class="flex-1 text-xs text-blue-600 hover:text-blue-800 flex items-center justify-center py-1 rounded hover:bg-blue-50 disabled:opacity-50"
        >
          <i class="fas fa-paste mr-1"></i>
          {{ pasting ? 'Colando...' : 'Colar aqui' }}
        </button>
      </div>
    </div>

    <!-- Espaço em disco -->
    <div class="mt-auto p-4 border-t border-gray-200">
      <div class="text-sm font-medium text-gray-700 mb-2">Espaço em Disco</div>
      <div class="h-2 bg-gray-200 rounded-full mb-1">
        <div class="h-2 bg-blue-500 rounded-full" style="width: 65%"></div>
      </div>
      <div class="text-xs text-gray-500">Uso fictício</div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  clipboard: Object,
  pasting: Boolean
})

defineEmits([
  'show-folder-modal',
  'show-upload-modal',
  'clear-clipboard',
  'paste-items'
])
</script>
