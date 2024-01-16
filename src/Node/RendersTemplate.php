<?php

declare(strict_types=1);

namespace Conia\Cms\Node;

use Conia\Cms\Exception\HttpBadRequest;
use Conia\Cms\Renderer\Render;
use Conia\Http\Response;
use Throwable;

trait RendersTemplate
{
    protected static string $template = '';

    public static function template(): ?string
    {
        if (!empty(static::$template)) {
            return static::$template;
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
            'locales' => $this->config->locales,
        ], $context);

        try {
            $render = new Render('template', self::template());

            return $render->response($this->registry, $context);
        } catch (Throwable $e) {
            if ($this->config->debug()) {
                throw $e;
            }

            throw new HttpBadRequest();
        }
    }
}
