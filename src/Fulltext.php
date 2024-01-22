<?php

declare(strict_types=1);

namespace Conia\Cms;

use Conia\Core\Exception\HttpNotFound;
use Conia\Core\Response;

class Fulltext
{
    public function __construct(string $type, array $content)
    {
        $data = $find->node->byPath($request->uri()->getPath());

        if (!$data) {
            throw new HttpNotFound($request);
        }

        $class = $data['slug'];

        if (is_subclass_of($class, Node::class)) {
            $page = new $class($request, $config, $find, $data);

            // Create a JSON response if the URL ends with .json
            if (strtolower($extension ?? '') === 'json') {
                return Response::create($this->factory)->json($page->json());
            }

            // try {
            // Render the template
            $render = new Render('template', $page::template());

            return $render->response($this->registry, [
                'page' => $page,
                'find' => $find,
            ]);
            // } catch (Throwable) {
            //     throw new HttpBadRequest($request);
            // }
        }
    }
}
