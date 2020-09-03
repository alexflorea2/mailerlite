<?php

namespace Api\Controllers;

use Api\Models\Subscriber;
use Api\Models\SubscriberField;
use Api\Repositories\FieldsRepositoryInterface;
use Api\Repositories\SubscriberFieldsRepositoryInterface;
use Api\Repositories\SubscribersRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SubscribersController
{
    private $request;
    private $subscribersRepository;
    private $subscriberFieldsRepository;

    public function __construct(
        Request $request,
        SubscribersRepositoryInterface $subscribersRepository,
        SubscriberFieldsRepositoryInterface $subscriberFieldsRepository
    ) {
        $this->request = $request;
        $this->subscribersRepository = $subscribersRepository;
        $this->subscriberFieldsRepository = $subscriberFieldsRepository;
    }

    public function find(int $id)
    {
        $subscriber = $this->subscribersRepository->findById($id);

        return new JsonResponse(
            [
                'data' => $subscriber
            ]
        );
    }

    public function getFields(int $id)
    {
        $fields = $this->subscriberFieldsRepository->findForSubscriber($id);

        return new JsonResponse(
            [
                'data' => $fields
            ]
        );
    }

    public function index()
    {
        $take = $this->request->get('take', 10);
        $cursor = $this->request->get('cursor', 0);
        $query = $this->request->get('query');
        $state = $this->request->get('state');

        $subscribers = $this->subscribersRepository->list($cursor, $take, $query, $state);
        $meta = $this->subscribersRepository->meta($cursor, $take, $query, $state);

        return new JsonResponse(
            [
                'data' => $subscribers,
                'meta' => $meta,
            ]
        );
    }

    public function add()
    {
        $email = $this->request->get('email');
        $name = $this->request->get('name');
        $state = $this->request->get('state', Subscriber::STATE_UNCONFIRMED);

        $subscriber = new Subscriber($email, $name, $state);
        $save = $this->subscribersRepository->save($subscriber);

        return new JsonResponse($this->subscribersRepository->findById($save));
    }

    public function addField(int $id)
    {
        $fieldId = $this->request->get('field_id');
        $value = $this->request->get('value');

        $subscriberField = new SubscriberField($value);
        $subscriberField->setFieldId($fieldId);
        $subscriberField->setSubscriberId($id);

        $save = $this->subscriberFieldsRepository->save($subscriberField);

        return new JsonResponse($this->subscriberFieldsRepository->findById($save));
    }

    public function updateState(int $id)
    {
        $state = $this->request->get('state');

        $subscriber = $this->subscribersRepository->findById($id);
        $subscriber->setState($state);

        $save = $this->subscribersRepository->save($subscriber);

        return new JsonResponse($this->subscribersRepository->findById($save));
    }

    public function delete(int $id)
    {
        return new JsonResponse($this->subscribersRepository->delete($id));
    }
}
