<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Default Doctrine_Record listener
 * 
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineRecordListener.class.php 11878 2008-09-30 20:14:40Z Jonathan.Wage $
 */
class sfDoctrineRecordListener extends Doctrine_Record_Listener
{
  /**
   * preInsert
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function preInsert(Doctrine_Event $event)
  {
    if ($event->getInvoker()->getTable()->hasColumn('created_at'))
    {
      $event->getInvoker()->created_at = date('Y-m-d H:i:s', time());
    }
    
    if ($event->getInvoker()->getTable()->hasColumn('updated_at'))
    {
      $event->getInvoker()->updated_at = date('Y-m-d H:i:s', time());
    }
  }
  
  /**
   * preUpdate
   *
   * @param string $Doctrine_Event 
   * @return void
   */
  public function preUpdate(Doctrine_Event $event)
  {
    if ($event->getInvoker()->getTable()->hasColumn('updated_at'))
    {
      $event->getInvoker()->updated_at = date('Y-m-d H:i:s', time());
    }
  }
}