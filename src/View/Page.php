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
        $page = Pages::byUrl($request->url(stripQuery: true));

        if (!$page) {
            throw new HttpNotFound();
        }

        return $request->response()->json($page);
    }
}
