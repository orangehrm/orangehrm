<?php

namespace OrangeHRM\Installer\Controller;

use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;

class CurrentVersionDetailsController extends AbstractInstallerVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('current-version-screen');
        $this->setComponent($component);
    }
}