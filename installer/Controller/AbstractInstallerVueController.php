<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Installer\Controller;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dto\AttributeBag;
use OrangeHRM\Core\Helper\VueControllerHelper;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Installer\Util\StateContainer;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class AbstractInstallerVueController extends AbstractInstallerController
{
    public const WELCOME_SCREEN = "Welcome";
    public const DATABASE_INFO_SCREEN = "Database Information";
    public const SYSTEM_CHECK_SCREEN = "System Check";
    public const VERSION_DETAILS_SCREEN = "Version Details";
    public const UPGRADE_SCREEN = "Upgrade";
    public const UPGRADER_COMPLETE_SCREEN = "Completion";
    public const LICENCE_ACCEPTANCE_SCREEN = "License Acceptance";
    public const DATABASE_CONFIG_SCREEN = "Database Configuration";
    public const INSTANCE_CREATION_SCREEN = "Instance Creation";
    public const ADMIN_USER_CREATION_SCREEN = "Admin User Creation";
    public const CONFIRMATION_SCREEN = "Confirmation";
    public const INSTALLATION_SCREEN = "Installation";
    public const INSTALLATION_COMPLETE_SCREEN = "Installation Complete";

    public const INSTALLER_SCREENS = [
        self::WELCOME_SCREEN,
        self::LICENCE_ACCEPTANCE_SCREEN,
        self::DATABASE_CONFIG_SCREEN,
        self::SYSTEM_CHECK_SCREEN,
        self::INSTANCE_CREATION_SCREEN,
        self::ADMIN_USER_CREATION_SCREEN,
        self::CONFIRMATION_SCREEN,
        self::INSTALLATION_SCREEN,
        self::INSTALLATION_COMPLETE_SCREEN
    ];

    public const UPGRADER_SCREENS = [
        self::WELCOME_SCREEN,
        self::DATABASE_INFO_SCREEN,
        self::SYSTEM_CHECK_SCREEN,
        self::VERSION_DETAILS_SCREEN,
        self::UPGRADE_SCREEN,
        self::UPGRADER_COMPLETE_SCREEN,
    ];

    /**
     * @var Environment|null
     */
    private ?Environment $twig;
    /**
     * @var string
     */
    private string $template = 'installer.html.twig';
    /**
     * @var null|Component
     */
    private ?Component $component = null;
    /**
     * @var AttributeBag
     */
    private AttributeBag $context;

    public function __construct()
    {
        $loader = new FilesystemLoader(
            [
                Config::get(Config::APP_TEMPLATE_DIR),
                realpath(__DIR__ . '/../config/templates')
            ]
        );
        $this->twig = new Environment($loader, ['cache' => false]);
        $this->context = new AttributeBag();
        $this->init();
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param Component $component
     */
    public function setComponent(Component $component): void
    {
        $this->component = $component;
    }

    /**
     * @return Component
     */
    public function getComponent(): Component
    {
        return $this->component;
    }

    /**
     * @return AttributeBag
     */
    public function getContext(): AttributeBag
    {
        return $this->context;
    }

    public function init(): void
    {
    }

    /**
     * @param Request $request
     */
    public function preRender(Request $request): void
    {
    }

    /**
     * @param Request $request
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(Request $request): string
    {
        $this->getContext()->add($this->getContextParams($request));
        return $this->getTwig()->render(
            $this->getTemplate(),
            $this->getContext()->all(),
        );
    }

    /**
     * @param Request $request
     */
    public function postRender(Request $request): void
    {
    }

    /**
     * @param Request $request
     * @return Response|RedirectResponse
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    protected function execute(Request $request)
    {
        $this->preRender($request);
        $content = $this->render($request);
        $this->postRender($request);

        $response = $this->getResponse();
        if (isset($content)) {
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getContextParams(Request $request): array
    {
        return [
            VueControllerHelper::COMPONENT_NAME => $this->getComponent()->getName(),
            VueControllerHelper::COMPONENT_PROPS => $this->getComponent()->getProps(),
            VueControllerHelper::PUBLIC_PATH => $request->getBasePath(),
            VueControllerHelper::BASE_URL => $request->getBaseUrl(),
            VueControllerHelper::ASSETS_VERSION => $this->getAssetsVersion(),
            VueControllerHelper::COPYRIGHT_YEAR => date('Y'),
            VueControllerHelper::PRODUCT_VERSION => Config::PRODUCT_VERSION,
            VueControllerHelper::PRODUCT_NAME => Config::PRODUCT_NAME,
            'steps' => $this->getSteps(),
            'currentStep' => $this->getCurrentStep(),
        ];
    }

    /**
     * @return string
     */
    protected function getAssetsVersion(): string
    {
        $pathToBuildFile = realpath(__DIR__ . '/../client/dist/build');
        if (!$pathToBuildFile) {
            return (new DateTime())->getTimestamp();
        }
        return file_get_contents($pathToBuildFile);
    }

    /**
     * @return string[]
     */
    protected function getSteps(): array
    {
        if (is_null(StateContainer::getInstance()->isUpgrader())) {
            return [self::WELCOME_SCREEN];
        } elseif (StateContainer::getInstance()->isUpgrader()) {
            return self::UPGRADER_SCREENS;
        } else {
            return self::INSTALLER_SCREENS;
        }
    }

    /**
     * @return int
     */
    protected function getCurrentStep(): int
    {
        $currentScreen = StateContainer::getInstance()->getCurrentScreen();
        $screens = self::INSTALLER_SCREENS;
        if (StateContainer::getInstance()->isUpgrader()) {
            $screens = self::UPGRADER_SCREENS;
        }
        return array_flip($screens)[$currentScreen] ?? 0;
    }
}
