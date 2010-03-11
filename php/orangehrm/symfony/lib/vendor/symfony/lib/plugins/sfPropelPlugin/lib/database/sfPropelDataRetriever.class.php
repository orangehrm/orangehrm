<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfGenerator is the abstract base class for all generators.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Olivier Verdier <Olivier.Verdier@gmail.com>
 * @version    SVN: $Id $
 */
class sfPropelDataRetriever
{
  static public function retrieveObjects($class, $peerMethod = null, $criteria = null)
  {
    if (!$peerMethod)
    {
      $peerMethod = 'doSelect';
    }

    $classPeer = constant($class.'::PEER');

    if (!is_callable(array($classPeer, $peerMethod)))
    {
      throw new sfException(sprintf('Peer method "%s" not found for class "%s"', $peerMethod, $classPeer));
    }

    return call_user_func(array($classPeer, $peerMethod), is_null($criteria) ? new Criteria() : $criteria);
  }
}