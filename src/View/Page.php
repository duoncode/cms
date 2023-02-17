<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Exception\HttpBadRequest;
use Conia\Chuck\Exception\HttpNotFound;
use Conia\Chuck\Factory;
use Conia\Chuck\Registry;
use Conia\Chuck\Renderer\Render;
use Conia\Chuck\Response;
use Conia\Core\Context;
use Conia\Core\Finder;
use Conia\Core\Type;
use Throwable;

class Page
{
    public function __construct(
        protected readonly Factory $factory,
        protected readonly Registry $registry,
    ) {
    }

    public function catchall(Context $context, Finder $find): Response
    {
        $data = $find->page->byPath($context->request->uri()->getPath());

        if (!$data) {
            throw new HttpNotFound();
        }

        $class = $data['classname'];

        if (is_subclass_of($class, Type::class)) {
            $page = new $class($context, $find, $data);

            // Create a JSON response if the URL ends with .json
            if (strtolower($extension ?? '') === 'json') {
                return Response::fromFactory($this->factory)->json($page->json());
            }

            // try {
            // Render the template
            $render = new Render('template', $page::template());

            return $render->response($this->registry, [
                'page' => $page,
                'find' => $find,
                'locale' => $context->request->get('locale'),
            ]);
            // } catch (Throwable) {
            //     throw new HttpBadRequest();
            // }
        }

        throw new HttpBadRequest();
    }
}
