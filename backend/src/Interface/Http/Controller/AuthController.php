<?php

declare(strict_types=1);

namespace App\Interface\Http\Controller;

use App\Application\UseCase\Auth\AuthenticateUser;
use App\Application\UseCase\Auth\LogoutUser;
use App\Domain\Exception\UnauthorizedException;
use App\Interface\Http\AuthGuardMiddleware;
use App\Interface\Http\JsonResponse;
use App\Interface\Http\Request;

final class AuthController
{
    public function __construct(
        private readonly AuthenticateUser $authenticateUser,
        private readonly LogoutUser $logoutUser,
        private readonly AuthGuardMiddleware $guard,
    ) {
    }

    /** AC-001, AC-002 */
    public function login(Request $request): void
    {
        $email = (string) ($request->body['email'] ?? '');
        $password = (string) ($request->body['password'] ?? '');

        try {
            $user = $this->authenticateUser->execute($email, $password);
            JsonResponse::send(200, ['user' => $user->toArray()]);
        } catch (UnauthorizedException $e) {
            JsonResponse::error(401, $e->getMessage());
        }
    }

    /** AC-005 */
    public function logout(): void
    {
        if ($this->guard->requireUser() === null) {
            return;
        }

        $this->logoutUser->execute();
        JsonResponse::send(200, ['message' => 'Sessão encerrada.']);
    }

    /** AC-003, AC-004 */
    public function me(): void
    {
        $user = $this->guard->requireUser();

        if ($user === null) {
            return;
        }

        JsonResponse::send(200, ['user' => $user->toArray()]);
    }
}
