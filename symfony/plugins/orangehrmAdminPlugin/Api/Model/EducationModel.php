<?php



namespace OrangeHRM\Admin\Api\Model;


use OrangeHRM\Entity\Education;

use Orangehrm\Rest\Api\Entity\Serializable;
use Orangehrm\Rest\Api\Model\ModelTrait;

class EducationModel implements Serializable
{
    use ModelTrait;

    public function __construct(Education $education)
    {
        $this->setEntity($education);
        $this->setFilters(
            [
                'id',
                'name',
            ]
        );
    }
}
