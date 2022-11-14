<?php

use Psr\Http\Message\ResponseInterface;

class JsonResponse
{
    /**
     * @param ResponseInterface $response
     * @param array $data
     * @return ResponseInterface
     */
    public static function toJson(ResponseInterface $response, array $data = []) : ResponseInterface {

        $response
            ->getBody()
            ->write(json_encode($data));

        $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}