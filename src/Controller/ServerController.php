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
        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*', true);
        $response->headers->set('Access-Control-Request-Headers', 'access-control-allow-origin');
            
        $servers = json_decode(file_get_contents(
            $this->getParameter('kernel.project_dir') . '/' . ReadServerListCommand::STATIC_JSON_PATH
        ));

        if ($filters === null) {
            $response->setData([
                'servers' => $servers,
                'availableFilters' => $this->getServerFilters($servers)
            ]);

            return $response;
        }

        foreach ($servers as $server) {
            if ($this->matchServerFilter($server, $filters)) {
                $filteredServers[] = $server;
            }
        }

        $response->setData([
            'servers' => $filteredServers,
            'availableFilters' => $this->getServerFilters($servers)
        ]);

        return $response;
    }

    private function matchServerFilter($server, ServerFiltersDTO $filters): bool
    {
        if (!empty($filters->storage)) {
            $filters->storage = array_merge(ServerFiltersDTO::HDD_STORAGE_MIN_MAX_DEFAULTS, $filters->storage);
            if ($server->hddStorage <= $filters->storage['min']  || $server->hddStorage >= $filters->storage['max']) {
                return false;
            }
        }

        if (!empty($filters->hddType)) {
            if (!in_array($server->hddType, $filters->hddType)) {
                return false;
            }
        }

        if (!empty($filters->ramSize)) {
            if (!in_array($server->ramSize, $filters->ramSize)) {
                return false;
            }
        }

        if (!empty($filters->ramType)) {
            if (!in_array($server->ramType, $filters->ramType)) {
                return false;
            }
        }

        if (!empty($filters->location)) {
            if (!in_array($server->location, $filters->location)) {
                return false;
            }
        }

        if (!empty($filters->price)) {
            $filters->price = array_merge(ServerFiltersDTO::PRICE_MIN_MAX_DEFAULTS, $filters->price);
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
