<?php

declare(strict_types=1);

namespace Conia\Cms\Node;

use Conia\Cms\Renderer;
use Conia\Core\Exception\HttpBadRequest;
use Conia\Core\Response;
use Conia\Registry\Exception\NotFoundException;
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

    public function render(array $context = []): Response
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

            return (new Response($this->factory->response()))->body(
                $renderer->render($id, $context)
            );
        } catch (NotFoundException) {
            return parent::render();
        } catch (Throwable $e) {
            if ($this->config->debug()) {
                throw $e;
            }

            throw new HttpBadRequest($this->request);
        }
    }
}
