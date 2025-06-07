<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Ftp\FtpAdapter;
use League\Flysystem\Ftp\FtpConnectionOptions;
use Inertia\Inertia;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    protected $filesystem;

    public function __construct()
    {
        // Verificar se há dados FTP na sessão
        if (!session('ftp_host') || !session('ftp_username') || !session('ftp_password')) {
            return;
        }

        $options = FtpConnectionOptions::fromArray([
            'host' => session('ftp_host') ?? config('ftp.host'),
            'username' => session('ftp_username') ?? config('ftp.username'),
            'password' => session('ftp_password') ?? config('ftp.password'),
            'port' => session('ftp_port') ?? config('ftp.port', 21),
            'root' => session('ftp_root') ?? config('ftp.root', '/'),
            'passive' => true,
            'ssl' => false,
            'timeout' => 30,
        ]);

        $adapter = new FtpAdapter($options);
        $this->filesystem = new Filesystem($adapter);
    }

    private function checkFtpLogin()
    {
        if (!session('ftp_host') || !session('ftp_username') || !session('ftp_password')) {
            return false;
        }
        return true;
    }

    public function index(Request $request)
    {
        if (!$this->checkFtpLogin()) {
            return redirect()->route('login')->with('error', 'Faça login para acessar o FTP.');
        }

        try {
            $path = $request->get('path', '/');
            $search = $request->get('search', '');

            $contents = collect($this->filesystem->listContents($path))
                ->map(function ($item) {
                    return [
                        'type' => $item['type'],
                        'path' => $item['path'],
                        'name' => basename($item['path']),
                        'size' => $item['type'] === 'file' ? $this->formatSize($item['fileSize']) : '-',
                        'last_modified' => $item['lastModified'],
                        'extension' => pathinfo($item['path'], PATHINFO_EXTENSION),
                        'is_image' => in_array(strtolower(pathinfo($item['path'], PATHINFO_EXTENSION)),
                            ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg']),
                        'is_editable' => in_array(strtolower(pathinfo($item['path'], PATHINFO_EXTENSION)),
                            ['php', 'html', 'htm', 'txt', 'js', 'css']),
                        'is_zip' => strtolower(pathinfo($item['path'], PATHINFO_EXTENSION)) === 'zip'
                    ];
                });

            // Filtro de pesquisa
            if ($search) {
                $contents = $contents->filter(function ($item) use ($search) {
                    return stripos($item['name'], $search) !== false;
                });
            }

            $contents = $contents->sortBy('type')->values();

            return Inertia::render('Home', [
                'files' => $contents,
                'currentPath' => $path,
                'search' => $search,
                'success' => session('success'),
                'error' => session('error'),
                'ftpInfo' => [
                    'host' => session('ftp_host'),
                    'username' => session('ftp_username'),
                    'connected' => true
                ]
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Home', [
                'files' => [],
                'currentPath' => $path ?? '/',
                'search' => $search ?? '',
                'error' => 'Erro ao listar diretório: ' . $e->getMessage(),
                'ftpInfo' => [
                    'host' => session('ftp_host'),
                    'username' => session('ftp_username'),
                    'connected' => false
                ]
            ]);
        }
    }

    public function upload(Request $request)
    {
        if (!$this->checkFtpLogin()) {
            return redirect()->route('login')->with('error', 'Faça login para acessar o FTP.');
        }

        try {
            $request->validate([
                'files.*' => 'required|file|max:102400', // Max 100MB por arquivo
                'path' => 'required|string',
            ]);

            $path = $request->input('path', '/');
            $uploadedCount = 0;

            foreach ($request->file('files') as $file) {
                $fileName = $file->getClientOriginalName();
                $remotePath = rtrim($path, '/') . '/' . $fileName;

                $stream = fopen($file->getRealPath(), 'r+');
                $this->filesystem->writeStream($remotePath, $stream);
                fclose($stream);
                $uploadedCount++;
            }

            return redirect()->route('home', ['path' => $path])
                ->with('success', "$uploadedCount arquivo(s) enviado(s) com sucesso!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao fazer upload: ' . $e->getMessage());
        }
    }

    public function download($path)
    {
        try {
            if (!$this->filesystem->fileExists($path)) {
                return redirect()->back()->with('error', 'Arquivo não encontrado');
            }

            $stream = $this->filesystem->readStream($path);
            return response()->stream(
                function () use ($stream) {
                    fpassthru($stream);
                    fclose($stream);
                },
                200,
                [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . basename($path) . '"',
                ]
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao baixar arquivo: ' . $e->getMessage());
        }
    }

    public function delete($path)
    {
        try {
            if ($this->filesystem->fileExists($path)) {
                $this->filesystem->delete($path);
            } else {
                $this->filesystem->deleteDirectory($path);
            }

            return redirect()->back()->with('success', 'Item excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao excluir: ' . $e->getMessage());
        }
    }

    public function createFolder(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'path' => 'required|string',
            ]);

            $path = trim($request->path, '/') . '/' . $request->name;
            $this->filesystem->createDirectory($path);

            return redirect()->back()->with('success', 'Pasta criada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao criar pasta: ' . $e->getMessage());
        }
    }

    public function rename(Request $request)
    {
        try {
            $request->validate([
                'old_path' => 'required|string',
                'new_name' => 'required|string',
            ]);

            $oldPath = $request->old_path;
            $newPath = dirname($oldPath) . '/' . $request->new_name;

            if ($this->filesystem->fileExists($oldPath)) {
                $this->filesystem->move($oldPath, $newPath);
            } else {
                // Para diretórios, precisamos mover todos os arquivos
                $contents = $this->filesystem->listContents($oldPath, true);
                foreach ($contents as $item) {
                    $newItemPath = str_replace($oldPath, $newPath, $item['path']);
                    if ($item['type'] === 'file') {
                        $this->filesystem->move($item['path'], $newItemPath);
                    }
                }
                $this->filesystem->deleteDirectory($oldPath);
            }

            return redirect()->back()->with('success', 'Item renomeado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao renomear: ' . $e->getMessage());
        }
    }

    public function unzip(Request $request, $path)
    {
        try {
            // 1. Baixar o arquivo ZIP do FTP para um local temporário
            $tmpZip = tempnam(sys_get_temp_dir(), 'ftpzip_');
            $stream = $this->filesystem->readStream($path);
            $local = fopen($tmpZip, 'w+');
            stream_copy_to_stream($stream, $local);
            fclose($stream);
            fclose($local);

            // 2. Descompactar
            $zip = new \ZipArchive;
            if ($zip->open($tmpZip) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entry = $zip->getNameIndex($i);
                    $fileContent = $zip->getFromIndex($i);
                    $destPath = dirname($path) . '/' . $entry;
                    if (substr($entry, -1) === '/') {
                        $this->filesystem->createDirectory($destPath);
                    } else {
                        $this->filesystem->write($destPath, $fileContent);
                    }
                }
                $zip->close();
            } else {
                unlink($tmpZip);
                return redirect()->back()->with('error', 'Não foi possível abrir o arquivo ZIP.');
            }

            // 3. Remover arquivo temporário
            unlink($tmpZip);

            return redirect()->back()->with('success', 'Arquivo descompactado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao descompactar: ' . $e->getMessage());
        }
    }

    public function edit($path)
    {
        try {
            $content = $this->filesystem->read($path);
            return Inertia::render('EditFile', [
                'path' => $path,
                'content' => $content,
                'filename' => basename($path)
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao abrir arquivo: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $path)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        try {
            $this->filesystem->write($path, $request->content);
            return redirect()->route('home', ['path' => dirname($path)])
                ->with('success', 'Arquivo editado com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao salvar arquivo: ' . $e->getMessage());
        }
    }

    public function viewRaw($path)
    {
        if (!$this->checkFtpLogin()) {
            return redirect()->route('login')->with('error', 'Faça login para acessar o FTP.');
        }

        try {
            if (!$this->filesystem->fileExists($path)) {
                return redirect()->back()->with('error', 'Arquivo não encontrado');
            }

            $mime = $this->getMimeType($path);
            $stream = $this->filesystem->readStream($path);

            return response()->stream(
                function () use ($stream) {
                    fpassthru($stream);
                    fclose($stream);
                },
                200,
                [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=86400',
                ]
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao visualizar arquivo: ' . $e->getMessage());
        }
    }

    public function copyItems(Request $request)
    {
        if (!$this->checkFtpLogin()) {
            return redirect()->route('login')->with('error', 'Faça login para acessar o FTP.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.path' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.type' => 'required|string',
            'destination' => 'required|string'
        ]);

        try {
            $destination = $request->input('destination');
            $items = $request->input('items');
            $copiedCount = 0;

            foreach ($items as $item) {
                $sourcePath = $item['path'];
                $destinationPath = rtrim($destination, '/') . '/' . $item['name'];

                // Evitar copiar para o mesmo local
                if ($sourcePath === $destinationPath) {
                    continue;
                }

                if ($item['type'] === 'file') {
                    // Copiar arquivo
                    $content = $this->filesystem->read($sourcePath);
                    $this->filesystem->write($destinationPath, $content);
                } else {
                    // Copiar diretório recursivamente
                    $this->copyDirectory($this->filesystem, $sourcePath, $destinationPath);
                }

                $copiedCount++;
            }

            return redirect()->back()->with('success', "$copiedCount item(s) copiado(s) com sucesso!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao copiar itens: ' . $e->getMessage());
        }
    }

    public function moveItems(Request $request)
    {
        if (!$this->checkFtpLogin()) {
            return redirect()->route('login')->with('error', 'Faça login para acessar o FTP.');
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.path' => 'required|string',
            'items.*.name' => 'required|string',
            'items.*.type' => 'required|string',
            'destination' => 'required|string'
        ]);

        try {
            $destination = $request->input('destination');
            $items = $request->input('items');
            $movedCount = 0;

            foreach ($items as $item) {
                $sourcePath = $item['path'];
                $destinationPath = rtrim($destination, '/') . '/' . $item['name'];

                // Evitar mover para o mesmo local
                if (dirname($sourcePath) === rtrim($destination, '/')) {
                    continue;
                }

                // Mover arquivo ou diretório
                $this->filesystem->move($sourcePath, $destinationPath);
                $movedCount++;
            }

            return redirect()->back()->with('success', "$movedCount item(s) movido(s) com sucesso!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao mover itens: ' . $e->getMessage());
        }
    }

    private function copyDirectory($filesystem, $source, $destination)
    {
        // Criar diretório de destino
        if (!$filesystem->directoryExists($destination)) {
            $filesystem->createDirectory($destination);
        }

        // Listar conteúdo do diretório fonte
        $contents = $filesystem->listContents($source, true);

        foreach ($contents as $item) {
            $relativePath = str_replace($source, '', $item['path']);
            $newPath = $destination . $relativePath;

            if ($item['type'] === 'file') {
                $content = $filesystem->read($item['path']);
                $filesystem->write($newPath, $content);
            } else {
                if (!$filesystem->directoryExists($newPath)) {
                    $filesystem->createDirectory($newPath);
                }
            }
        }
    }

    // Função auxiliar para determinar o tipo MIME
    private function getMimeType($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
