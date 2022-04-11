<?php

namespace OrangeHRM\Installer\Controller;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class DatabaseConfigurationController extends AbstractInstallerVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('database-config-screen');
        $this->setComponent($component);
    }
}