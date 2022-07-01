<?php

declare(strict_types=1);

namespace Conia\View;

use Chuck\Error\{HttpNotFound, HttpBadRequest};
use Chuck\Response\Response;
use Conia\Request;
use Conia\Pages;
use Conia\Type;


class Page
{
    public function catchall(Request $request): Response
    {
        $data = Pages::byUrl($request->url(stripQuery: true));

        if (!$data) {
            throw new HttpNotFound();
        }

        $classname = $data['classname'];

        if (is_subclass_of($classname, Type::class)) {
            $page = new $classname($request, $data);

            return $request->response()->json($page->json());
        }

        throw new HttpBadRequest();
    }
}
