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
 * @version    SVN: $Id: sfI18nYamlGeneratorExtractor.class.php 9128 2008-05-21 00:58:19Z Carl.Vondrick $
 */
class sfI18nYamlGeneratorExtractor extends sfI18nYamlExtractor
{
  protected $strings = array();

  /**
   * Extract i18n strings for the given content.
   *
   * @param  string $content The content
   *
   * @return array An array of i18n strings
   */
  public function extract($content)
  {
    $this->strings = array();

    $config = sfYaml::load($content);

    if (!isset($config['generator']['param']))
    {
      return array();
    }

    $params = $config['generator']['param'];

    // titles
    if (isset($params['list']['title']))
    {
      $this->strings[] = $params['list']['title'];
    }

    if (isset($params['edit']['title']))
    {
      $this->strings[] = $params['edit']['title'];
    }

    // names and help messages
    if (isset($params['fields']))
    {
      $this->getFromFields($params['fields']);
    }

    if (isset($params['list']['fields']))
    {
      $this->getFromFields($params['list']['fields']);
    }

    if (isset($params['edit']['fields']))
    {
      $this->getFromFields($params['edit']['fields']);
    }

    // edit categories
    if (isset($params['edit']['display']) && !isset($params['edit']['display'][0]))
    {
      foreach (array_keys($params['edit']['display']) as $string)
      {
        if ('NONE' == $string)
        {
          continue;
        }

        $this->strings[] = $string;
      }
    }

    return $this->strings;
  }

  protected function getFromFields($fields)
  {
    foreach ($fields as $field => $options)
    {
      if (isset($options['name']))
      {
        $this->strings[] = $options['name'];
      }

      if (isset($options['help']))
      {
        $this->strings[] = $options['help'];
      }
    }
  }
}
