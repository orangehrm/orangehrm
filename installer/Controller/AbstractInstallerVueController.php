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
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

abstract class AbstractInstallerVueController extends AbstractInstallerController
{
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
}
