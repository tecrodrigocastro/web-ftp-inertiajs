<template>
  <div class="bg-white shadow-sm border-b border-gray-200 px-4 py-2 flex items-center justify-between">
    <div class="flex items-center">
      <a href="#" class="text-purple-600 hover:text-purple-700 flex items-center">
        <i class="fas fa-server mr-2"></i>
        <span class="font-bold text-xl">KingWebFTP</span>
      </a>
      <div class="text-gray-500 mx-2">/</div>
      <div class="flex items-center space-x-1 text-gray-600 text-sm">
        <span>{{ currentPath }}</span>
      </div>

      <!-- Dica de Drag & Drop -->
      <div class="ml-4 hidden lg:flex items-center text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
        <i class="fas fa-hand-paper mr-1"></i>
        Arraste arquivos aqui para upload
      </div>
    </div>

    <!-- Informações de conexão e controles -->
    <div class="flex items-center space-x-4">
      <!-- Status de conexão -->
      <div class="flex items-center space-x-2 text-sm text-gray-600">
        <div class="flex items-center">
          <div :class="ftpInfo?.connected ? 'bg-green-400' : 'bg-red-400'" class="w-2 h-2 rounded-full mr-2"></div>
          <span>{{ ftpInfo?.host }}</span>
        </div>
        <span class="text-gray-400">•</span>
        <span>{{ ftpInfo?.username }}</span>
      </div>

      <!-- Busca -->
      <div class="relative">
        <input
          :value="searchQuery"
          @input="$emit('update:searchQuery', $event.target.value)"
          type="text"
          placeholder="Pesquisar arquivos..."
          class="px-4 py-2 pr-8 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
        >
        <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
      </div>

      <!-- Botões de ação -->
      <button
        @click="$emit('show-upload-modal')"
        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center"
      >
        <i class="fas fa-upload mr-2"></i>Upload
      </button>

      <button
        @click="$emit('show-folder-modal')"
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center"
      >
        <i class="fas fa-folder-plus mr-2"></i>Nova Pasta
      </button>

      <!-- Botões para operações em massa -->
      <div v-if="selectedItems.length > 0" class="flex items-center space-x-2">
        <span class="text-sm text-gray-600">{{ selectedItems.length }} selecionado(s)</span>

        <button
          @click="$emit('copy-selected')"
          class="bg-cyan-500 hover:bg-cyan-600 text-white px-3 py-2 rounded-lg text-sm flex items-center"
        >
          <i class="fas fa-copy mr-1"></i>Copiar
        </button>

        <button
          @click="$emit('cut-selected')"
          class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-2 rounded-lg text-sm flex items-center"
        >
          <i class="fas fa-cut mr-1"></i>Cortar
        </button>

        <button
          @click="$emit('delete-selected')"
          class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm flex items-center"
        >
          <i class="fas fa-trash mr-1"></i>Excluir
        </button>

        <button
          @click="$emit('clear-selection')"
          class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Botão de Colar -->
      <button
        v-if="clipboardItems.length > 0"
        @click="$emit('paste-items')"
        :disabled="pasting"
        class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm flex items-center disabled:opacity-50"
      >
        <i class="fas fa-paste mr-2"></i>
        {{ pasting ? 'Colando...' : `Colar (${clipboardItems.length})` }}
      </button>

      <!-- Botão de Logout -->
      <button
        @click="$emit('logout')"
        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex items-center"
        title="Sair"
      >
        <i class="fas fa-sign-out-alt mr-2"></i>Sair
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  currentPath: String,
  ftpInfo: Object,
  searchQuery: String,
  selectedItems: Array,
  clipboardItems: Array,
  pasting: Boolean
})

defineEmits([
  'update:searchQuery',
  'show-upload-modal',
  'show-folder-modal',
  'copy-selected',
  'cut-selected',
  'delete-selected',
  'clear-selection',
  'paste-items',
  'logout'
])
</script>
