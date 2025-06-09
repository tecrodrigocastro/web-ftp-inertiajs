<template>
  <div v-if="show" class="fixed inset-0  bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
          <i class="fas fa-file-archive text-yellow-600 mr-2"></i>
          Descompactando Arquivo
        </h3>
        <button
          v-if="!processing"
          @click="$emit('close')"
          class="text-gray-400 hover:text-gray-600"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="space-y-4">
        <!-- Informações do arquivo -->
        <div class="bg-gray-50 rounded-lg p-3">
          <div class="text-sm text-gray-600">Arquivo:</div>
          <div class="font-medium text-gray-900 break-all">{{ fileName }}</div>
        </div>

        <!-- Status -->
        <div class="text-center">
          <div v-if="processing" class="space-y-3">
            <!-- Spinner -->
            <div class="flex justify-center">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-yellow-600"></div>
            </div>

            <div class="text-sm text-gray-600">
              {{ status }}
            </div>

            <!-- Progresso (se disponível) -->
            <div v-if="progress.total > 0" class="space-y-2">
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div
                  class="bg-yellow-600 h-2 rounded-full transition-all duration-300"
                  :style="{ width: `${(progress.current / progress.total) * 100}%` }"
                ></div>
              </div>
              <div class="text-xs text-gray-500">
                {{ progress.current }} de {{ progress.total }} arquivos processados
              </div>
            </div>
          </div>

          <!-- Resultado -->
          <div v-else-if="result" class="space-y-3">
            <div v-if="result.success" class="text-green-600">
              <i class="fas fa-check-circle text-2xl mb-2"></i>
              <div class="font-medium">{{ result.message }}</div>
              <div v-if="result.errors && result.errors.length > 0" class="mt-2 text-left">
                <div class="text-sm text-orange-600 font-medium">Avisos:</div>
                <div class="text-xs text-orange-600 bg-orange-50 rounded p-2 mt-1 max-h-32 overflow-y-auto">
                  <div v-for="error in result.errors" :key="error" class="mb-1">
                    {{ error }}
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-red-600">
              <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
              <div class="font-medium">{{ result.message }}</div>
            </div>
          </div>
        </div>

        <!-- Botões -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
          <button
            v-if="!processing"
            @click="$emit('close')"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
          >
            Fechar
          </button>
          <button
            v-if="result && result.success"
            @click="$emit('refresh')"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
          >
            <i class="fas fa-sync-alt mr-1"></i>
            Atualizar Lista
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { defineEmits, defineProps, ref, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  filePath: String,
  fileName: String
})

const emit = defineEmits(['close', 'refresh'])

const processing = ref(false)
const status = ref('')
const progress = ref({ current: 0, total: 0 })
const result = ref(null)

// Função para iniciar o unzip
const startUnzip = async () => {
  if (!props.filePath) return

  processing.value = true
  status.value = 'Baixando arquivo ZIP do servidor...'
  progress.value = { current: 0, total: 0 }
  result.value = null

  try {
    // Fazer request para a API de unzip assíncrono
    status.value = 'Descompactando arquivo...'

    const response = await axios.post(`/unzip-async/${props.filePath}`, {}, {
      timeout: 300000, // 5 minutos de timeout
      onDownloadProgress: (progressEvent) => {
        // Pode ser usado para mostrar progresso do download se necessário
      }
    })

    result.value = response.data

  } catch (error) {
    console.error('Erro no unzip:', error)

    if (error.response && error.response.data) {
      result.value = {
        success: false,
        message: error.response.data.message || 'Erro desconhecido ao descompactar arquivo'
      }
    } else if (error.code === 'ECONNABORTED') {
      result.value = {
        success: false,
        message: 'Timeout: A operação demorou muito para ser concluída. Tente novamente ou verifique o tamanho do arquivo.'
      }
    } else {
      result.value = {
        success: false,
        message: 'Erro de conexão. Verifique sua internet e tente novamente.'
      }
    }
  } finally {
    processing.value = false
  }
}

// Observar quando o modal é mostrado para iniciar o processo
watch(() => props.show, (newVal) => {
  if (newVal && props.filePath) {
    // Resetar estado
    processing.value = false
    status.value = ''
    progress.value = { current: 0, total: 0 }
    result.value = null

    // Iniciar processo após um pequeno delay
    setTimeout(() => {
      startUnzip()
    }, 300)
  }
})
</script>
