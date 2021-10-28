<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
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
