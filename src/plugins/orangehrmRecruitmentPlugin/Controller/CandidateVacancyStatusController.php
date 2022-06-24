<?php

namespace OrangeHRM\Recruitment\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;

class CandidateVacancyStatusController extends AbstractVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('save-interview-passed');
        $component->addProp(new Prop(
            'candidate-id', Prop::TYPE_NUMBER, $request->attributes->get('candidateId')
        ));
        $component->addProp(new Prop(
            'interview-id', Prop::TYPE_NUMBER, $request->attributes->get('interviewId')
        ));
        $this->setComponent($component);
    }
}
