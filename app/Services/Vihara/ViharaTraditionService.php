<?php

namespace App\Services\Vihara;

use App\Repositories\Contracts\ViharaRepositoryInterface;
use App\Repositories\MySQL\ViharaRepository;
use App\Repositories\MySQL\ViharaTraditionRepository;
use Illuminate\Validation\ValidationException;

class ViharaTraditionService
{
    public function __construct(
        private readonly ViharaTraditionRepository $repository
    ) {}

    public function getTraditionsByViharaId(int $viharaId): object
    {
        return $this->repository->getTraditionsByViharaId($viharaId)->first();
    }
}
