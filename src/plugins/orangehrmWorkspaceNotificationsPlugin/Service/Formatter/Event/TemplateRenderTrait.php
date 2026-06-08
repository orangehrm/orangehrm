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

namespace OrangeHRM\WorkspaceNotifications\Service\Formatter\Event;

use OrangeHRM\Config\Config;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Autoescape is OFF because templates emit plain text for chat platforms —
 * HTML escaping would turn apostrophes ("Let's") into &#039; in the webhook
 * payload.
 */
trait TemplateRenderTrait
{
    private static ?Environment $twigEnv = null;

    /**
     * @param string $templateName e.g. 'birthday.twig'
     * @param array $context Twig context (must include `dialect`)
     */
    protected function renderTemplate(string $templateName, array $context): string
    {
        return rtrim(self::getTwig()->render($templateName, $context));
    }

    private static function getTwig(): Environment
    {
        if (self::$twigEnv === null) {
            $loader = new FilesystemLoader(
                Config::get(Config::PLUGINS_DIR)
                . '/orangehrmWorkspaceNotificationsPlugin/Service/Formatter/Event/templates'
            );
            self::$twigEnv = new Environment($loader, ['autoescape' => false]);
        }
        return self::$twigEnv;
    }
}
