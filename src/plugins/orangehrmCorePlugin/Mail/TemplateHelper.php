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

namespace OrangeHRM\Core\Mail;

use Exception;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class TemplateHelper
{
    protected ?Environment $twig = null;

    public function __construct()
    {
        $loader = new ArrayLoader([]);
        $this->twig = new Environment($loader);
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        return $this->twig;
    }

    /**
     * Render given Twig template string with parameters
     *
     * @param string $templateString
     * @param array $context
     * @return string
     * @throws TemplateRenderException
     */
    public function renderTemplate(string $templateString, array $context = []): string
    {
        try {
            $template = $this->getTwig()->createTemplate($templateString);
            return $template->render($context);
        } catch (Exception $e) {
            throw new TemplateRenderException($e);
        }
    }
}
