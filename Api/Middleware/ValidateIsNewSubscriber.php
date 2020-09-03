<?php

namespace Api\Middleware;

use Api\Repositories\ResourceNotFoundException;
use Api\Repositories\Subscribers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ValidateIsNewSubscriber
{
    private Request $request;
    private Subscribers $subscribersRepository;

    public function __construct(Request $request, Subscribers $subscribersRepository)
    {
        $this->request = $request;
        $this->subscribersRepository = $subscribersRepository;
    }

    public function handle()
    {
        $email = $this->request->get('email');
        try {
            $this->subscribersRepository->findByEmail($email);
            return new JsonResponse(['errors' => ['email_already_registered']], 400);
        } catch (ResourceNotFoundException $e) {
            // do nothing, the email is new
           // dd($e->getMessage());
        }
    }
}
