<?php

namespace App\Controller;

use App\Command\ReadServerListCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/')]
    public function homeAction(): JsonResponse
    {
        $responseData = json_decode(
            file_get_contents(
                $this->getParameter('kernel.project_dir') . '/' . ReadServerListCommand::STATIC_JSON_PATH
            )
        );
        return new JsonResponse($responseData);
    }
}
