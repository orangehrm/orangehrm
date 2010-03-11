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
 * Doctrine data retriever. Used to assist helpers with retrieving data for
 * selector options.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineDataRetriever.class.php 12089 2008-10-08 20:23:25Z Jonathan.Wage $
 */
class sfDoctrineDataRetriever
{
  /**
   * Used internally by symfony for retrieving objects for selector helper options.
   *
   * @param string $model       Name of the model to retrieve the objects from.
   * @param string $peerMethod  Name of the peer method to invoke on the Doctrine_Table instance for the model.
   */
  static public function retrieveObjects($class, $peerMethod = 'findAll')
  {
    if (!$peerMethod)
    {
      $peerMethod = 'findAll';
    }

    $table = Doctrine::getTable($class);

    return call_user_func(array($table, $peerMethod));
  }
}