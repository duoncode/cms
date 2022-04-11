<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Request as BaseRequest;


/**
 * @method session
 */
class Request extends BaseRequest
{
    public function isXHR(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
}
