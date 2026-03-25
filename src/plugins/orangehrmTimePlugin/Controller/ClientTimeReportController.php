<?php

namespace OrangeHRM\Time\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class ClientTimeReportController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $component = new Component('client-time-report');
        $this->setComponent($component);
    }
}