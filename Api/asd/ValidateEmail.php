<?php

namespace Api\Middleware;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ValidateEmail
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        $email = $this->request->get('email');

        if (empty($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return new JsonResponse(['errors' => ['email_format_is_not_correct']], 400);
        }

        if (checkdnsrr(explode("@", $email)[1], "MX") === false) {
            return new JsonResponse(['errors' => ['email_domain_mx_not_found']], 400);
        }
    }
}
