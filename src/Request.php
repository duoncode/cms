<?php

declare(strict_types=1);

namespace Conia;

use Chuck\Request as BaseRequest;
use Chuck\ResponseFactory;
use Chuck\Routing\RouterInterface;


/**
 * @method session
 */
class Request extends BaseRequest
{
    protected ?Locale $locale = null;

    public function __construct(
        Config $config,
        RouterInterface $router,
        protected Session $session,
        ResponseFactory $response = new ResponseFactory(),
    ) {
        parent::__construct($config, $router, $response);

        $session->start();
    }

    public function isXHR(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function locale(): Locale
    {
        if (!$this->locale) {
            $this->locale = $this->config->locales->negotiate($this);
        }

        return $this->locale;
    }

    public function config(): Config
    {
        return $this->config;
    }
}
