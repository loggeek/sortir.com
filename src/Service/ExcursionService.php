<?php

namespace App\Service;

use App\Repository\ExcursionRepository;

class ExcursionService
{
    private ExcursionRepository $excursionRepository;

    public function __construct(ExcursionRepository $excursionRepository)
    {
        $this->excursionRepository = $excursionRepository;
    }

    public function afficher()
    {
        return $this->excursionRepository->findAll();
    }
}