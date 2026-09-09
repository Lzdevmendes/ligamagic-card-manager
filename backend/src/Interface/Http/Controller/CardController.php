<?php

declare(strict_types=1);

namespace App\Interface\Http\Controller;

use App\Application\DTO\CardInput;
use App\Application\DTO\CardOutput;
use App\Application\UseCase\Card\CreateCard;
use App\Application\UseCase\Card\DeleteCard;
use App\Application\UseCase\Card\GetCard;
use App\Application\UseCase\Card\ListCards;
use App\Application\UseCase\Card\UpdateCard;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\ValidationException;
use App\Interface\Http\AuthGuardMiddleware;
use App\Interface\Http\JsonResponse;
use App\Interface\Http\Request;

final class CardController
{
    public function __construct(
        private readonly ListCards $listCards,
        private readonly GetCard $getCard,
        private readonly CreateCard $createCard,
        private readonly UpdateCard $updateCard,
        private readonly DeleteCard $deleteCard,
        private readonly AuthGuardMiddleware $guard,
    ) {
    }

    /** AC-006 */
    public function index(): void
    {
        if ($this->guard->requireUser() === null) {
            return;
        }

        JsonResponse::send(200, ['cards' => CardOutput::fromEntities($this->listCards->execute())]);
    }

    public function show(array $params): void
    {
        if ($this->guard->requireUser() === null) {
            return;
        }

        try {
            $card = $this->getCard->execute((int) $params['id']);
            JsonResponse::send(200, ['card' => CardOutput::fromEntity($card)]);
        } catch (NotFoundException $e) {
            JsonResponse::error(404, $e->getMessage());
        }
    }

    /** AC-007, AC-008 */
    public function store(Request $request): void
    {
        if ($this->guard->requireUser() === null) {
            return;
        }

        try {
            $card = $this->createCard->execute(CardInput::fromArray($request->body));
            JsonResponse::send(201, ['card' => CardOutput::fromEntity($card)]);
        } catch (ValidationException $e) {
            JsonResponse::error(422, $e->getMessage(), $e->fields());
        }
    }

    /** AC-009, AC-010 */
    public function update(Request $request, array $params): void
    {
        if ($this->guard->requireUser() === null) {
            return;
        }

        try {
            $card = $this->updateCard->execute((int) $params['id'], CardInput::fromArray($request->body));
            JsonResponse::send(200, ['card' => CardOutput::fromEntity($card)]);
        } catch (NotFoundException $e) {
            JsonResponse::error(404, $e->getMessage());
        } catch (ValidationException $e) {
            JsonResponse::error(422, $e->getMessage(), $e->fields());
        }
    }

    /** AC-011 */
    public function destroy(array $params): void
    {
        if ($this->guard->requireUser() === null) {
            return;
        }

        try {
            $this->deleteCard->execute((int) $params['id']);
            JsonResponse::send(200, ['message' => 'Carta excluída.']);
        } catch (NotFoundException $e) {
            JsonResponse::error(404, $e->getMessage());
        }
    }
}
