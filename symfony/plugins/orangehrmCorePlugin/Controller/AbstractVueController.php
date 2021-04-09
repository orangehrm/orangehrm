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

namespace OrangeHRM\Core\Controller;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Vue\Component;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    protected $twig = null;
    /**
     * @var string
     */
    protected $template = 'vue.html.twig';
    /**
     * @var null|Component
     */
    protected $component = null;

    public function __construct()
    {
        $loader = new FilesystemLoader(Config::get('ohrm_app_template_dir'));
        $this->twig = new Environment($loader, ['cache' => false]);
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
        $this->component = $component;
    }

    /**
     * @return Component
     */
    public function getComponent(): Component
    {
        return $this->component;
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
        return $this->getTwig()->render(
            $this->getTemplate(),
            [
                'componentName' => $this->getComponent()->getName(),
                'componentProps' => $this->getComponent()->getProps(),
                'publicPath' => $request->getBasePath(),
                'baseUrl' => $request->getBaseUrl(),
            ]
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
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(Request $request): Response
    {
        $this->preRender($request);
        $content = $this->render($request);
        $this->postRender($request);

        $response = new Response();
        $response->setContent($content);

        return $response;
    }
}
