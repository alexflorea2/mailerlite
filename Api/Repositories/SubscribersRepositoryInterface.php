<?php

namespace Api\Repositories;

use Api\Models\Subscriber;

interface SubscribersRepositoryInterface extends SimpleRepositoryInterface
{
    public function list(int $lastID = 0, int $take = 10, ?string $query = null, ?string $state = null): array;

    public function findById(float $id): Subscriber;

    public function save(Subscriber $subscriber);
}
