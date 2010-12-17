<?php

/**
 * cache components.
 *
 * @package    project
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: components.class.php 2319 2006-10-05 09:03:13Z fabien $
 */
class cacheComponents extends sfComponents
{
  public function executeComponent()
  {
    $this->componentParam = 'componentParam';
    $this->requestParam = $this->getRequestParameter('param');
  }

  public function executeCacheableComponent()
  {
    $this->componentParam = 'componentParam';
    $this->requestParam = $this->getRequestParameter('param');
  }

  public function executeContextualComponent()
  {
    $this->componentParam = 'componentParam';
    $this->requestParam = $this->getRequestParameter('param');
  }

  public function executeContextualCacheableComponent()
  {
    $this->componentParam = 'componentParam';
    $this->requestParam = $this->getRequestParameter('param');
  }
}
