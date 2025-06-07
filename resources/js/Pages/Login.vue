<template>
  <div class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Alertas -->
    <div v-if="successMessage" class="fixed top-4 left-4 right-4 z-50">
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative max-w-md mx-auto" role="alert">
        <span class="block sm:inline">{{ successMessage }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="successMessage = null">
          <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
          </svg>
        </span>
      </div>
    </div>

    <div v-if="errorMessage" class="fixed top-4 left-4 right-4 z-50">
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative max-w-md mx-auto" role="alert">
        <span class="block sm:inline">{{ errorMessage }}</span>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="errorMessage = null">
          <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
          </svg>
        </span>
      </div>
    </div>

    <div class="flex-1 flex flex-col lg:flex-row">
      <!-- Coluna da esquerda (login) -->
      <div class="lg:w-1/2 flex items-center justify-center p-6">
        <div class="max-w-md w-full">
          <!-- Card principal -->
          <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header com gradiente -->
            <div class="bg-gradient p-6 text-center">
              <div class="inline-block bg-white p-3 rounded-full mb-4">
                <i class="fas fa-server text-purple-600 text-3xl"></i>
              </div>
              <h1 class="text-2xl font-bold text-white">KingWebFTP</h1>
              <p class="text-purple-100 mt-2">Gerenciador de arquivos</p>
            </div>

            <!-- Formulário de login -->
            <div class="p-6">
              <form @submit.prevent="submitLogin" class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Host</label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-600">
                      <i class="fas fa-server"></i>
                    </span>
                    <input
                      v-model="form.host"
                      type="text"
                      placeholder="ftp.example.com"
                      class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200"
                      required
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Usuário</label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-600">
                      <i class="fas fa-user"></i>
                    </span>
                    <input
                      v-model="form.username"
                      type="text"
                      placeholder="username"
                      class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200"
                      required
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-600">
                      <i class="fas fa-lock"></i>
                    </span>
                    <input
                      v-model="form.password"
                      type="password"
                      placeholder="********"
                      class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200"
                      required
                    >
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Porta</label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-600">
                        <i class="fas fa-plug"></i>
                      </span>
                      <input
                        v-model="form.port"
                        type="number"
                        class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200"
                        required
                      >
                    </div>
                  </div>

                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diretório</label>
                    <div class="relative">
                      <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-purple-600">
                        <i class="fas fa-folder-open"></i>
                      </span>
                      <input
                        v-model="form.root"
                        type="text"
                        class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-200"
                        required
                      >
                    </div>
                  </div>
                </div>

                <button
                  type="submit"
                  :disabled="submitting"
                  class="w-full py-2 px-4 bg-purple-600 hover:bg-purple-700 text-white font-bold rounded-lg transition duration-200 flex items-center justify-center disabled:opacity-50"
                >
                  <i class="fas fa-sign-in-alt mr-2"></i>
                  {{ submitting ? 'Entrando...' : 'Entrar com dados informados' }}
                </button>
              </form>

              <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                  <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                  <span class="px-2 bg-white text-gray-500">ou</span>
                </div>
              </div>

              <button
                @click="loginWithEnv"
                :disabled="submittingEnv"
                class="w-full py-2 px-4 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-lg transition duration-200 flex items-center justify-center disabled:opacity-50"
              >
                <i class="fas fa-cog mr-2"></i>
                {{ submittingEnv ? 'Entrando...' : 'Entrar com dados do sistema' }}
              </button>
            </div>
          </div>

          <div class="text-center mt-6 text-gray-500 text-sm hidden lg:block">
            <p>&copy; {{ currentYear }} KingWebFTP. Todos os direitos reservados.</p>
          </div>
        </div>
      </div>

      <!-- Coluna da direita (funcionalidades) -->
      <div class="lg:w-1/2 bg-purple-50 p-8 flex items-center justify-center">
        <div class="max-w-2xl w-full">
          <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Depois de logado no WebFTP, você poderá:</h2>
          </div>

          <div class="grid md:grid-cols-2 gap-4">
            <!-- Feature cards -->
            <div v-for="feature in features" :key="feature.id" class="feature-card bg-white p-4 rounded-lg shadow">
              <div class="flex items-start">
                <div class="feature-icon">
                  <i :class="feature.icon"></i>
                </div>
                <div>
                  <h3 class="font-bold text-gray-800">{{ feature.title }}</h3>
                  <p class="text-sm text-gray-600">{{ feature.description }}</p>
                </div>
              </div>
            </div>
          </div>

          <div class="text-center mt-6 text-gray-500 text-sm lg:hidden">
            <p>&copy; {{ currentYear }} KingWebFTP. Todos os direitos reservados.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, defineProps } from 'vue'
import { router } from '@inertiajs/vue3'

// Props recebidos do controller
const props = defineProps({
  success: String,
  error: String
})

// Estados reativos
const form = ref({
  host: '',
  username: '',
  password: '',
  port: 21,
  root: '/'
})

const submitting = ref(false)
const submittingEnv = ref(false)
const successMessage = ref(props.success)
const errorMessage = ref(props.error)

// Computed properties
const currentYear = computed(() => new Date().getFullYear())

// Features data
const features = ref([
  {
    id: 1,
    icon: 'fas fa-sitemap',
    title: 'Navegar no servidor de FTP',
    description: 'Explore sua conta através do navegador.'
  },
  {
    id: 2,
    icon: 'fas fa-exchange-alt',
    title: 'Transferir arquivos',
    description: 'Ideal para exportar arquivos de um servidor FTP para outro.'
  },
  {
    id: 3,
    icon: 'fas fa-upload',
    title: 'Fazer upload de arquivos',
    description: 'Envio simples, envio e descompactação (upload-and-unzip).'
  },
  {
    id: 4,
    icon: 'fas fa-key',
    title: 'Renomear e atribuir permissões',
    description: 'Chmod recursivo em diretórios.'
  },
  {
    id: 5,
    icon: 'fas fa-download',
    title: 'Fazer download de arquivos',
    description: 'Simples e múltiplos - selecione e clique "download" para baixá-los.'
  },
  {
    id: 6,
    icon: 'fas fa-code',
    title: 'Ver códigos com syntax highlighting',
    description: 'Funções de PHP relacionadas com a documentação do php.net.'
  },
  {
    id: 7,
    icon: 'fas fa-file-archive',
    title: 'Compactar arquivos',
    description: 'Reúne diversos arquivos em um zip no próprio servidor ou encaminhe por email.'
  },
  {
    id: 8,
    icon: 'fas fa-edit',
    title: 'Editar textos online',
    description: 'Edite textos dentro do navegador e salve automaticamente no servidor.'
  },
  {
    id: 9,
    icon: 'fas fa-file-archive',
    title: 'Descompactar arquivos',
    description: 'Suporte a diferentes formatos: zip, tar, tgz and gz.'
  },
  {
    id: 10,
    icon: 'fas fa-code',
    title: 'Editar HTML e PHP',
    description: '2 Diferentes editores visuais de HTML de fácil utilização.'
  },
  {
    id: 11,
    icon: 'fas fa-search',
    title: 'Pesquisar',
    description: 'Encontre arquivos por nome, data de modificação ou tamanho.'
  },
  {
    id: 12,
    icon: 'fas fa-calculator',
    title: 'Calcular tamanho',
    description: 'Calcule o tamanho de diretórios e arquivos.'
  },
  {
    id: 13,
    icon: 'fas fa-copy',
    title: 'Copiar, mover e deletar',
    description: 'Tratamento recursivo aos diretórios: o conteúdo também será copiado, movido ou excluído.'
  }
])

// Métodos
const submitLogin = () => {
  if (submitting.value) return

  submitting.value = true

  router.post('/login', form.value, {
    onSuccess: () => {
      // Será redirecionado automaticamente
    },
    onError: (errors) => {
      errorMessage.value = 'Erro ao fazer login. Verifique os dados informados.'
      setTimeout(() => errorMessage.value = null, 5000)
    },
    onFinish: () => {
      submitting.value = false
    }
  })
}

const loginWithEnv = () => {
  if (submittingEnv.value) return

  submittingEnv.value = true

  router.post('/login-env', {}, {
    onSuccess: () => {
      // Será redirecionado automaticamente
    },
    onError: (errors) => {
      errorMessage.value = 'Erro ao fazer login com dados do sistema.'
      setTimeout(() => errorMessage.value = null, 5000)
    },
    onFinish: () => {
      submittingEnv.value = false
    }
  })
}
</script>

<style scoped>
.bg-gradient {
  background: linear-gradient(135deg, #782DC8 0%, #461978 100%);
}

.feature-card {
  transition: all 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.feature-icon {
  background-color: #782DC8;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 12px;
  flex-shrink: 0;
}

/* Animações suaves para alertas */
.fixed {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    transform: translateY(-100%);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
