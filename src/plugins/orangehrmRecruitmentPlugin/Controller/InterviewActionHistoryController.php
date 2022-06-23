<?php

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class InterviewActionHistoryController extends AbstractVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('candidateVacancyId')) {
            $component = new Component('save-interview-passed');
            $component->addProp(new Prop(
                'candidate-id', Prop::TYPE_NUMBER, $request->attributes->get('candidateVacancyId')));
        } else {
            $component = new Component('save-interview-passed');
        }
        $this->setComponent($component);
    }
}
