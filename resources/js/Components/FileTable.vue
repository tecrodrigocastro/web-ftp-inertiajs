<template>
  <table class="min-w-full divide-y divide-gray-200">
    <thead>
      <tr class="bg-gray-50">
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
          <input
            type="checkbox"
            @change="toggleSelectAll"
            :checked="selectedItems.length === filteredContents.length && filteredContents.length > 0"
            :indeterminate="selectedItems.length > 0 && selectedItems.length < filteredContents.length"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
          >
        </th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamanho</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modificado</th>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      <!-- Voltar diretório -->
      <tr v-if="currentPath !== '/'" class="hover:bg-gray-50 cursor-pointer transition-colors">
        <td class="px-6 py-4 whitespace-nowrap"></td>
        <td class="px-6 py-4 whitespace-nowrap">
          <a @click="$emit('navigate-up')" class="text-blue-600 hover:text-blue-800 flex items-center cursor-pointer">
            <i class="fas fa-arrow-up mr-2"></i>..
          </a>
        </td>
        <td class="px-6 py-4 whitespace-nowrap"></td>
        <td class="px-6 py-4 whitespace-nowrap"></td>
        <td class="px-6 py-4 whitespace-nowrap"></td>
        <td class="px-6 py-4 whitespace-nowrap"></td>
      </tr>

      <!-- Lista de arquivos e pastas -->
      <tr v-for="item in filteredContents" :key="item.path"
          class="hover:bg-gray-50 cursor-pointer transition-colors"
          :class="{
            'bg-cyan-50 border-l-4 border-cyan-400': isInClipboard(item, 'copy'),
            'bg-orange-50 border-l-4 border-orange-400': isInClipboard(item, 'cut'),
            'bg-blue-50': isSelected(item)
          }"
      >
        <td class="px-6 py-4 whitespace-nowrap">
          <input
            type="checkbox"
            @change="toggleSelectItem(item)"
            :checked="isSelected(item)"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            @click.stop
          >
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <!-- Pastas -->
          <div v-if="item.type === 'dir'" @click="$emit('navigate-to', item.path)" class="flex items-center text-blue-600 hover:text-blue-800 cursor-pointer group">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 mr-3 group-hover:bg-blue-200 transition-colors">
              <i class="fas fa-folder text-blue-500 text-sm"></i>
            </div>
            <span class="font-medium">{{ item.name }}/</span>
          </div>

          <!-- Imagens -->
          <div v-else-if="item.is_image" @click="$emit('open-image', item)" class="flex items-center text-gray-700 hover:text-green-600 cursor-pointer group">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 mr-3 group-hover:bg-green-200 transition-colors">
              <i class="fas fa-image text-green-500 text-sm"></i>
            </div>
            <span class="group-hover:font-medium transition-all">{{ item.name }}</span>
          </div>

          <!-- Outros arquivos -->
          <div v-else class="flex items-center text-gray-700">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg mr-3" :class="getFileIconBg(item)">
              <i :class="getFileIcon(item)" class="text-sm"></i>
            </div>
            <span>{{ item.name }}</span>
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <span v-if="item.type === 'dir'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <i class="fas fa-folder mr-1"></i> Pasta
          </span>
          <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            <i class="fas fa-file mr-1"></i> Arquivo
          </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
          <span v-if="item.type === 'file'" class="text-gray-900 font-medium">{{ item.size }}</span>
          <span v-else class="text-gray-400">—</span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex flex-col">
            <span class="text-gray-900">{{ formatDate(item.last_modified) }}</span>
            <span class="text-xs text-gray-400">{{ formatRelativeTime(item.last_modified) }}</span>
          </div>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
          <div class="flex items-center space-x-1">
            <!-- Download -->
            <button
              v-if="item.type === 'file'"
              @click="$emit('download', item.path)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200"
              title="Download"
            >
              <i class="fas fa-download text-sm"></i>
            </button>

            <!-- Visualizar imagem -->
            <button
              v-if="item.is_image"
              @click="$emit('open-image', item)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition-all duration-200"
              title="Visualizar imagem"
            >
              <i class="fas fa-eye text-sm"></i>
            </button>

            <!-- Descompactar ZIP -->
            <button
              v-if="item.is_zip"
              @click="$emit('unzip', item.path)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 transition-all duration-200"
              title="Descompactar arquivo"
            >
              <i class="fas fa-file-archive text-sm"></i>
            </button>

            <!-- Editar arquivo de texto -->
            <button
              v-if="item.is_editable"
              @click="$emit('edit', item.path)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-purple-600 hover:bg-purple-50 transition-all duration-200"
              title="Editar arquivo"
            >
              <i class="fas fa-code text-sm"></i>
            </button>

            <!-- Renomear -->
            <button
              @click="$emit('show-rename', item)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200"
              title="Renomear"
            >
              <i class="fas fa-pen text-sm"></i>
            </button>

            <!-- Copiar -->
            <button
              @click="$emit('copy', item)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-cyan-600 hover:bg-cyan-50 transition-all duration-200"
              :class="{ 'bg-cyan-100 text-cyan-600': isInClipboard(item, 'copy') }"
              title="Copiar"
            >
              <i class="fas fa-copy text-sm"></i>
            </button>

            <!-- Cortar (Mover) -->
            <button
              @click="$emit('cut', item)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-all duration-200"
              :class="{ 'bg-orange-100 text-orange-600': isInClipboard(item, 'cut') }"
              title="Cortar (mover)"
            >
              <i class="fas fa-cut text-sm"></i>
            </button>

            <!-- Excluir -->
            <button
              @click="$emit('delete', item)"
              class="group inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200"
              title="Excluir"
            >
              <i class="fas fa-trash text-sm"></i>
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
const props = defineProps({
  filteredContents: Array,
  selectedItems: Array,
  currentPath: String,
  clipboard: Object
})

const emit = defineEmits([
  'toggle-select-all',
  'toggle-select-item',
  'navigate-up',
  'navigate-to',
  'open-image',
  'download',
  'unzip',
  'edit',
  'show-rename',
  'copy',
  'cut',
  'delete'
])

const toggleSelectAll = (event) => {
  emit('toggle-select-all', event.target.checked)
}

const toggleSelectItem = (item) => {
  emit('toggle-select-item', item)
}

const isSelected = (item) => {
  return props.selectedItems.some(i => i.path === item.path)
}

const isInClipboard = (item, action) => {
  return props.clipboard?.action === action &&
         props.clipboard?.items?.some(clipItem => clipItem.path === item.path)
}

const getFileIcon = (item) => {
  const ext = item.extension?.toLowerCase()
  if (ext === 'php') return 'fas fa-code text-purple-500'
  if (['txt', 'log'].includes(ext)) return 'fas fa-file-alt text-gray-500'
  if (['html', 'htm'].includes(ext)) return 'fas fa-code text-orange-500'
  if (['js', 'ts'].includes(ext)) return 'fab fa-js-square text-yellow-500'
  if (['css', 'scss', 'sass'].includes(ext)) return 'fab fa-css3-alt text-blue-500'
  if (ext === 'zip') return 'fas fa-file-archive text-yellow-600'
  if (['pdf'].includes(ext)) return 'fas fa-file-pdf text-red-500'
  if (['doc', 'docx'].includes(ext)) return 'fas fa-file-word text-blue-600'
  if (['xls', 'xlsx'].includes(ext)) return 'fas fa-file-excel text-green-600'
  return 'fas fa-file text-gray-500'
}

const getFileIconBg = (item) => {
  const ext = item.extension?.toLowerCase()
  if (ext === 'php') return 'bg-purple-100'
  if (['txt', 'log'].includes(ext)) return 'bg-gray-100'
  if (['html', 'htm'].includes(ext)) return 'bg-orange-100'
  if (['js', 'ts'].includes(ext)) return 'bg-yellow-100'
  if (['css', 'scss', 'sass'].includes(ext)) return 'bg-blue-100'
  if (ext === 'zip') return 'bg-yellow-100'
  if (['pdf'].includes(ext)) return 'bg-red-100'
  if (['doc', 'docx'].includes(ext)) return 'bg-blue-100'
  if (['xls', 'xlsx'].includes(ext)) return 'bg-green-100'
  return 'bg-gray-100'
}

const formatDate = (timestamp) => {
  return new Date(timestamp * 1000).toLocaleString('pt-BR')
}

const formatRelativeTime = (timestamp) => {
  const now = new Date().getTime()
  const diff = now - timestamp * 1000
  if (diff < 60000) {
    const minutes = Math.round(diff / 60000)
    return `${minutes} min${minutes > 1 ? 's' : ''} atrás`
  } else if (diff < 3600000) {
    const hours = Math.round(diff / 3600000)
    return `${hours} hora${hours > 1 ? 's' : ''} atrás`
  } else if (diff < 86400000) {
    const days = Math.round(diff / 86400000)
    return `${days} dia${days > 1 ? 's' : ''} atrás`
  } else {
    const months = Math.round(diff / 2592000000)
    return `${months} mes${months > 1 ? 'es' : ''} atrás`
  }
}
</script>
