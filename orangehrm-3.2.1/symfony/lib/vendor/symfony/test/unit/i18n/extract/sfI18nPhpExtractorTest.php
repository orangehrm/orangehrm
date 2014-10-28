<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../bootstrap/unit.php');

$t = new lime_test(7);

// __construct()
$t->diag('__construct()');
$e = new sfI18nPhpExtractor();
$t->ok($e instanceof sfI18nExtractorInterface, 'sfI18nPhpExtractor implements the sfI18nExtractorInterface interface');

// ->extract();
$t->diag('->extract()');

$content = <<<EOF
__('bar')

<?php __('foo') ?>

<?php __('I\'m "happy"') ?>
<?php __("I'm very \"happy\"") ?>
<?php __("I\\'m so \"happy\"") ?>
EOF;

$t->is($e->extract($content), array('foo', 'I\'m "happy"', 'I\'m very "happy"', 'I\\\'m so "happy"'), '->extract() extracts strings from PHP files');

$content = <<<EOF
<?php format_number_choice('foo') ?>
EOF;

$t->is($e->extract($content), array('foo'), '->extract() takes into account the format_number_choice() helper');

$content = <<<EOF
<?php \$this->getContext()->getI18N()->__('foo') ?>
EOF;

$t->is($e->extract($content), array('foo'), '->extract() takes into account calls in an action file');

$content = <<<EOF
<?php
  echo __('foo');
  echo __("bar");
  echo __ ( 'foobar' );
  echo __('foo %a% bar', array('%a%' => foo));
?>
EOF;

$t->is($e->extract($content), array('foo', 'bar', 'foobar', 'foo %a% bar'), '->extract() extracts strings from \'\' and "" quoted strings');

$content = <<<EOF
<?php
  echo __ ( 'foo' );

  echo __ ( 
    'bar' 
  );
?>
EOF;

$t->is($e->extract($content), array('foo', 'bar'), '->extract() does not care if you add some whitespaces');

$content = <<<EOF
<?php
  echo __(<<<EOD
foo
EOD
);

  echo __(<<<EOD
bar
EOD
);
EOF;

$t->is(fix_linebreaks($e->extract($content)), array("foo\n", "bar\n"), '->extract() extracts strings from HEREDOC quoted strings');
