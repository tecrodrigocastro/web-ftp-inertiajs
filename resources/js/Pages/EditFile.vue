<template>
  <div class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Top Navigation Bar -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-4 py-3 flex items-center justify-between">
      <div class="flex items-center">
        <button @click="goBack" class="text-purple-600 hover:text-purple-700 flex items-center">
          <i class="fas fa-server mr-2"></i>
          <span class="font-bold text-xl">KingWebFTP</span>
        </button>
        <div class="text-gray-400 mx-2">/</div>
        <div class="flex items-center space-x-1 text-gray-600 text-sm">
          <!-- Breadcrumb navegável -->
          <button @click="navigateToPath('/')" class="hover:text-purple-600 breadcrumb-item">Home</button>
          <span v-for="(part, index) in pathParts" :key="index" class="flex items-center">
            <span v-if="index < pathParts.length - 1">
              <button @click="navigateToPath(getPathUpTo(index))" class="hover:text-purple-600 breadcrumb-item">{{ part }}</button>
            </span>
            <span v-else class="text-gray-700 font-medium breadcrumb-item">{{ part }}</span>
          </span>
        </div>
      </div>
      <div class="flex items-center space-x-2">
        <button @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm flex items-center">
          <i class="fas fa-arrow-left mr-2"></i>Voltar
        </button>
      </div>
    </div>

    <!-- Alertas -->
    <div v-if="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-4 mt-4 rounded relative" role="alert">
      <span class="block sm:inline">{{ successMessage }}</span>
      <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="successMessage = null">
        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
        </svg>
      </span>
    </div>

    <div v-if="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-4 mt-4 rounded relative" role="alert">
      <span class="block sm:inline">{{ errorMessage }}</span>
      <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="errorMessage = null">
        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
        </svg>
      </span>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-4">
          <h1 class="text-xl font-bold text-gray-800 flex items-center">
            <i :class="getFileIcon()" class="mr-2"></i>
            Editando: {{ filename }}
          </h1>
          <div class="text-sm text-gray-500">
            {{ fileSize }}
          </div>
        </div>

        <form @submit.prevent="saveFile">
          <div class="editor-container mb-4 relative">
            <!-- Ações do Editor -->
            <div class="editor-actions absolute top-2 right-2 z-10 flex space-x-1">
              <button type="button" @click="toggleFullscreen" :title="isFullscreen ? 'Sair da tela cheia' : 'Tela cheia'" class="editor-action-btn">
                <i :class="isFullscreen ? 'fas fa-compress' : 'fas fa-expand'"></i>
              </button>
              <button type="button" @click="toggleTheme" title="Alternar tema" class="editor-action-btn">
                <i :class="isDarkTheme ? 'fas fa-sun' : 'fas fa-adjust'"></i>
              </button>
              <button type="button" @click="copyAll" title="Copiar tudo" class="editor-action-btn">
                <i :class="copySuccess ? 'fas fa-check' : 'fas fa-copy'"></i>
              </button>
              <button type="button" @click="findText" title="Buscar (Ctrl+F)" class="editor-action-btn">
                <i class="fas fa-search"></i>
              </button>
            </div>

            <!-- Editor CodeMirror -->
            <div ref="editorContainer" class="editor-wrapper">
              <textarea ref="codeEditor" v-model="fileContent"></textarea>
            </div>
          </div>

          <div class="flex justify-between items-center mt-4">
            <div class="flex flex-col">
              <span class="text-sm text-gray-600">{{ cursorInfo }}</span>
              <span class="text-xs text-gray-400">{{ fileInfo }}</span>
            </div>
            <div class="space-x-2 flex">
              <button type="button" @click="goBack" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded flex items-center">
                <i class="fas fa-times mr-2"></i>Cancelar
              </button>
              <button type="submit" :disabled="saving" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded flex items-center disabled:opacity-50">
                <i :class="saving ? 'fas fa-spinner fa-spin mr-2' : 'fas fa-save mr-2'"></i>
                {{ saving ? 'Salvando...' : 'Salvar' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { computed, defineProps, nextTick, onMounted, onUnmounted, ref } from 'vue'

// Props recebidos do controller
const props = defineProps({
  path: String,
  content: String,
  filename: String
})

// Estados reativos
const fileContent = ref(props.content || '')
const saving = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)
const isDirty = ref(false)
const isDarkTheme = ref(true)
const isFullscreen = ref(false)
const copySuccess = ref(false)
const cursorInfo = ref('Linha: 1, Coluna: 1')

// Referências do template
const codeEditor = ref(null)
const editorContainer = ref(null)

// CodeMirror instance
let editor = null

// Computed properties
const fileExtension = computed(() => {
  return props.filename?.split('.').pop()?.toLowerCase() || 'txt'
})

const pathParts = computed(() => {
  return props.path.split('/').filter(p => p)
})

const fileSize = computed(() => {
  const size = new Blob([fileContent.value]).size
  if (size > 1024 * 1024) {
    return `${(size / (1024 * 1024)).toFixed(2)} MB`
  } else if (size > 1024) {
    return `${(size / 1024).toFixed(2)} KB`
  }
  return `${size} bytes`
})

const fileInfo = computed(() => {
  const mode = getLanguageMode()
  return `Tipo: ${fileExtension.value.toUpperCase()} | Modo: ${mode}`
})

// Métodos
const getFileIcon = () => {
  const ext = fileExtension.value
  const iconMap = {
    'php': 'fab fa-php text-purple-500',
    'html': 'fab fa-html5 text-orange-500',
    'htm': 'fab fa-html5 text-orange-500',
    'css': 'fab fa-css3-alt text-blue-500',
    'js': 'fab fa-js text-yellow-500',
    'json': 'fas fa-code text-green-500',
    'md': 'fab fa-markdown text-gray-600',
    'txt': 'fas fa-file-alt text-gray-500',
    'log': 'fas fa-file-alt text-gray-500'
  }
  return iconMap[ext] || 'fas fa-file-code text-purple-600'
}

const getLanguageMode = () => {
  const ext = fileExtension.value
  const modeMap = {
    'php': 'application/x-httpd-php',
    'html': 'text/html',
    'htm': 'text/html',
    'js': 'text/javascript',
    'css': 'text/css',
    'xml': 'application/xml',
    'json': 'application/json',
    'md': 'text/x-markdown'
  }
  return modeMap[ext] || 'text/plain'
}

const getPathUpTo = (index) => {
  return '/' + pathParts.value.slice(0, index + 1).join('/')
}

const navigateToPath = (path) => {
  router.get('/', { path })
}

const initCodeMirror = async () => {
  // Aguardar carregamento dos scripts CDN
  await loadCodeMirrorCDN()

  // Usar CodeMirror global
  const CodeMirror = window.CodeMirror

  // Criar editor
  editor = CodeMirror.fromTextArea(codeEditor.value, {
    lineNumbers: true,
    mode: getLanguageMode(),
    theme: 'dracula',
    indentUnit: 4,
    smartIndent: true,
    indentWithTabs: false,
    matchBrackets: true,
    styleActiveLine: true,
    lineWrapping: false,
    extraKeys: {
      'Ctrl-S': () => saveFile(),
      'Cmd-S': () => saveFile(),
      'Ctrl-F': 'findPersistent',
      'Cmd-F': 'findPersistent',
      'Ctrl-H': 'replace',
      'Cmd-H': 'replace'
    }
  })

  editor.setSize('100%', 'calc(100vh - 300px)')

  // Event listeners
  editor.on('cursorActivity', () => {
    const pos = editor.getCursor()
    cursorInfo.value = `Linha: ${pos.line + 1}, Coluna: ${pos.ch + 1}`
  })

  editor.on('change', () => {
    isDirty.value = true
    fileContent.value = editor.getValue()
  })
}

const loadCodeMirrorCDN = () => {
  return new Promise((resolve, reject) => {
    // Se já carregou, retorna
    if (window.CodeMirror) {
      resolve()
      return
    }

    // CSS Files
    const cssFiles = [
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/dracula.min.css',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/dialog/dialog.min.css'
    ]

    // JS Files em ordem de dependência
    const jsFiles = [
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/xml/xml.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/javascript/javascript.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/css/css.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/htmlmixed/htmlmixed.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/php/php.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/json/json.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/markdown/markdown.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/edit/matchbrackets.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/selection/active-line.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/search/searchcursor.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/dialog/dialog.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/search/search.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/search/jump-to-line.min.js',
      'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/addon/search/match-highlighter.min.js'
    ]

    // Função para carregar CSS
    const loadCSS = () => {
      return Promise.all(cssFiles.map(href => {
        return new Promise((resolveCSS) => {
          // Verificar se já existe
          if (document.querySelector(`link[href="${href}"]`)) {
            resolveCSS()
            return
          }

          const link = document.createElement('link')
          link.rel = 'stylesheet'
          link.href = href
          link.onload = () => resolveCSS()
          link.onerror = () => resolveCSS() // Continua mesmo se falhar
          document.head.appendChild(link)
        })
      }))
    }

    // Função para carregar JS sequencialmente
    const loadJS = () => {
      return jsFiles.reduce((promise, src) => {
        return promise.then(() => {
          return new Promise((resolveJS, rejectJS) => {
            // Verificar se já existe
            if (document.querySelector(`script[src="${src}"]`)) {
              resolveJS()
              return
            }

            const script = document.createElement('script')
            script.src = src
            script.onload = () => resolveJS()
            script.onerror = () => {
              console.warn(`Falha ao carregar: ${src}`)
              resolveJS() // Continua mesmo se falhar
            }
            document.head.appendChild(script)
          })
        })
      }, Promise.resolve())
    }

    // Carregar CSS primeiro, depois JS
    loadCSS()
      .then(() => loadJS())
      .then(() => {
        // Aguardar um pouco para garantir que tudo carregou
        setTimeout(() => {
          if (window.CodeMirror) {
            resolve()
          } else {
            reject(new Error('CodeMirror não carregou corretamente'))
          }
        }, 100)
      })
      .catch(reject)
  })
}

const saveFile = async () => {
  if (saving.value) return

  saving.value = true
  isDirty.value = false

  try {
    await router.post(`/edit/${props.path}`, {
      content: fileContent.value
    }, {
      onSuccess: () => {
        successMessage.value = 'Arquivo salvo com sucesso!'
        setTimeout(() => successMessage.value = null, 3000)
      },
      onError: (errors) => {
        console.error('Erro ao salvar:', errors)
        errorMessage.value = 'Erro ao salvar o arquivo!'
        setTimeout(() => errorMessage.value = null, 5000)
      },
      onFinish: () => {
        saving.value = false
      }
    })
  } catch (error) {
    console.error('Erro ao salvar arquivo:', error)
    errorMessage.value = 'Erro ao salvar o arquivo!'
    setTimeout(() => errorMessage.value = null, 5000)
    saving.value = false
  }
}

const goBack = () => {
  if (isDirty.value && !confirm('Você tem alterações não salvas. Deseja continuar?')) {
    return
  }
  const parentPath = props.path.split('/').slice(0, -1).join('/') || '/'
  router.get('/', { path: parentPath })
}

const toggleTheme = () => {
  if (!editor) return
  isDarkTheme.value = !isDarkTheme.value
  editor.setOption('theme', isDarkTheme.value ? 'dracula' : 'default')
}

const toggleFullscreen = () => {
  if (!editor || !editorContainer.value) return

  isFullscreen.value = !isFullscreen.value
  const container = editorContainer.value

  if (isFullscreen.value) {
    container.style.position = 'fixed'
    container.style.top = '0'
    container.style.left = '0'
    container.style.right = '0'
    container.style.bottom = '0'
    container.style.zIndex = '1000'
    container.style.backgroundColor = '#282a36'
    container.style.padding = '20px'
    editor.setSize('100%', '100%')
  } else {
    container.style.position = 'relative'
    container.style.top = ''
    container.style.left = ''
    container.style.right = ''
    container.style.bottom = ''
    container.style.zIndex = ''
    container.style.backgroundColor = ''
    container.style.padding = ''
    editor.setSize('100%', 'calc(100vh - 300px)')
  }
  editor.refresh()
}

const copyAll = async () => {
  try {
    await navigator.clipboard.writeText(fileContent.value)
    copySuccess.value = true
    setTimeout(() => copySuccess.value = false, 1200)
  } catch (err) {
    console.error('Erro ao copiar:', err)
  }
}

const findText = () => {
  if (editor && editor.execCommand) {
    editor.execCommand('findPersistent')
  }
}

// Lifecycle hooks
onMounted(async () => {
  await nextTick()
  await initCodeMirror()
})

onUnmounted(() => {
  if (editor) {
    editor.toTextArea()
  }
})

// Aviso de mudanças não salvas
const handleBeforeUnload = (e) => {
  if (isDirty.value) {
    e.preventDefault()
    e.returnValue = ''
  }
}

onMounted(() => {
  window.addEventListener('beforeunload', handleBeforeUnload)
})

onUnmounted(() => {
  window.removeEventListener('beforeunload', handleBeforeUnload)
})
</script>

<style scoped>
.editor-container {
  position: relative;
}

.editor-wrapper {
  border-radius: 0.375rem;
  overflow: hidden;
}

.editor-action-btn {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
  padding: 6px 10px;
  color: white;
  font-size: 12px;
  transition: background 0.2s;
  border: none;
  cursor: pointer;
}

.editor-action-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.breadcrumb-item::after {
  content: '/';
  margin: 0 0.5rem;
  color: #6b7280;
}

.breadcrumb-item:last-child::after {
  content: '';
}

/* CodeMirror customizations */
:deep(.CodeMirror) {
  height: calc(100vh - 300px) !important;
  border-radius: 0.375rem;
  font-family: 'JetBrains Mono', 'Consolas', monospace !important;
  font-size: 14px !important;
}

:deep(.CodeMirror-focused .CodeMirror-selected) {
  background: rgba(255, 255, 255, 0.1);
}
</style>
