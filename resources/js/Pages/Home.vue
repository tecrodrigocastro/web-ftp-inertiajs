<template>
  <div class="bg-gray-50 h-screen flex flex-col">
    <!-- Alertas -->
    <Alert
      :message="success"
      type="success"
      @close="success = null"
    />

    <Alert
      :message="error"
      type="error"
      @close="error = null"
    />

    <!-- Top Navigation Bar -->
    <TopNavigation
      :current-path="currentPath"
      :ftp-info="ftpInfo"
      v-model:search-query="searchQuery"
      :selected-items="selectedItems"
      :clipboard-items="clipboard.items"
      :pasting="pasting"
      @show-upload-modal="showUploadModal = true"
      @show-folder-modal="showFolderModal = true"
      @copy-selected="copySelectedItems"
      @cut-selected="cutSelectedItems"
      @delete-selected="deleteSelectedItems"
      @clear-selection="clearSelection"
      @paste-items="pasteItems"
      @logout="logout"
    />

    <div class="flex flex-1 overflow-hidden">
      <!-- Sidebar -->
      <Sidebar
        :clipboard="clipboard"
        :pasting="pasting"
        @show-folder-modal="showFolderModal = true"
        @show-upload-modal="showUploadModal = true"
        @clear-clipboard="clearClipboard"
        @paste-items="pasteItems"
      />

      <!-- File Listing com Drag & Drop -->
      <div
        class="flex-1 overflow-auto relative"
        @dragover.prevent="handleGlobalDragOver"
        @dragenter.prevent="handleGlobalDragEnter"
        @dragleave.prevent="handleGlobalDragLeave"
        @drop.prevent="handleGlobalDrop"
      >
        <!-- Overlay de Drag & Drop Global -->
        <DragDropOverlay
          :show="isGlobalDragging || uploading"
          :uploading="uploading"
          :current-path="currentPath"
        />

        <!-- Tabela de arquivos -->
        <FileTable
          :filtered-contents="filteredContents"
          :selected-items="selectedItems"
          :current-path="currentPath"
          :clipboard="clipboard"
          @toggle-select-all="toggleSelectAll"
          @toggle-select-item="toggleSelectItem"
          @navigate-up="navigateToPath(getParentPath())"
          @navigate-to="navigateToPath"
          @open-image="openImageViewer"
          @download="downloadFile"
          @unzip="unzipFile"
          @edit="editFile"
          @show-rename="showRenameModal"
          @copy="copyItem"
          @cut="cutItem"
          @delete="deleteItem"
        />
      </div>
    </div>

    <!-- Modais -->
    <UploadModal
      :show="showUploadModal"
      :uploading="uploading"
      @close="showUploadModal = false"
      @upload="uploadFiles"
    />

    <FolderModal
      :show="showFolderModal"
      @close="showFolderModal = false"
      @create="createFolder"
    />

    <RenameModal
      :show="showRenameModalFlag"
      :item="itemToRename"
      @close="showRenameModalFlag = false"
      @rename="renameItem"
    />

    <!-- Visualizador de Imagem -->
    <ImageViewer
      :show="showImageViewer"
      :image-url="currentImageUrl"
      :image-title="currentImageTitle"
      @close="closeImageViewer"
      @download="downloadFile(currentImagePath)"
    />
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { computed, defineProps, onMounted, ref, watch } from 'vue'

// Importar componentes
import Alert from '@/Components/Alert.vue'
import TopNavigation from '@/Components/TopNavigation.vue'
import Sidebar from '@/Components/Sidebar.vue'
import FileTable from '@/Components/FileTable.vue'
import UploadModal from '@/Components/UploadModal.vue'
import FolderModal from '@/Components/FolderModal.vue'
import RenameModal from '@/Components/RenameModal.vue'
import ImageViewer from '@/Components/ImageViewer.vue'
import DragDropOverlay from '@/Components/DragDropOverlay.vue'

// Props recebidos do controller
const props = defineProps({
  files: Array,
  contents: Array,
  currentPath: String,
  path: String,
  search: String,
  success: String,
  error: String,
  ftpInfo: Object
})

// Estados essenciais
const searchQuery = ref(props.search || '')
const showUploadModal = ref(false)
const showFolderModal = ref(false)
const showRenameModalFlag = ref(false)
const itemToRename = ref(null)
const uploading = ref(false)
const pasting = ref(false)
const selectedItems = ref([])

// Estado do clipboard
const clipboard = ref({
  items: [],
  action: null
})

// Estados para visualizador de imagem
const showImageViewer = ref(false)
const currentImageUrl = ref('')
const currentImageTitle = ref('')
const currentImagePath = ref('')

// Estados para mensagens
const success = ref(props.success)
const error = ref(props.error)

// Estado para drag and drop global
const isGlobalDragging = ref(false)
const globalDragCounter = ref(0)

// Montar componente - restaurar clipboard do localStorage
onMounted(() => {
  loadClipboardFromStorage()

  // Verificar se é o mesmo servidor FTP
  const currentServer = props.ftpInfo?.host + '_' + props.ftpInfo?.username
  const savedServer = localStorage.getItem('webftp_server')

  if (savedServer && savedServer !== currentServer) {
    // Servidor diferente, limpar clipboard
    clearClipboard()
    console.log('Servidor FTP mudou, clipboard limpo')
  }

  // Salvar servidor atual
  localStorage.setItem('webftp_server', currentServer)

  // Debug: mostrar status do clipboard
  if (clipboard.value.items.length > 0) {
    console.log(`Clipboard restaurado: ${clipboard.value.items.length} item(s) (${clipboard.value.action})`)

    // Mostrar notificação discreta
    setTimeout(() => {
      if (clipboard.value.items.length > 0) {
        const action = clipboard.value.action === 'copy' ? 'copiados' : 'cortados'
        console.log(`💾 ${clipboard.value.items.length} item(s) ${action} disponível(is) para colar`)
      }
    }, 100)
  }

  // Adicionar event listeners globais para drag and drop
  setupGlobalDragListeners()
})

// Função para configurar listeners globais de drag and drop
const setupGlobalDragListeners = () => {
  // Prevenir comportamento padrão do navegador
  const preventDefaults = (e) => {
    e.preventDefault()
    e.stopPropagation()
  }

  // Eventos que devem ser prevenidos
  const events = ['dragenter', 'dragover', 'dragleave', 'drop']
  events.forEach(eventName => {
    document.addEventListener(eventName, preventDefaults, false)
  })

  // Mostrar overlay quando arquivos entram na janela
  document.addEventListener('dragenter', (e) => {
    if (e.dataTransfer.types.includes('Files')) {
      globalDragCounter.value++
      isGlobalDragging.value = true
    }
  })

  // Esconder overlay quando arquivos saem da janela
  document.addEventListener('dragleave', (e) => {
    globalDragCounter.value--
    if (globalDragCounter.value <= 0) {
      isGlobalDragging.value = false
      globalDragCounter.value = 0
    }
  })
}

// Observar mudanças no clipboard para salvar no localStorage
watch(clipboard, (newClipboard) => {
  saveClipboardToStorage(newClipboard)
}, { deep: true })

// Funções para persistir clipboard
const saveClipboardToStorage = (clipboardData) => {
  try {
    localStorage.setItem('webftp_clipboard', JSON.stringify(clipboardData))
  } catch (error) {
    console.error('Erro ao salvar clipboard:', error)
  }
}

const loadClipboardFromStorage = () => {
  try {
    const saved = localStorage.getItem('webftp_clipboard')
    if (saved) {
      const parsedClipboard = JSON.parse(saved)
      // Verificar se os dados são válidos
      if (parsedClipboard && Array.isArray(parsedClipboard.items)) {
        clipboard.value = parsedClipboard
      }
    }
  } catch (error) {
    console.error('Erro ao carregar clipboard:', error)
    clearClipboardStorage()
  }
}

const clearClipboardStorage = () => {
  try {
    localStorage.removeItem('webftp_clipboard')
  } catch (error) {
    console.error('Erro ao limpar clipboard:', error)
  }
}

// Computed properties
const currentPath = computed(() => props.currentPath || props.path || '/')

const filteredContents = computed(() => {
  const items = props.files || props.contents || []
  if (!searchQuery.value) return items
  return items.filter(item =>
    item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Métodos
const navigateToPath = (path) => {
  router.get('/', { path })
}

const getParentPath = () => {
  const parts = currentPath.value.split('/').filter(p => p)
  parts.pop()
  return parts.length === 0 ? '/' : '/' + parts.join('/')
}

const uploadFiles = (files) => {
  if (files.length === 0) return

  uploading.value = true
  const formData = new FormData()

  files.forEach((file) => {
    formData.append('files[]', file)
  })
  formData.append('path', currentPath.value)

  router.post('/upload', formData, {
    onSuccess: () => {
      showUploadModal.value = false
      uploading.value = false
    },
    onError: () => {
      uploading.value = false
    }
  })
}

const createFolder = (name) => {
  router.post('/create-folder', {
    name: name,
    path: currentPath.value
  }, {
    onSuccess: () => {
      showFolderModal.value = false
    }
  })
}

const showRenameModal = (item) => {
  itemToRename.value = item
  showRenameModalFlag.value = true
}

const renameItem = (newName) => {
  router.post('/rename', {
    oldPath: itemToRename.value.path,
    newName: newName
  }, {
    onSuccess: () => {
      showRenameModalFlag.value = false
      itemToRename.value = null
    }
  })
}

const deleteItem = (item) => {
  if (confirm(`Tem certeza que deseja excluir ${item.name}?`)) {
    router.delete(`/delete/${item.path}`)
  }
}

const downloadFile = (path) => {
  window.open(`/download/${path}`, '_blank')
}

const unzipFile = (path) => {
  router.post(`/unzip/${path}`)
}

const editFile = (path) => {
  router.get(`/edit/${path}`)
}

const openImageViewer = (item) => {
  currentImageUrl.value = `/raw/${item.path}`
  currentImageTitle.value = item.name
  currentImagePath.value = item.path
  showImageViewer.value = true
}

const closeImageViewer = () => {
  showImageViewer.value = false
  currentImageUrl.value = ''
  currentImageTitle.value = ''
  currentImagePath.value = ''
}

const copyItem = (item) => {
  clipboard.value.items = [item]
  clipboard.value.action = 'copy'
}

const cutItem = (item) => {
  clipboard.value.items = [item]
  clipboard.value.action = 'cut'
}

const pasteItems = () => {
  if (clipboard.value.items.length === 0) return

  pasting.value = true

  const data = {
    items: clipboard.value.items.map(item => ({
      path: item.path,
      name: item.name,
      type: item.type
    })),
    action: clipboard.value.action,
    destination: currentPath.value
  }

  const endpoint = clipboard.value.action === 'copy' ? '/copy-items' : '/move-items'

  router.post(endpoint, data, {
    onSuccess: () => {
      if (clipboard.value.action === 'cut') {
        clearClipboard()
      }
      pasting.value = false
    },
    onError: () => {
      pasting.value = false
    }
  })
}

const toggleSelectAll = (checked) => {
  selectedItems.value = checked
    ? filteredContents.value.map(item => ({ ...item }))
    : []
}

const toggleSelectItem = (item) => {
  const index = selectedItems.value.findIndex(i => i.path === item.path)
  if (index >= 0) {
    selectedItems.value.splice(index, 1)
  } else {
    selectedItems.value.push(item)
  }
}

const clearSelection = () => {
  selectedItems.value = []
}

const copySelectedItems = () => {
  if (selectedItems.value.length === 0) return
  clipboard.value.items = [...selectedItems.value]
  clipboard.value.action = 'copy'
  clearSelection()
}

const cutSelectedItems = () => {
  if (selectedItems.value.length === 0) return
  clipboard.value.items = [...selectedItems.value]
  clipboard.value.action = 'cut'
  clearSelection()
}

const deleteSelectedItems = () => {
  if (selectedItems.value.length === 0) return

  const itemNames = selectedItems.value.map(item => item.name).join(', ')
  if (confirm(`Tem certeza que deseja excluir ${selectedItems.value.length} item(s)?\n\n${itemNames}`)) {
    selectedItems.value.forEach(item => {
      router.delete(`/delete/${item.path}`)
    })
    clearSelection()
  }
}

const logout = () => {
  router.post('/logout')
}

// Métodos para handle de drag and drop global
const handleGlobalDragOver = (event) => {
  event.preventDefault()
  event.dataTransfer.dropEffect = 'copy'
}

const handleGlobalDragEnter = (event) => {
  event.preventDefault()
  globalDragCounter.value++
  if (event.dataTransfer.types.includes('Files')) {
    isGlobalDragging.value = true
  }
}

const handleGlobalDragLeave = (event) => {
  event.preventDefault()
  globalDragCounter.value--
  if (globalDragCounter.value <= 0) {
    isGlobalDragging.value = false
    globalDragCounter.value = 0
  }
}

const handleGlobalDrop = (event) => {
  event.preventDefault()
  isGlobalDragging.value = false
  globalDragCounter.value = 0

  const files = Array.from(event.dataTransfer.files)
  if (files.length > 0) {
    uploadDroppedFiles(files)
  }
}

const uploadDroppedFiles = (files) => {
  if (uploading.value) return

  uploading.value = true
  const formData = new FormData()

  files.forEach((file) => {
    formData.append('files[]', file)
  })
  formData.append('path', currentPath.value)

  router.post('/upload', formData, {
    onSuccess: () => {
      uploading.value = false
    },
    onError: () => {
      uploading.value = false
    }
  })
}
</script>

<style scoped>
.transition-colors {
  transition: background-color 0.2s ease;
}
</style>
