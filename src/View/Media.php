<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Factory;
use Conia\Chuck\Request;
use Conia\Chuck\Response;

class Media
{
    public function __construct(
        protected readonly Factory $factory,
        protected readonly Request $request
    ) {
    }

    public function image(string $slug): Response
    {
        $response = Response::fromFactory($this->factory);

        $response->body('<h1>Hans Karl</h1>');
        error_log(print_r($slug, true));

        return $response;
    }
}
