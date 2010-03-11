<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../../lib/vendor/lime/lime.php');
require_once(dirname(__FILE__).'/../../../../lib/view/escaper/sfOutputEscaper.class.php');
require_once(dirname(__FILE__).'/../../../../lib/view/escaper/sfOutputEscaperGetterDecorator.class.php');
require_once(dirname(__FILE__).'/../../../../lib/view/escaper/sfOutputEscaperArrayDecorator.class.php');
require_once(dirname(__FILE__).'/../../../../lib/view/escaper/sfOutputEscaperObjectDecorator.class.php');
require_once(dirname(__FILE__).'/../../../../lib/view/escaper/sfOutputEscaperIteratorDecorator.class.php');

require_once(dirname(__FILE__).'/../../../../lib/helper/EscapingHelper.php');
require_once(dirname(__FILE__).'/../../../../lib/config/sfConfig.class.php');

class sfException extends Exception
{
}

sfConfig::set('sf_charset', 'UTF-8');

$t = new lime_test(3, new lime_output_color());

class OutputEscaperTest
{
  public function __toString()
  {
    return $this->getTitle();
  }

  public function getTitle()
  {
    return '<strong>escaped!</strong>';
  }

  public function getTitles()
  {
    return array(1, 2, '<strong>escaped!</strong>');
  }
}

$object = new OutputEscaperTest();
$escaped = sfOutputEscaper::escape('esc_entities', $object);

$t->is($escaped->getTitle(), '&lt;strong&gt;escaped!&lt;/strong&gt;', 'The escaped object behaves like the real object');

$array = $escaped->getTitles();
$t->is($array[2], '&lt;strong&gt;escaped!&lt;/strong&gt;', 'The escaped object behaves like the real object');

// __toString()
$t->diag('__toString()');

$t->is($escaped->__toString(), '&lt;strong&gt;escaped!&lt;/strong&gt;', 'The escaped object behaves like the real object');
