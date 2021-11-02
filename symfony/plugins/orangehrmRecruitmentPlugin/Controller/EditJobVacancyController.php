<?php

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class EditJobVacancyController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $id = $request->get('id');
        $component = new Component('edit-job-vacancy');
        $component->addProp(new Prop('vacancy-id', Prop::TYPE_NUMBER, $id));
        $this->setComponent($component);
    }
}