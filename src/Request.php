<?php

declare(strict_types=1);

use Chuck\Request as BaseRequest;


class Request extends BaseRequest
{
    public function isXHR(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }
}
