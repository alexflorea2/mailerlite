<?php

namespace Api\Middleware;

use Api\Models\Subscriber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ValidateState
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $state = $this->request->get('state');

        if (empty($state) || !in_array($state, Subscriber::ALLOWED_STATES)) {
            return new JsonResponse(['errors' => ['state_not_allowed'],'state' => $state], 400);
        }
    }
}
