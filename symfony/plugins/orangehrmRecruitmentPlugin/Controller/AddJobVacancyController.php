<?php

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class AddJobVacancyController extends AbstractVueController
{

    public function preRender(Request $request): void
    {
        $component = new Component('add-job-vacancy');
        $this->setComponent($component);
    }
}