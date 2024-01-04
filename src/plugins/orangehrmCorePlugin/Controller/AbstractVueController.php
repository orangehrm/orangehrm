<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Core\Controller;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Controller\Exception\VueControllerException;
use OrangeHRM\Core\Dto\AttributeBag;
use OrangeHRM\Core\Exception\ServiceException;
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

abstract class AbstractVueController extends AbstractViewController
{
    /**
     * @var Environment|null
     */
    private ?Environment $twig = null;
    /**
     * @var string
     */
    private string $template = 'vue.html.twig';
    /**
     * @var null|Component
     */
    private ?Component $component = null;
    /**
     * @var AttributeBag
     */
    private AttributeBag $context;
    /**
     * @var VueControllerHelper
     */
    private VueControllerHelper $vueControllerHelper;

    /**
     * @var bool
     */
    private bool $handled = false;

    public function __construct()
    {
        $loader = new FilesystemLoader(Config::get(Config::APP_TEMPLATE_DIR));
        $this->twig = new Environment($loader, ['cache' => false]);
        $this->context = new AttributeBag();
        $this->vueControllerHelper = new VueControllerHelper();
        $this->init();
    }

    /**
     * @param Environment $twig
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
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
        if ($this->isHandled()) {
            throw VueControllerException::alreadyHandled();
        }
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
     * @throws VueControllerException
     */
    public function init(): void
    {
    }

    /**
     * @param Request $request
     * @throws VueControllerException
     */
    public function preRender(Request $request): void
    {
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(Request $request): string
    {
        $this->vueControllerHelper->setRequest($request);
        $this->vueControllerHelper->setComponent($this->getComponent());
        $this->getContext()->add($this->vueControllerHelper->getContextParams());
        return $this->getTwig()->render(
            $this->getTemplate(),
            $this->getContext()->all(),
        );
    }

    /**
     * @param Request $request
     * @throws VueControllerException
     */
    public function postRender(Request $request): void
    {
    }

    /**
     * @param Request $request
     * @return Response|RedirectResponse
     * @throws LoaderError
     * @throws RuntimeError
     * @throws ServiceException
     * @throws SyntaxError
     */
    public function handle(Request $request)
    {
        if (!$this->isHandled()) {
            $this->preRender($request);
        }
        if (!$this->isHandled()) {
            $content = $this->render($request);
        }
        if (!$this->isHandled()) {
            $this->postRender($request);
        }

        $response = $this->getResponse();
        if (isset($content)) {
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * @return AttributeBag
     */
    public function getContext(): AttributeBag
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    protected function handleBadRequest(?Response $response = null): Response
    {
        // TODO:: develop UI for bad request controllers
        $component = new Component('bad-request');
        $this->setComponent($component);
        $this->setHandled(true);
        return parent::handleBadRequest($response);
    }

    /**
     * @param RedirectResponse|Response|null $response
     */
    protected function setResponse($response): void
    {
        parent::setResponse($response);
        $this->setHandled(!is_null($response));
    }

    /**
     * @return bool
     */
    protected function isHandled(): bool
    {
        return $this->handled;
    }

    /**
     * @param bool $handled
     */
    protected function setHandled(bool $handled): void
    {
        $this->handled = $handled;
    }
}
