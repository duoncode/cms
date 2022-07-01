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
        $parts = pathinfo($request->url(stripQuery: true));
        $extension = $parts['extension'] ?? null;

        // Remove the extension from the url
        if (empty($parts['filename'])) {
            $url = $parts['dirname'];
        } else {
            $url = $parts['dirname'] . '/' . $parts['filename'];
        }

        $data = Pages::byUrl($url);

        if (!$data) {
            throw new HttpNotFound();
        }

        $classname = $data['classname'];

        if (is_subclass_of($classname, Type::class)) {
            $page = new $classname($request, $data);

            // Create a JSON response if the URL ends with .json
            if (strtolower($extension ?? '') === 'json') {
                return $request->response()->json($page->json());
            }

            // Render the template
            $renderer = $request->renderer('template', $page::template());
            return $renderer->response([
                'page' => $page,
            ]);
        }

        throw new HttpBadRequest();
    }
}
