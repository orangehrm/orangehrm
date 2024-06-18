<?php

namespace OrangeHRM\Admin\Traits\Service;

use OrangeHRM\Admin\Service\PayGradeService;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Services;

trait PayGradeServiceTrait
{
    use ServiceContainerTrait;

    /**
     * @return PayGradeService
     */
    public function getPayGradeService(): PayGradeService
    {
        return $this->getContainer()->get(Services::PAY_GRADE_SERVICE);
    }
}
