<?php

declare(strict_types=1);

namespace Conia\Cms\Node;

use Conia\Core\Exception\HttpBadRequest;
use Conia\Core\Response;
use Throwable;

trait RendersTemplate
{
    protected const string renderer = '';

    public static function renderer(): ?string
    {
        if (!empty(static::renderer)) {
            return static::renderer;
        }

        return static::handle();
    }

    /**
     * Called on GET request.
     */
    public function read(): array|Response
    {
        if ($this->request->header('Accept') === 'application/json') {
            return parent::read();
        }

        return $this->render();
    }

    protected function render(array $context = []): Response
    {
        $context = array_merge([
            'page' => $this,
            'find' => $this->find,
            'locale' => $this->request->get('locale'),
            'locales' => $this->request->get('locales'),
            'request' => $this->request,
            'registry' => $this->registry,
            'debug' => $this->config->debug,
            'env' => $this->config->env,
        ], $context);

        try {
            [$type, $id] = self::renderer();
            $renderer = $this->registry->tag(Renderer::class)->get($type);

            return $renderer->render($id, $context);
        } catch (Throwable $e) {
            if ($this->config->debug()) {
                throw $e;
            }

            throw new HttpBadRequest();
        }
    }
}
