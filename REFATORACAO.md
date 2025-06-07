# Refatoração do Sistema WebFTP

## Visão Geral

O arquivo `Home.vue` foi refatorado e dividido em componentes menores e mais específicos para melhorar a manutenibilidade e organização do código.

## Componentes Criados

### 1. `Alert.vue`
**Localização:** `resources/js/Components/Alert.vue`

Componente reutilizável para exibir mensagens de sucesso e erro.

**Props:**
- `message` (String): Mensagem a ser exibida
- `type` (String): Tipo do alerta ('success' ou 'error')

**Eventos:**
- `close`: Emitido quando o usuário fecha o alerta

### 2. `TopNavigation.vue`
**Localização:** `resources/js/Components/TopNavigation.vue`

Barra de navegação superior com logo, breadcrumb, busca e controles.

**Props:**
- `currentPath` (String): Caminho atual
- `ftpInfo` (Object): Informações da conexão FTP
- `searchQuery` (String): Termo de busca
- `selectedItems` (Array): Itens selecionados
- `clipboardItems` (Array): Itens no clipboard
- `pasting` (Boolean): Indicador de operação de colagem

**Eventos:**
- `update:searchQuery`: Atualização do termo de busca
- `show-upload-modal`: Exibir modal de upload
- `show-folder-modal`: Exibir modal de nova pasta
- `copy-selected`: Copiar itens selecionados
- `cut-selected`: Cortar itens selecionados
- `delete-selected`: Excluir itens selecionados
- `clear-selection`: Limpar seleção
- `paste-items`: Colar itens
- `logout`: Logout do sistema

### 3. `Sidebar.vue`
**Localização:** `resources/js/Components/Sidebar.vue`

Barra lateral com navegação, área de transferência e informações de espaço.

**Props:**
- `clipboard` (Object): Estado do clipboard
- `pasting` (Boolean): Indicador de operação de colagem

**Eventos:**
- `show-folder-modal`: Exibir modal de nova pasta
- `show-upload-modal`: Exibir modal de upload
- `clear-clipboard`: Limpar clipboard
- `paste-items`: Colar itens

### 4. `FileTable.vue`
**Localização:** `resources/js/Components/FileTable.vue`

Tabela de arquivos e pastas com funcionalidades de seleção e ações.

**Props:**
- `filteredContents` (Array): Lista filtrada de arquivos
- `selectedItems` (Array): Itens selecionados
- `currentPath` (String): Caminho atual
- `clipboard` (Object): Estado do clipboard

**Eventos:**
- `toggle-select-all`: Alternar seleção de todos
- `toggle-select-item`: Alternar seleção de item
- `navigate-up`: Navegar para pasta pai
- `navigate-to`: Navegar para pasta
- `open-image`: Abrir visualizador de imagem
- `download`: Download de arquivo
- `unzip`: Descompactar arquivo
- `edit`: Editar arquivo
- `show-rename`: Exibir modal de renomear
- `copy`: Copiar item
- `cut`: Cortar item
- `delete`: Excluir item

### 5. `UploadModal.vue`
**Localização:** `resources/js/Components/UploadModal.vue`

Modal para upload de arquivos com drag & drop.

**Props:**
- `show` (Boolean): Exibir modal
- `uploading` (Boolean): Indicador de upload em progresso

**Eventos:**
- `close`: Fechar modal
- `upload`: Iniciar upload com lista de arquivos

### 6. `FolderModal.vue`
**Localização:** `resources/js/Components/FolderModal.vue`

Modal para criação de nova pasta.

**Props:**
- `show` (Boolean): Exibir modal

**Eventos:**
- `close`: Fechar modal
- `create`: Criar pasta com nome fornecido

### 7. `RenameModal.vue`
**Localização:** `resources/js/Components/RenameModal.vue`

Modal para renomear arquivos e pastas.

**Props:**
- `show` (Boolean): Exibir modal
- `item` (Object): Item a ser renomeado

**Eventos:**
- `close`: Fechar modal
- `rename`: Renomear com novo nome

### 8. `ImageViewer.vue`
**Localização:** `resources/js/Components/ImageViewer.vue`

Visualizador de imagens com rotação e zoom.

**Props:**
- `show` (Boolean): Exibir visualizador
- `imageUrl` (String): URL da imagem
- `imageTitle` (String): Título da imagem

**Eventos:**
- `close`: Fechar visualizador
- `download`: Download da imagem

### 9. `DragDropOverlay.vue`
**Localização:** `resources/js/Components/DragDropOverlay.vue`

Overlay para drag & drop de arquivos.

**Props:**
- `show` (Boolean): Exibir overlay
- `uploading` (Boolean): Indicador de upload
- `currentPath` (String): Caminho de destino

## Benefícios da Refatoração

### 1. **Manutenibilidade**
- Código dividido em componentes menores e focados
- Cada componente tem uma responsabilidade específica
- Facilita correções e melhorias

### 2. **Reutilização**
- Componentes podem ser reutilizados em outras partes da aplicação
- Alert, por exemplo, pode ser usado em qualquer página

### 3. **Testabilidade**
- Componentes menores são mais fáceis de testar
- Isolamento de funcionalidades facilita testes unitários

### 4. **Legibilidade**
- Código mais limpo e organizado
- Estrutura clara e hierárquica
- Separação de responsabilidades

### 5. **Performance**
- Componentes podem ser carregados sob demanda
- Otimizações específicas por componente

## Estrutura Final

```
resources/js/
├── Components/
│   ├── Alert.vue
│   ├── TopNavigation.vue
│   ├── Sidebar.vue
│   ├── FileTable.vue
│   ├── UploadModal.vue
│   ├── FolderModal.vue
│   ├── RenameModal.vue
│   ├── ImageViewer.vue
│   └── DragDropOverlay.vue
└── Pages/
    └── Home.vue (refatorado)
```

## Próximos Passos

1. **Testes**: Implementar testes unitários para cada componente
2. **Documentação**: Adicionar JSDoc aos componentes
3. **Otimizações**: Implementar lazy loading dos componentes
4. **Acessibilidade**: Melhorar a acessibilidade dos componentes
5. **TypeScript**: Migrar para TypeScript para melhor tipagem

## Notas Técnicas

- Todos os componentes usam Vue 3 Composition API
- Props e eventos são bem definidos
- Estado é gerenciado de forma reativa
- Funcionalidades de drag & drop mantidas
- Sistema de clipboard preservado
- Compatibilidade com Inertia.js mantida 
