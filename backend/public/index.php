<?php

declare(strict_types=1);

use App\Application\UseCase\Auth\AuthenticateUser;
use App\Application\UseCase\Auth\GetCurrentUser;
use App\Application\UseCase\Auth\LogoutUser;
use App\Application\UseCase\Card\CreateCard;
use App\Application\UseCase\Card\DeleteCard;
use App\Application\UseCase\Card\GetCard;
use App\Application\UseCase\Card\ListCards;
use App\Application\UseCase\Card\UpdateCard;
use App\Application\UseCase\Edition\GetEditionsByGame;
use App\Infrastructure\Auth\PhpSessionAuthService;
use App\Infrastructure\Persistence\PdoCardRepository;
use App\Infrastructure\Persistence\PdoConnectionFactory;
use App\Infrastructure\Persistence\PdoEditionRepository;
use App\Infrastructure\Persistence\PdoUserRepository;
use App\Interface\Http\AuthGuardMiddleware;
use App\Interface\Http\Controller\AuthController;
use App\Interface\Http\Controller\CardController;
use App\Interface\Http\Controller\EditionController;
use App\Interface\Http\CorsMiddleware;
use App\Interface\Http\JsonResponse;
use App\Interface\Http\Request;
use App\Interface\Http\Router;

$config = require __DIR__ . '/../config/bootstrap.php';

CorsMiddleware::apply($config['frontend_origin']);

try {
    $pdo = PdoConnectionFactory::create($config['database']);

    $authSession = new PhpSessionAuthService();
    $userRepository = new PdoUserRepository($pdo);
    $cardRepository = new PdoCardRepository($pdo);
    $editionRepository = new PdoEditionRepository($pdo);

    $getCurrentUser = new GetCurrentUser($userRepository, $authSession);
    $guard = new AuthGuardMiddleware($getCurrentUser);

    $authController = new AuthController(
        new AuthenticateUser($userRepository, $authSession),
        new LogoutUser($authSession),
        $guard,
    );
    $cardController = new CardController(
        new ListCards($cardRepository),
        new GetCard($cardRepository),
        new CreateCard($cardRepository),
        new UpdateCard($cardRepository),
        new DeleteCard($cardRepository),
        $guard,
    );
    $editionController = new EditionController(new GetEditionsByGame($editionRepository), $guard);

    $router = new Router();
    $router->add('POST', '/api/auth/login', fn (Request $r) => $authController->login($r));
    $router->add('POST', '/api/auth/logout', fn () => $authController->logout());
    $router->add('GET', '/api/auth/me', fn () => $authController->me());

    $router->add('GET', '/api/cards', fn () => $cardController->index());
    $router->add('GET', '/api/cards/{id}', fn (Request $r, array $p) => $cardController->show($p));
    $router->add('POST', '/api/cards', fn (Request $r) => $cardController->store($r));
    $router->add('PUT', '/api/cards/{id}', fn (Request $r, array $p) => $cardController->update($r, $p));
    $router->add('DELETE', '/api/cards/{id}', fn (Request $r, array $p) => $cardController->destroy($p));

    $router->add('GET', '/api/editions', fn (Request $r) => $editionController->byGame($r));

    $router->dispatch(Request::fromGlobals());
} catch (\Throwable $e) {
    error_log($e->getMessage());
    JsonResponse::error(500, 'Erro interno do servidor.');
}
