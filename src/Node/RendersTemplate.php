<?php

declare(strict_types=1);

namespace Conia\Cms\Node;

use Conia\Cms\Renderer;
use Conia\Core\Exception\HttpBadRequest;
use Conia\Core\Response;
use Throwable;

trait RendersTemplate
{
    protected const string renderer = '';

    public function renderer(): array
    {
        if (!empty(static::renderer)) {
            return ['template', static::renderer];
        }

        return ['template', static::handle()];
    }

    /**
     * Called on GET request.
     */
    public function read(): array|Response
    {
        if ($this->request->header('Accept') === 'application/json') {
            return parent::read();
        }

        return (new Response($this->factory->response()))->body($this->render());
    }

    protected function render(array $context = []): string
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
            [$type, $id] = $this->renderer();
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
