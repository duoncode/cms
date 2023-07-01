<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Factory;
use Conia\Chuck\Request;
use Conia\Chuck\Response;
use Conia\Core\Assets\Assets;
use Conia\Core\Assets\ResizeMode;
use Conia\Core\Assets\Size;
use Conia\Core\Config;
use Conia\Core\Exception\RuntimeException;
use Conia\Core\Middleware\Permission;
use Gumlet\ImageResize;

class Media
{
    public function __construct(
        protected readonly Factory $factory,
        protected readonly Request $request,
        protected readonly Config $config
    ) {
    }

    /**
     * TODO: sanitize filename.
     */
    #[Permission('panel')]
    public function upload(string $type, string $uid): Response
    {
        $response = Response::fromFactory($this->factory);

        $public = $this->config->get('path.public');
        $assets = $this->config->get('path.assets');
        $maxSize = $this->config->get('upload.maxsize');
        $mimeTypes = $this->config->get('upload.mimetypes');

        $file = $_FILES['file'];
        $tmpFile = $file['tmp_name'];
        $fileSize = filesize($tmpFile);
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $tmpFile);
        finfo_close($fileInfo);
        $fileName = $file['full_path'];
        $pathInfo = pathinfo($fileName);
        $ext = $pathInfo['extension'] ?? null;
        $allowedExtensions = $mimeTypes[$mimeType] ?? null;

        if ($file['error'] ?? null !== UPLOAD_ERR_OK) {
            return $response->json(['ok' => false, 'error' => 'Upload failed.'], 400);
        }

        if ($fileSize > $maxSize) {
            return $response->json(['ok' => false, 'error' => 'File too large.'], 400);
        }

        if (!$allowedExtensions) {
            return $response->json(['ok' => false, 'error' => "File type not allowed: {$mimeType}."], 400);
        }

        if (!$ext || !in_array(strtolower($ext), $allowedExtensions)) {
            return $response->json([
                'ok' => false,
                'error' => 'Wrong file extension. Allowed are: ' . join(', ', $allowedExtensions) . '.',
            ], 400);
        }

        move_uploaded_file($tmpFile, "{$public}/{$assets}/{$type}/{$uid}/{$fileName}");

        return $response->json(['ok' => true, 'file' => $fileName]);
    }

    public function image(string $slug): Response
    {
        $image = $this->getAssets()->image($slug);
        $qs = $this->request->params();

        if ($qs['resize'] ?? null) {
            [$size, $mode] = match ($qs['resize']) {
                ResizeMode::Width->value => [new Size((int)$qs['w']), ResizeMode::Width],
                ResizeMode::Height->value => [new Size((int)$qs['h']), ResizeMode::Height],
                ResizeMode::LongSide->value => [new Size((int)$qs['size']), ResizeMode::LongSide],
                ResizeMode::ShortSide->value => [new Size((int)$qs['size']), ResizeMode::ShortSide],
                ResizeMode::Fit->value => [new Size((int)$qs['w'], (int)$qs['h']), ResizeMode::Fit],
                ResizeMode::Resize->value => [new Size((int)$qs['w'], (int)$qs['h']), ResizeMode::Resize],
                ResizeMode::FreeCrop->value => [new Size((int)$qs['w'], (int)$qs['h'], [
                    'x' => $qs['x'] ? (int)$qs['x'] : false,
                    'y' => $qs['y'] ? (int)$qs['y'] : false,
                ]), ResizeMode::FreeCrop],
                ResizeMode::Crop->value => [new Size((int)$qs['w'], (int)$qs['h'], match ($qs['pos']) {
                    'top' => ImageResize::CROPTOP,
                    'centre' => ImageResize::CROPCENTRE,
                    'center' => ImageResize::CROPCENTER,
                    'bottom' => ImageResize::CROPBOTTOM,
                    'left' => ImageResize::CROPLEFT,
                    'right' => ImageResize::CROPRIGHT,
                    'topcenter' => ImageResize::CROPTOPCENTER,
                    default => throw new RuntimeException('Crop position not supported: ' . $qs['pos']),
                }), ResizeMode::Crop],
                default => throw new RuntimeException('Resize mode not supported: ' . $qs['resize']),
            };

            $quality = ($qs['quality'] ?? null) ? (int)$qs['quality'] : null;
            $image->resize($size, $mode, $qs['enlarge'] ?? false, $quality);
        }

        $fileServer = $this->config->get('media.fileserver', null);

        if ($fileServer) {
            return $this->sendFile($fileServer, $image->path());
        }

        return Response::fromFactory($this->factory)->file($image->path());
    }

    protected function sendFile(string $fileServer, string $file): Response
    {
        $response = Response::fromFactory($this->factory);
        $response->header('Content-Type', mime_content_type($file));

        switch ($fileServer) {
            case 'apache':
                // apt install libapache2-mod-xsendfile
                // a2enmod xsendfile
                // Apache config:
                //    XSendFile On
                //    XSendFilePath "/path/to/files"
                $response->header('X-Sendfile', $file);
                break;
            case 'nginx':
                // Nginx config
                //   location /path/to/files/ {
                //       internal;
                //           alias   /some/path/; # note the trailing slash
                //       }
                //   }

                $response->header('X-Accel-Redirect', $file);
                break;
            default:
                throw new RuntimeException(
                    'File server not supported: `' .
                    $fileServer .
                    '`. Supported values `nginx`, `apache`.'
                );
        }

        return $response;
    }

    protected function getAssets(): Assets
    {
        static $assets = null;

        if (!$assets) {
            $assets = new Assets($this->request, $this->config);
        }

        return $assets;
    }
}
