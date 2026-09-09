<?php

declare(strict_types=1);

namespace App\Interface\Http\Controller;

use App\Application\UseCase\Edition\GetEditionsByGame;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObject\Edition;
use App\Interface\Http\AuthGuardMiddleware;
use App\Interface\Http\JsonResponse;
use App\Interface\Http\Request;

final class EditionController
{
    public function __construct(
        private readonly GetEditionsByGame $getEditionsByGame,
        private readonly AuthGuardMiddleware $guard,
    ) {
    }

    /** AC-013, AC-014, AC-015 */
    public function byGame(Request $request): void
    {
        if ($this->guard->requireUser() === null) {
            return;
        }

        $game = (string) ($request->query['game'] ?? '');

        try {
            $editions = $this->getEditionsByGame->execute($game);
            JsonResponse::send(200, ['editions' => array_map(static fn (Edition $e) => $e->toArray(), $editions)]);
        } catch (ValidationException $e) {
            JsonResponse::error(422, $e->getMessage(), $e->fields());
        }
    }
}
