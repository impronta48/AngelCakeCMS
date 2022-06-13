<?php

declare(strict_types=1);

namespace App\Middleware\UnauthorizedHandler;

use Authorization\Exception\Exception;
use Authorization\Middleware\UnauthorizedHandler\RedirectHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CustomRedirectHandler extends RedirectHandler
{
    public function handle(Exception $exception, ServerRequestInterface $request, array $options = []): ResponseInterface
    {
        $response = parent::handle($exception, $request, $options);
        $request->getFlash()->error('You are not authorized to access that location');
        return $response;
    }
}
