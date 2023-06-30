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
use Gumlet\ImageResize;

class Media
{
    public function __construct(
        protected readonly Factory $factory,
        protected readonly Request $request,
        protected readonly Config $config
    ) {
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
