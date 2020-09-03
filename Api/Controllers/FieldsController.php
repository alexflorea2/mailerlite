<?php

namespace Api\Controllers;

use Api\Repositories\FieldsRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FieldsController
{
    private $request;
    private $fieldsRepository;

    public function __construct(Request $request, FieldsRepositoryInterface $fieldsRepository)
    {
        $this->request = $request;
        $this->fieldsRepository = $fieldsRepository;
    }

    public function index()
    {
        $fields = $this->fieldsRepository->list();
        return new JsonResponse(['data' => $fields,'meta' => ['total' => count($fields)]]);
    }

    public function add()
    {
        $title = $this->request->get('title');
        $type = $this->request->get('type');
        $description = $this->request->get('description');
        $default_value = $this->request->get('default_value');

        $result = $this->fieldsRepository->add($title, $type);

        return new JsonResponse($result);
    }

    public function delete(int $id)
    {
        dd($id);
    }
}
