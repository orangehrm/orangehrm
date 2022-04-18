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

namespace OrangeHRM\Core\Helper;

use OrangeHRM\Admin\Traits\Service\UserServiceTrait;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Service\ScreenPermissionService;
use OrangeHRM\Core\Dto\AttributeBag;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Service\MenuService;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\Service\MenuServiceTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\I18N\Traits\Service\I18NHelperTrait;

class VueControllerHelper
{
    use ModuleScreenHelperTrait;
    use UserServiceTrait;
    use MenuServiceTrait;
    use I18NHelperTrait;

    public const COMPONENT_NAME = 'componentName';
    public const COMPONENT_PROPS = 'componentProps';
    public const PUBLIC_PATH = 'publicPath';
    public const BASE_URL = 'baseUrl';
    public const ASSETS_VERSION = 'assetsVersion';
    public const USER = 'user';
    public const SIDE_PANEL_MENU_ITEMS = 'sidePanelMenuItems';
    public const TOP_MENU_ITEMS = 'topMenuItems';
    public const CONTEXT_TITLE = 'contextTitle';
    public const CONTEXT_ICON = 'contextIcon';
    public const COPYRIGHT_YEAR = 'copyrightYear';
    public const PRODUCT_VERSION = 'productVersion';
    public const PRODUCT_NAME = 'productName';
    public const PERMISSIONS = 'permissions';
    public const BREADCRUMB = 'breadcrumb';

    /**
     * @var Request|null
     */
    protected ?Request $request = null;
    /**
     * @var null|Component
     */
    protected ?Component $component = null;
    /**
     * @var AttributeBag
     */
    protected AttributeBag $context;

    /**
     * @var MenuService|null
     */
    protected ?MenuService $menuService = null;

    /**
     * @var ScreenPermissionService|null
     */
    protected ?ScreenPermissionService $screenPermissionService = null;

    public function __construct()
    {
        $this->context = new AttributeBag();
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * @param Request|null $request
     */
    public function setRequest(?Request $request): void
    {
        $this->request = $request;
    }

    /**
     * @return Component|null
     */
    public function getComponent(): ?Component
    {
        return $this->component;
    }

    /**
     * @param Component|null $component
     */
    public function setComponent(?Component $component): void
    {
        $this->component = $component;
    }

    /**
     * @return array
     */
    public function getContextParams(): array
    {
        list($sidePanelMenuItems, $topMenuItems) = $this->getMenuItems();
        list($contextTitle, $contextIcon) = $this->getContextItems();

        $this->context->add(
            [
                self::COMPONENT_NAME => $this->getComponent()->getName(),
                self::COMPONENT_PROPS => $this->getComponent()->getProps(),
                self::PUBLIC_PATH => $this->getRequest()->getBasePath(),
                self::BASE_URL => $this->getRequest()->getBaseUrl(),
                self::ASSETS_VERSION => $this->getAssetsVersion(),
                self::USER => $this->getUserObject(),
                self::SIDE_PANEL_MENU_ITEMS => $sidePanelMenuItems,
                self::TOP_MENU_ITEMS => $topMenuItems,
                self::CONTEXT_TITLE => $contextTitle,
                self::CONTEXT_ICON => $contextIcon,
                self::COPYRIGHT_YEAR => date('Y'),
                self::PRODUCT_VERSION => Config::PRODUCT_VERSION,
                self::PRODUCT_NAME => Config::PRODUCT_NAME,
                self::BREADCRUMB => $this->getBreadcrumb(),
            ]
        );
        return $this->context->all();
    }

    /**
     * @return string
     */
    protected function getAssetsVersion(): string
    {
        return Config::get('ohrm_vue_build_timestamp');
    }

    /**
     * @return int|null
     */
    protected function getUserId(): ?int
    {
        return User::getInstance()->getUserId();
    }

    /**
     * @return array
     */
    protected function getUserObject(): array
    {
        if (is_null($this->getUserId())) {
            // No logged in user, may be in login page
            return [];
        }

        $user = $this->getUserService()->getSystemUser($this->getUserId());
        if (is_null($user->getEmployee())) {
            $profileImgUrl = sprintf(
                '%s/dist/img/user-default-400.png?%s',
                $this->getRequest()->getBasePath(),
                $this->getAssetsVersion()
            );
        } else {
            $profileImgUrl = sprintf(
                '%s/pim/viewPhoto/empNumber/%s',
                $this->getRequest()->getBaseUrl(),
                $user->getEmployee()->getEmpNumber()
            );
        }

        $firstName = $user->getEmployee() ?
            $user->getEmployee()->getFirstName() :
            $user->getUserRole()->getDisplayName();
        $lastName = $user->getEmployee() ? $user->getEmployee()->getLastName() : null;
        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'profImgSrc' => $profileImgUrl,
        ];
    }

    /**
     * @return array[]
     */
    protected function getMenuItems(): array
    {
        try {
            return $this->getMenuService()->getMenuItems($this->getRequest()->getBaseUrl());
        } catch (ServiceException $e) {
        }
        return [[], []];
    }

    /**
     * @return array|null[]
     */
    protected function getContextItems(): array
    {
        $currentScreen = $this->getScreenPermissionService()->getCurrentScreen();
        if ($currentScreen) {
            // TODO:: nav bar icon per screen
            return [$currentScreen->getName(), null];
        }
        return [null, null];
    }

    /**
     * @return ScreenPermissionService
     */
    public function getScreenPermissionService(): ScreenPermissionService
    {
        if (!$this->screenPermissionService instanceof ScreenPermissionService) {
            $this->screenPermissionService = new ScreenPermissionService();
        }
        return $this->screenPermissionService;
    }

    /**
     * @return array
     */
    public function getBreadcrumb(): array
    {
        // this function will return an array that contains the module name and level in the particular screen dynamically
        $breadcrumb = [];
        // TODO
        $breadcrumb['moduleName'] = $this->getCurrentModuleAndScreen()->getModule() === 'pim' ? strtoupper(
            $this->getCurrentModuleAndScreen()->getModule()
        ) : ucfirst($this->getCurrentModuleAndScreen()->getModule());
        $menuItem = $this->getMenuService()->getMenuDao()->getMenuItemByModuleAndScreen(
            $this->getCurrentModuleAndScreen()->getModule(),
            $this->getCurrentModuleAndScreen()->getScreen()
        );
        if ($menuItem) {
            //TODO needed to fix for add screens
            $breadcrumb['level'] = $menuItem->getLevel() == 3 ? $menuItem->getParent()->getMenuTitle() : null;
            if (!is_null($breadcrumb['level'])) {
                $breadcrumb['level'] =$this->getI18NHelper()->transBySource($breadcrumb['level']);
            }
        }
        if ($breadcrumb['moduleName']) {
            $breadcrumb['moduleName'] =$this->getI18NHelper()->transBySource($breadcrumb['moduleName']);
        }
        return $breadcrumb;
    }
}
