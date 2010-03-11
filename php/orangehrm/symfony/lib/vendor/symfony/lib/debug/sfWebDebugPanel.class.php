<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanel represents a web debug panel.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanel.class.php 11164 2008-08-26 19:55:02Z fabien $
 */
abstract class sfWebDebugPanel
{
  protected
    $webDebug = null;

  /**
   * Constructor.
   *
   * @param sfWebDebug $webDebug The web debut toolbar instance
   */
  public function __construct(sfWebDebug $webDebug)
  {
    $this->webDebug = $webDebug;
  }

  /**
   * Gets the link URL for the link.
   *
   * @return string The URL link
   */
  public function getTitleUrl()
  {
  }

  /**
   * Gets the text for the link.
   *
   * @return string The link text
   */
  abstract public function getTitle();

  /**
   * Gets the title of the panel.
   *
   * @return string The panel title
   */
  abstract public function getPanelTitle();

  /**
   * Gets the panel HTML content.
   *
   * @return string The panel HTML content
   */
  abstract public function getPanelContent();
}
