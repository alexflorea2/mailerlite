<?php

namespace Api\Middleware;

use Api\Repositories\ResourceNotFoundException;
use Api\Repositories\SubscriberFieldsRepositoryInterface;
use Api\Repositories\Subscribers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ValidateSubscriberDoesNotHaveField
{
    private Request $request;
    private SubscriberFieldsRepositoryInterface $subscribersFieldsRepository;

    public function __construct(Request $request, SubscriberFieldsRepositoryInterface $subscribersFieldsRepository)
    {
        $this->request = $request;
        $this->subscribersFieldsRepository = $subscribersFieldsRepository;
    }

    public function handle(int $subscriber_id)
    {
        $test = $this->subscribersFieldsRepository->findFieldForSubscriber(
            $subscriber_id,
            $this->request->get('field_id')
        );

        if ($test) {
            return new JsonResponse(['errors' => ['subscriber_has_field'],'data' => $test], 400);
        }
    }
}
