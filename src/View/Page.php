<?php

declare(strict_types=1);

namespace Conia\View;

use Conia\Controller;
use Conia\Request;
use Conia\Response;


class Page extends Controller
{
    public function catchall(Request $request): Response
    {
        return $request->getResponse();
    }
}
