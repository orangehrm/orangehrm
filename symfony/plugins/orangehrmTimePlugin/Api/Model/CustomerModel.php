<?php


namespace OrangeHRM\Time\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Customer;

class CustomerModel implements Normalizable{

    use ModelTrait;

    public function __construct(Customer $customer)
    {
        $this->setEntity($customer);
        $this->setFilters(
            [
                'customerId',
                'name',
                'description',
            ]
        );
    }

}