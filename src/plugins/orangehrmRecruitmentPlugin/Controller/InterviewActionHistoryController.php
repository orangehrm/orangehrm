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
        $component = new Component('action-history');
        $component->addProp(new Prop(
            'candidate-id', Prop::TYPE_NUMBER, $request->attributes->get('candidateId')
        ));
        $component->addProp(new Prop(
            'history-id', Prop::TYPE_NUMBER, $request->attributes->get('historyId')
        ));
        $this->setComponent($component);
    }
}
