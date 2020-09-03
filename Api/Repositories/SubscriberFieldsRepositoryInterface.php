<?php

namespace Api\Repositories;

use Api\Models\SubscriberField;

interface SubscriberFieldsRepositoryInterface extends SimpleRepositoryInterface
{
    public function save(SubscriberField $field);

    public function findForSubscriber(int $id): array;

    public function findFieldForSubscriber(int $subscriber_id, int $field_id);
}
