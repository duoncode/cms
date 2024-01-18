<?php

declare(strict_types=1);

namespace Conia\Cms;

use Conia\Core\Factory;
use Conia\Route\After;
use Psr\Http\Message\ResponseInterface as Response;
use Traversable;

/** @psalm-api */
class JsonRenderer implements Renderer
{
    public function __construct(protected Factory $factory)
    {
    }

    public function handle(mixed $data): Response
    {
        $flags = JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;

        if ($data instanceof Traversable) {
            return $this->response(json_encode(iterator_to_array($data), $flags));
        }

        return json_encode($data, $flags);
    }

    public function response(string $json): Response
    {
        $response = $this->factory
            ->response()
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($json);

        return $response;
    }

    public function replace(After $handler): bool
    {
        return is_a($handler, Renderer::class);
    }
}
