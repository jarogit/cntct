<?php

namespace App\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseSerializer
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function serialize($data, array $groups = null): Response
    {
        $context = $this->getSerializerContext();
        if ($groups) {
            $context->setGroups($groups);
        }

        $response = new Response(
            $this->serializer->serialize($data, 'json', $context)
        );
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function getSerializerContext()
    {
        return (new SerializationContext)->setSerializeNull(true);
    }
}
