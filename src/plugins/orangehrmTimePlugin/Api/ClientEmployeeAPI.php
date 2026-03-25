<?php
namespace OrangeHRM\Time\Api;

use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Time\Service\ClientEmployeeService;

class ClientEmployeeAPI extends Endpoint
{
    private ?ClientEmployeeService $service = null;

    public function getClientEmployeeService(): ClientEmployeeService
    {
        if ($this->service === null) {
            $this->service = new ClientEmployeeService();
        }

        return $this->service;
    }

    public function getAll(): EndpointResult
    {
        $customerId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_QUERY,
            'customerId'
        );

        $search = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            'nameOrId'
        );

        $employees = $this->getClientEmployeeService()
            ->getEmployeesByCustomer($customerId, $search);

        $result = [];

        foreach ($employees as $emp) {
            $result[] = [
                'empNumber' => $emp['empNumber'],
                'firstName' => $emp['firstName'],
                'middleName' => $emp['middleName'] ?? '',
                'lastName' => $emp['lastName'],
                'terminationId' => $emp['terminationId'] ?? null,
            ];
        }

        return new EndpointResult(['data' => $result]);
    }
}