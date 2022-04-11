<?php

namespace OrangeHRM\Installer\Controller;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class SystemCheckController extends AbstractInstallerVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('system-check-screen');
        $this->setComponent($component);
    }
}