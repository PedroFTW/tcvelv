<?php

namespace App\Controller;

use App\Command\ReadServerListCommand;
use App\DTO\ServerFiltersDTO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ServerController extends AbstractController
{
    #[Route('/server', name: 'search')]
    public function searchServerAction(
        #[MapQueryString] ?ServerFiltersDTO $filters
    ): JsonResponse {
        $filteredServers = [];
        $servers = json_decode(file_get_contents(
            $this->getParameter('kernel.project_dir') . '/' . ReadServerListCommand::STATIC_JSON_PATH
        ));

        if ($filters === null) {
            return new JsonResponse(
                [
                    'servers' => $servers,
                    'availableFilters' => $this->getServerFilters($servers)
                ]
            );
        }

        foreach ($servers as $server) {
            if ($this->matchServerFilter($server, $filters)) {
                $filteredServers[] = $server;
            }
        }
        return new JsonResponse([
            'servers' => $filteredServers,
            'availableFilters' => $this->getServerFilters($servers)
        ]);
    }

    private function matchServerFilter($server, ServerFiltersDTO $filters): bool
    {
        if (!empty($filters->storage)) {
            if ($server->hddStorage <= $filters->storage['min'] || $server->hddStorage >= $filters->storage['max']) {
                return false;
            }
        }

        if (!empty($filters->hddType)) {
            if (!in_array($server->hddType, $filters->hddType)) {
                return false;
            }
        }

        if (!empty($filters->ram)) {
            if (!in_array($server->ramSize, $filters->ram)) {
                return false;
            }
        }

        if (!empty($filters->hddType)) {
            if (!in_array($server->ramType, $filters->ramType)) {
                return false;
            }
        }

        if (!empty($filters->location)) {
            if (!in_array($server->location, $filters->location)) {
                return false;
            }
        }

        if (!empty($filters->storage)) {
            if ($server->price <= $filters->price['min'] || $server->price >= $filters->price['max']) {
                return false;
            }
        }

        return true;
    }

    private function getServerFilters(array $servers): array
    {
        $filters = [
            'model' => [],
            'ramType' => [],
            'ramSize' => [],
            'hddType' => [],
            'hddStorage' => [],
            'hddStorageDistribution' => [],
            'location' => [],
            'currency' => [],
            'price' => [],
        ];

        foreach ($servers as $server) {
            foreach ($server as $field => $value) {
                if (!in_array($value, $filters[$field])) {
                    $filters[$field][] = $value;
                }
            }
        }

        foreach (array_keys($filters) as $field) {
            sort($filters[$field]);
        }

        return $filters;
    }
}
