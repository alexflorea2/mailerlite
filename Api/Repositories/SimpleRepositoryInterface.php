<?php

namespace Api\Repositories;

interface SimpleRepositoryInterface
{
    public function list(int $lastID = 0, int $take = 10): array;

    public function findById(float $id);
}
