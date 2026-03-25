<?php

namespace OrangeHRM\Time\Controller;

use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Time\Service\ClientEmployeeService;

class ClientEmployeesController
{
    private ClientEmployeeService $clientEmployeeService;

    public function __construct()
    {
        $this->clientEmployeeService = new ClientEmployeeService();
    }

    public function execute(Request $request): Response
    {
        $customerId = $request->query->get('customerId');
        $search = $request->query->get('nameOrId');

        $employees = $this->clientEmployeeService
            ->getEmployeesByCustomer($customerId, $search);

        $result = [];

        foreach ($employees as $emp) {
            $result[] = [
                'empNumber' => $emp['empNumber'],
                'firstName' => $emp['firstName'],
                'middleName' => $emp['middleName'] ?? '',
                'lastName' => $emp['lastName'],
            ];
        }

        $response = new Response();
        $response->setContent(json_encode(['data' => $result]));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}