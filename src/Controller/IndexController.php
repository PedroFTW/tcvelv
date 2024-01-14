<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController
{
    #[Route('/')]
    public function homeAction(): JsonResponse
    {
        $responseData = [
            'location' => 'Index@home'
        ];
        return new JsonResponse($responseData);
    }
}