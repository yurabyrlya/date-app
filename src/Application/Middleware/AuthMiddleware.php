<?php

namespace App\Application\Middleware;

use App\Models\User;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthMiddleware
{

    private $container;

    public function __construct($container) {
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @param RequestHandler $handler
     * @return Response
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $jwt = $request->getQueryParams()['token'];

        // if token in body // @todo to move token to Header
        if (!$jwt) {
            $data = json_decode($request->getBody()->getContents(), true);
            $jwt = $data['token'];
        }

        if (!$jwt){
            return \JsonResponse::toJson(new Response(), [
                    'message' => 'Unauthorized - token not matched',
                    'code' => 401
                ]
            );
        }
        if (!User::authenticated($this->container, $jwt)){
            return \JsonResponse::toJson(new Response(), [
                    'message' => 'Unauthorized',
                    'code' => 401
                ]
            );
        }
        return $handler->handle($request);
    }
}
