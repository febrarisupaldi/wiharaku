<?php

namespace App\Services\Vihara;

use App\Repositories\Contracts\ViharaRepositoryInterface;
use App\Repositories\MySQL\ViharaRepository;
use Illuminate\Validation\ValidationException;

class ViharaSearchService
{
    public function __construct(
        private readonly ViharaRepository $repository
    ) {}

    public function nearby(array $filters)
    {
        $filters['radius'] ??= 5000;

        return $this->repository->search($filters, 'nearby');
    }

    public function radius(array $filters)
    {
        return $this->repository->search($filters, 'radius');
    }

    public function map(array $filters)
    {
        if ($filters['south'] >= $filters['north']) {
            throw ValidationException::withMessages([
                'bounds' => 'South harus lebih kecil daripada north.',
            ]);
        }

        return $this->repository->search($filters, 'map');
    }
}
