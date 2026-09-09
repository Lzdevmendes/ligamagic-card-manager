<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class ValidationException extends DomainException
{
    /** @var array<string, string> */
    private array $fields;

    /**
     * @param array<string, string> $fields nome do campo => mensagem de erro
     */
    public function __construct(array $fields, string $message = 'Dados inválidos.')
    {
        parent::__construct($message);
        $this->fields = $fields;
    }

    /** @return array<string, string> */
    public function fields(): array
    {
        return $this->fields;
    }
}
