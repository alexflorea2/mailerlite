<?php

namespace Api\Repositories;

class ResourceNotFoundException extends \Exception
{
    protected $message = "Resource not found";
}
