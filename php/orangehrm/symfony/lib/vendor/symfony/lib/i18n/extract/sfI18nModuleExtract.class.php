<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage i18n
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfI18nModuleExtract.class.php 31248 2010-10-26 13:54:12Z fabien $
 */
class sfI18nModuleExtract extends sfI18nExtract
{
  protected $module = '';

  /**
   * Configures the current extract object.
   */
  public function configure()
  {
    if (!isset($this->parameters['module']))
    {
      throw new sfException('You must give a "module" parameter when extracting for a module.');
    }

    $this->module = $this->parameters['module'];

    $options = $this->i18n->getOptions();
    $dirs = $this->i18n->isMessageSourceFileBased($options['source']) ? $this->i18n->getConfiguration()->getI18NDirs($this->module) : null;
    $this->i18n->setMessageSource($dirs, $this->culture);
  }

  /**
   * Extracts i18n strings.
   *
   * This class must be implemented by subclasses.
   */
  public function extract()
  {
    // Extract from PHP files to find __() calls in actions/ lib/ and templates/ directories
    $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$this->module;
    $this->extractFromPhpFiles(array(
      $moduleDir.'/actions',
      $moduleDir.'/lib',
      $moduleDir.'/templates',
    ));

    // Extract from generator.yml files
    $generator = $moduleDir.'/config/generator.yml';
    if (file_exists($generator))
    {
      $yamlExtractor = new sfI18nYamlGeneratorExtractor();
      $this->updateMessages($yamlExtractor->extract(file_get_contents($generator)));
    }

    // Extract from validate/*.yml files
    $validateFiles = glob($moduleDir.'/validate/*.yml');
    if (is_array($validateFiles))
    {
      foreach ($validateFiles as $validateFile)
      {
        $yamlExtractor = new sfI18nYamlValidateExtractor();
        $this->updateMessages($yamlExtractor->extract(file_get_contents($validateFile)));
      }
    }
  }
}
