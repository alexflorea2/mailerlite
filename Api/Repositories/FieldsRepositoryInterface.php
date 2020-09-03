<?php

namespace Api\Repositories;

use Api\Models\Field;

interface FieldsRepositoryInterface extends SimpleRepositoryInterface
{
    public function save(Field $field);
}
