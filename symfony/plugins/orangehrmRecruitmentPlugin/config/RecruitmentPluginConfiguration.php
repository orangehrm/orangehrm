<?php


use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use OrangeHRM\Recruitment\Service\VacancyService;

class RecruitmentPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;

    /**
     * @inheritDoc
     */
    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::VACANCY_SERVICE,
            VacancyService::class
        );

    }

}