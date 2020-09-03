<?php

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

if( !function_exists('dd') )
{
    function dd($args)
    {
        var_dump($args);
        die();
    }
}

if( !function_exists('sendResponse') )
{
    function sendResponse(Response $response, bool $enableCORS = true)
    {
        if( $enableCORS )
        {
            $response->headers->set("Access-Control-Allow-Origin", "http://127.0.0.1:8070");
            $response->headers->set("Access-Control-Allow-Credentials", true);
            $response->headers->set('Access-Control-Allow-Methods',"GET, POST, DELETE, PUT, PATCH, OPTIONS");
            $response->headers->set('Access-Control-Allow-Headers', "Content-Type, Accept");
        }

        $response->send();
        exit;
    }
}

