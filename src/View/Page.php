<?php

declare(strict_types=1);

namespace Conia\View;

use Chuck\Error\HttpNotFound;
use Conia\Request;
use Conia\Response;


class Page
{
    public function catchall(Request $request): Response
    {
        throw new HttpNotFound();
        return $request->getResponse();
    }
}
