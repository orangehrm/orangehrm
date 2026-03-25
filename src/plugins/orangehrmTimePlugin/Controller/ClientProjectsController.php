<?php

namespace OrangeHRM\Time\Controller;

use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Time\Service\ClientProjectService;

class ClientProjectsController
{
    private ClientProjectService $clientProjectService;

    public function __construct()
    {
        $this->clientProjectService = new ClientProjectService();
    }

    public function execute(Request $request): Response
    {
        // Get query params
        $customerId = $request->query->get('customerId');
        $search = $request->query->get('name');

        // If no customer selected → return empty
        if (!$customerId) {
            $response = new Response();
            $response->setContent(json_encode(['data' => []]));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        // Fetch filtered projects
        $projects = $this->clientProjectService
            ->getProjectsByCustomer($customerId, $search);

        // Format response
        $result = [];

        foreach ($projects as $project) {
            $result[] = [
                'id' => $project['id'],
                'name' => $project['name'],
            ];
        }

        // Return JSON response
        $response = new Response();
        $response->setContent(json_encode(['data' => $result]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}