<?php

declare(strict_types=1);

namespace Conia\View;

use Chuck\Error\HttpNotFound;
use Chuck\Response\Response;
use Conia\Request;
use Conia\Pages;


class Page
{
    public function catchall(Request $request): Response
    {
        $data = Pages::byUrl($request->url(stripQuery: true));

        if (!$data) {
            throw new HttpNotFound();
        }

        $page = new ($data['classname'])($data, $request->locale()->id);

        return $request->response()->json($page->json());
    }
}
