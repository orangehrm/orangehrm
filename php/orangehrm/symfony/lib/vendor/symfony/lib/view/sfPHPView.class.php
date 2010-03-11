<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A view that uses PHP as the templating engine.
 *
 * @package    symfony
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfPHPView.class.php 11783 2008-09-25 16:21:27Z fabien $
 */
class sfPHPView extends sfView
{
  /**
   * Executes any presentation logic for this view.
   */
  public function execute()
  {
  }

  /**
   * Loads core and standard helpers to be use in the template.
   */
  protected function loadCoreAndStandardHelpers()
  {
    static $coreHelpersLoaded = 0;

    if ($coreHelpersLoaded)
    {
      return;
    }

    $coreHelpersLoaded = 1;

    $helpers = array_unique(array_merge(array('Helper', 'Url', 'Asset', 'Tag', 'Escaping'), sfConfig::get('sf_standard_helpers')));

    // remove default Form helper if compat_10 is false
    if (!sfConfig::get('sf_compat_10') && false !== $i = array_search('Form', $helpers))
    {
      unset($helpers[$i]);
    }

    $this->context->getConfiguration()->loadHelpers($helpers);
  }

  /**
   * Renders the presentation.
   *
   * @param  string $_sfFile  Filename
   *
   * @return string File content
   */
  protected function renderFile($_sfFile)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Render "%s"', $_sfFile))));
    }

    $this->loadCoreAndStandardHelpers();

    // EXTR_REFS can't be used (see #3595 and #3151)
    extract($this->attributeHolder->toArray());

    // render
    ob_start();
    ob_implicit_flush(0);
    require($_sfFile);

    return ob_get_clean();
  }

  /**
   * Retrieves the template engine associated with this view.
   *
   * Note: This will return null because PHP itself has no engine reference.
   *
   * @return null
   */
  public function getEngine()
  {
    return null;
  }

  /**
   * Configures template.
   *
   * @return void
   */
  public function configure()
  {
    // store our current view
    $this->context->set('view_instance', $this);

    // require our configuration
    require($this->context->getConfigCache()->checkConfig('modules/'.$this->moduleName.'/config/view.yml'));

    // set template directory
    if (!$this->directory)
    {
      $this->setDirectory($this->context->getConfiguration()->getTemplateDir($this->moduleName, $this->getTemplate()));
    }
  }

  /**
   * Loop through all template slots and fill them in with the results of presentation data.
   *
   * @param  string $content  A chunk of decorator content
   *
   * @return string A decorated template
   */
  protected function decorate($content)
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Decorate content with "%s/%s"', $this->getDecoratorDirectory(), $this->getDecoratorTemplate()))));
    }

    // set the decorator content as an attribute
    $attributeHolder = $this->attributeHolder;

    $this->attributeHolder = $this->initializeAttributeHolder(array('sf_content' => new sfOutputEscaperSafe($content)));
    $this->attributeHolder->set('sf_type', 'layout');

    // render the decorator template and return the result
    $ret = $this->renderFile($this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate());

    $this->attributeHolder = $attributeHolder;

    return $ret;
  }

  /**
   * Renders the presentation.
   *
   * @return string A string representing the rendered presentation
   */
  public function render()
  {
    $content = null;
    if (sfConfig::get('sf_cache'))
    {
      $viewCache = $this->context->getViewCacheManager();
      $uri = $this->context->getRouting()->getCurrentInternalUri();

      if (!is_null($uri))
      {
        list($content, $decoratorTemplate) = $viewCache->getActionCache($uri);
        if (!is_null($content))
        {
          $this->setDecoratorTemplate($decoratorTemplate);
        }
      }
    }

    // render template if no cache
    if (is_null($content))
    {
      // execute pre-render check
      $this->preRenderCheck();

      $this->attributeHolder->set('sf_type', 'action');

      // render template file
      $content = $this->renderFile($this->getDirectory().'/'.$this->getTemplate());

      if (sfConfig::get('sf_cache') && !is_null($uri))
      {
        $content = $viewCache->setActionCache($uri, $content, $this->isDecorator() ? $this->getDecoratorDirectory().'/'.$this->getDecoratorTemplate() : false);
      }
    }

    // now render decorator template, if one exists
    if ($this->isDecorator())
    {
      $content = $this->decorate($content);
    }

    return $content;
  }
}
