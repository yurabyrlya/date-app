<?php

declare(strict_types=1);

use App\Application\Middleware\AuthMiddleware;
use App\Models\Swipe;
use App\Models\User;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as RequestInterface;

include_once __DIR__ . '/../app/Models/User.php';
include_once __DIR__ . '/../app/Models/Swipe.php';
include_once __DIR__ . '/../app/Response/JsonResponse.php';

return function (App $app) {

    $container = $app->getContainer();

    $app->group('', function (RouteCollectorProxy $view)
    {
        $view->get('/', function($request, $response, $args) {
            return $this->get('view')->render($response, 'app.twig');
        });

    })->add($container->get('viewMiddleware'));

    // API routes
    $app->post('/login', function (RequestInterface $request, ResponseInterface $response, $args) {
        $key = $this->get('settings')['appKey'];
        $data = json_decode($request->getBody()->getContents(), true);

        $authToken = User::login(
            $this->get('connection'),
            $data,
            $key
        );

        return JsonResponse::toJson($response, $authToken);
    });

    $app->get('/user/create', function (RequestInterface $request, ResponseInterface $response) {
        $newUser = User::generateUser($this->get('connection'));
        return JsonResponse::toJson($response, $newUser);
    });

    $app->get('/profiles', function (RequestInterface $request, ResponseInterface $response) use ($app)
    {
        $jwt = $request->getQueryParams()['token'];
        $user = User::Auth($app->getContainer(), $jwt);

        $params = $request->getQueryParams();
        $filter = ['from' => $params['from'], 'to' => $params['to'] ];

        $matchProfile = Swipe::getProfile($this->get('connection'), $user, $filter);
        if (!$matchProfile) {
            return JsonResponse::toJson($response, ['message' => 'profiles not found, try to update filters', 'status' => 404 ]);
        }
        return JsonResponse::toJson($response, (array) $matchProfile);

    })->add(new AuthMiddleware($container));

    $app->post('/swipe', function (RequestInterface $request, ResponseInterface $response, $args) use ($app)
    {
        $data = json_decode($request->getBody()->getContents(), true);
        $jwt = $data['token'];

        $user= User::Auth($app->getContainer(), $jwt);

        $db = $this->get('connection');
        $profile = $db->table('users')->find($data['profile_id']);
        $preference = strtoupper(trim($data['preference'])) === 'YES';

        $newSwipe = [
            'user_id' => $user->id,
            'profile_id' => $profile->id,
            'preference' => $preference,
        ];
        Swipe::swipe($db, $newSwipe);

        $isMatch = false;
        if ($preference) {
            $isMatch = Swipe::isMatch($db, $user, $profile);
        }

        $data = [
            'user' => $user,
            'swipe_profile' => $profile,
            'match' => $isMatch ? 'Yes' : 'No',
            'preference' => $preference ? 'Yes' : 'No'
        ];
        return JsonResponse::toJson($response, $data) ;
    })->add(new AuthMiddleware($container));

};
