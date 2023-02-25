<?php

declare(strict_types=1);

namespace Conia\Core\View;

use Conia\Chuck\Exception\HttpBadRequest;
use Conia\Chuck\Exception\HttpNotFound;
use Conia\Chuck\Factory;
use Conia\Chuck\Registry;
use Conia\Chuck\Response;
use Conia\Core\Context;
use Conia\Core\Finder;
use Conia\Core\Node;

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

        if (is_subclass_of($class, Node::class)) {
            $page = new $class($context, $find, $data);

            return $page->response();
        }

        throw new HttpBadRequest();
    }
}
