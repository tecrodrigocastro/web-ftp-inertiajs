# 🚀 KingWebFTP

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white" alt="Vue.js">
  <img src="https://img.shields.io/badge/Inertia.js-7A2D96?style=for-the-badge&logo=inertia&logoColor=white" alt="Inertia.js">
  <img src="https://img.shields.io/badge/TailwindCSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
</p>

Sistema WebFTP moderno e intuitivo desenvolvido com Laravel e Vue.js, oferecendo uma interface web elegante para gerenciamento de arquivos via FTP.

## 📋 Objetivo

O **KingWebFTP** foi criado para facilitar o gerenciamento de arquivos em servidores FTP através de uma interface web moderna e responsiva. O sistema oferece:

- ✅ **Interface Intuitiva**: Design moderno e responsivo
- ✅ **Drag & Drop**: Upload de arquivos por arrastar e soltar
- ✅ **Visualizador de Imagens**: Com zoom e rotação
- ✅ **Operações em Massa**: Seleção múltipla para operações
- ✅ **Clipboard Virtual**: Sistema de copiar/cortar/colar
- ✅ **Navegação Rápida**: Breadcrumb e navegação por pastas
- ✅ **Busca em Tempo Real**: Filtros dinâmicos
- ✅ **Gerenciamento Completo**: Upload, download, renomear, excluir, criar pastas
- ✅ **Editor de Código**: CodeMirror com syntax highlighting e temas

## 🧩 Componentes Vue.js

O sistema foi refatorado em **9 componentes modulares** para melhor manutenibilidade:

| Componente | Descrição | Localização |
|------------|-----------|-------------|
| `Alert.vue` | Sistema de alertas (sucesso/erro) | `resources/js/Components/` |
| `TopNavigation.vue` | Barra de navegação superior | `resources/js/Components/` |
| `Sidebar.vue` | Barra lateral com navegação | `resources/js/Components/` |
| `FileTable.vue` | Tabela de arquivos e pastas | `resources/js/Components/` |
| `UploadModal.vue` | Modal de upload com drag & drop | `resources/js/Components/` |
| `FolderModal.vue` | Modal para criar pastas | `resources/js/Components/` |
| `RenameModal.vue` | Modal para renomear itens | `resources/js/Components/` |
| `ImageViewer.vue` | Visualizador de imagens | `resources/js/Components/` |
| `DragDropOverlay.vue` | Overlay para drag & drop | `resources/js/Components/` |

## 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 12 + Inertia.js
- **Frontend**: Vue.js 3 (Composition API)
- **Styling**: TailwindCSS + FontAwesome
- **Build**: Vite
- **FTP**: Extensão PHP FTP

## 📦 Instalação

### Pré-requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- Extensão PHP FTP habilitada

### Passo a Passo

1. **Clone o repositório**
```bash
git clone <url-do-repositorio>
cd webftp_vue
```

2. **Instale as dependências PHP**
```bash
composer install
```

3. **Instale as dependências Node.js**
```bash
npm install
```

4. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure o banco de dados** (opcional, para sessões)
```bash
php artisan migrate
```

6. **Compile os assets**
```bash
# Desenvolvimento
npm run dev

# Produção
npm run build
```

7. **Inicie o servidor**
```bash
php artisan serve
```

## ⚙️ Configuração do .env

Configure as seguintes variáveis no arquivo `.env`:

### Configurações Essenciais

```env
# Configuração da aplicação
APP_NAME="KingWebFTP"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Configuração de sessão (recomendado)
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Cache (opcional)
CACHE_DRIVER=file
```

### Configurações de Banco (Opcional)

Se desejar usar banco de dados para sessões:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=webftp
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### Configurações de Segurança

```env
# Configuração de arquivos
MAX_UPLOAD_SIZE=100M
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif,pdf,txt,zip,rar

# Timeout FTP (segundos)
FTP_TIMEOUT=30
```

## 🚀 Uso

1. Acesse `http://localhost:8000`
2. Insira suas credenciais FTP:
   - **Servidor**: IP ou hostname do servidor FTP
   - **Usuário**: Seu usuário FTP
   - **Senha**: Sua senha FTP
   - **Porta**: Porta FTP (padrão: 21)
3. Navegue e gerencie seus arquivos!

## ✨ Funcionalidades

### 📁 Gerenciamento de Arquivos
- Upload via drag & drop ou seleção
- Download de arquivos
- Criação de pastas
- Renomear arquivos/pastas
- Exclusão com confirmação

### ✏️ **Editor de Código Avançado (CodeMirror)**
- **Syntax Highlighting**: Suporte para PHP, HTML, CSS, JavaScript, JSON, Markdown
- **Numeração de Linhas**: Interface profissional
- **Temas**: Modo escuro (Dracula) e claro
- **Tela Cheia**: Editor em fullscreen para melhor experiência
- **Busca Avançada**: Ctrl+F para buscar e substituir texto
- **Atalhos de Teclado**: Ctrl+S (salvar), Ctrl+F (buscar)
- **Informações do Cursor**: Linha e coluna atual
- **Detecção de Mudanças**: Aviso ao sair sem salvar
- **Copiar Tudo**: Botão para copiar todo o conteúdo
- **Múltiplos Formatos**: TXT, PHP, HTML, CSS, JS, JSON, MD, XML

### 🖼️ Visualizador de Imagens
- Zoom in/out
- Rotação (90°)
- Download direto
- Suporte: JPG, PNG, GIF, WebP

### 📋 Sistema de Clipboard
- Copiar/Cortar arquivos
- Persistência entre sessões
- Operações em massa
- Indicadores visuais

### 🔍 Busca e Navegação
- Busca em tempo real
- Breadcrumb navigation
- Navegação por clique
- Atalhos de teclado

## 🏗️ Estrutura do Projeto

```
resources/js/
├── Components/           # Componentes Vue reutilizáveis
│   ├── Alert.vue
│   ├── TopNavigation.vue
│   ├── Sidebar.vue
│   ├── FileTable.vue
│   ├── UploadModal.vue
│   ├── FolderModal.vue
│   ├── RenameModal.vue
│   ├── ImageViewer.vue
│   └── DragDropOverlay.vue
└── Pages/               # Páginas Inertia.js
    ├── Home.vue         # Interface principal
    ├── EditFile.vue     # Editor de código com CodeMirror
    └── Login.vue        # Tela de login FTP
```

## 🤝 Contribuindo

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está licenciado sob a [MIT License](https://opensource.org/licenses/MIT).

## 🐛 Reportar Bugs

Encontrou um bug? [Abra uma issue](../../issues) descrevendo:
- Passos para reproduzir
- Comportamento esperado vs atual
- Screenshots (se aplicável)
- Informações do ambiente

## 📚 Documentação Adicional

- [Documentação da Refatoração](REFATORACAO.md)
- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)

---

<p align="center">
  Desenvolvido com ❤️ usando Laravel e Vue.js
</p>
