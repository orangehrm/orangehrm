<?php

/**
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Unit test library.
 *
 * @package    lime
 * @author     Fabien Potencier <fabien.potencier@gmail.com>
 * @version    SVN: $Id: lime.php 18665 2009-05-26 19:41:00Z fabien $
 */
class lime_test
{
  const EPSILON = 0.0000000001;

  public $plan = null;
  public $test_nb = 0;
  public $failed = 0;
  public $passed = 0;
  public $skipped = 0;
  public $output = null;

  public function __construct($plan = null, $output_instance = null)
  {
    $this->plan = $plan;
    $this->output = $output_instance ? $output_instance : new lime_output();

    null !== $this->plan and $this->output->echoln(sprintf("1..%d", $this->plan));
  }

  public function __destruct()
  {
    $total = $this->passed + $this->failed + $this->skipped;

    null === $this->plan and $this->plan = $total and $this->output->echoln(sprintf("1..%d", $this->plan));

    if ($total > $this->plan)
    {
      $this->output->red_bar(sprintf(" Looks like you planned %d tests but ran %d extra.", $this->plan, $total - $this->plan));
    }
    elseif ($total < $this->plan)
    {
      $this->output->red_bar(sprintf(" Looks like you planned %d tests but only ran %d.", $this->plan, $total));
    }

    if ($this->failed)
    {
      $this->output->red_bar(sprintf(" Looks like you failed %d tests of %d.", $this->failed, $this->passed + $this->failed));
    }
    else if ($total == $this->plan)
    {
      $this->output->green_bar(" Looks like everything went fine.");
    }

    flush();
  }

  public function ok($exp, $message = '')
  {
    if ($result = (boolean) $exp)
    {
      ++$this->passed;
    }
    else
    {
      ++$this->failed;
    }
    $this->output->echoln(sprintf("%s %d%s", $result ? 'ok' : 'not ok', ++$this->test_nb, $message = $message ? sprintf('%s %s', 0 === strpos($message, '#') ? '' : ' -', $message) : ''));

    if (!$result)
    {
      $traces = debug_backtrace();
      if (!empty($_SERVER['PHP_SELF'])) 
      {
        $i = strstr($traces[0]['file'], $_SERVER['PHP_SELF']) ? 0 : (isset($traces[1]['file']) ? 1 : 0);
      }
      else
      {
        $i = 0;
      }
      $this->output->diag(sprintf('    Failed test (%s at line %d)', str_replace(getcwd(), '.', $traces[$i]['file']), $traces[$i]['line']));
    }

    return $result;
  }

  public function is($exp1, $exp2, $message = '')
  {
    if (is_object($exp1) || is_object($exp2))
    {
      $value = $exp1 === $exp2;
    }
    else if (is_float($exp1) && is_float($exp2))
    {
      $value = abs($exp1 - $exp2) < self::EPSILON;
    }
    else
    {
      $value = $exp1 == $exp2;
    }

    if (!$result = $this->ok($value, $message))
    {
      $this->output->diag(sprintf("           got: %s", var_export($exp1, true)), sprintf("      expected: %s", var_export($exp2, true)));
    }

    return $result;
  }

  public function isnt($exp1, $exp2, $message = '')
  {
    if (!$result = $this->ok($exp1 != $exp2, $message))
    {
      $this->output->diag(sprintf("      %s", var_export($exp2, true)), '          ne', sprintf("      %s", var_export($exp2, true)));
    }

    return $result;
  }

  public function like($exp, $regex, $message = '')
  {
    if (!$result = $this->ok(preg_match($regex, $exp), $message))
    {
      $this->output->diag(sprintf("                    '%s'", $exp), sprintf("      doesn't match '%s'", $regex));
    }

    return $result;
  }

  public function unlike($exp, $regex, $message = '')
  {
    if (!$result = $this->ok(!preg_match($regex, $exp), $message))
    {
      $this->output->diag(sprintf("               '%s'", $exp), sprintf("      matches '%s'", $regex));
    }

    return $result;
  }

  public function cmp_ok($exp1, $op, $exp2, $message = '')
  {
    eval(sprintf("\$result = \$exp1 $op \$exp2;"));
    if (!$this->ok($result, $message))
    {
      $this->output->diag(sprintf("      %s", str_replace("\n", '', var_export($exp1, true))), sprintf("          %s", $op), sprintf("      %s", str_replace("\n", '', var_export($exp2, true))));
    }

    return $result;
  }

  public function can_ok($object, $methods, $message = '')
  {
    $result = true;
    $failed_messages = array();
    foreach ((array) $methods as $method)
    {
      if (!method_exists($object, $method))
      {
        $failed_messages[] = sprintf("      method '%s' does not exist", $method);
        $result = false;
      }
    }

    !$this->ok($result, $message);

    !$result and $this->output->diag($failed_messages);

    return $result;
  }

  public function isa_ok($var, $class, $message = '')
  {
    $type = is_object($var) ? get_class($var) : gettype($var);
    if (!$result = $this->ok($type == $class, $message))
    {
      $this->output->diag(sprintf("      variable isn't a '%s' it's a '%s'", $class, $type));
    }

    return $result;
  }

  public function is_deeply($exp1, $exp2, $message = '')
  {
    if (!$result = $this->ok($this->test_is_deeply($exp1, $exp2), $message))
    {
      $this->output->diag(sprintf("           got: %s", str_replace("\n", '', var_export($exp1, true))), sprintf("      expected: %s", str_replace("\n", '', var_export($exp2, true))));
    }

    return $result;
  }

  public function pass($message = '')
  {
    return $this->ok(true, $message);
  }

  public function fail($message = '')
  {
    return $this->ok(false, $message);
  }

  public function diag($message)
  {
    $this->output->diag($message);
  }

  public function skip($message = '', $nb_tests = 1)
  {
    for ($i = 0; $i < $nb_tests; $i++)
    {
      ++$this->skipped and --$this->passed;
      $this->pass(sprintf("# SKIP%s", $message ? ' '.$message : ''));
    }
  }

  public function todo($message = '')
  {
    ++$this->skipped and --$this->passed;
    $this->pass(sprintf("# TODO%s", $message ? ' '.$message : ''));
  }

  public function include_ok($file, $message = '')
  {
    if (!$result = $this->ok((@include($file)) == 1, $message))
    {
      $this->output->diag(sprintf("      Tried to include '%s'", $file));
    }

    return $result;
  }

  private function test_is_deeply($var1, $var2)
  {
    if (gettype($var1) != gettype($var2))
    {
      return false;
    }

    if (is_array($var1))
    {
      ksort($var1);
      ksort($var2);

      $keys1 = array_keys($var1);
      $keys2 = array_keys($var2);
      if (array_diff($keys1, $keys2) || array_diff($keys2, $keys1))
      {
        return false;
      }
      $is_equal = true;
      foreach ($var1 as $key => $value)
      {
        $is_equal = $this->test_is_deeply($var1[$key], $var2[$key]);
        if ($is_equal === false)
        {
          break;
        }
      }

      return $is_equal;
    }
    else
    {
      return $var1 === $var2;
    }
  }

  public function comment($message)
  {
    $this->output->comment($message);
  }

  public function info($message)
  {
    $this->output->info($message);
  }

  public function error($message)
  {
    $this->output->error($message);
  }

  public static function get_temp_directory()
  {
    if ('\\' == DIRECTORY_SEPARATOR)
    {
      foreach (array('TEMP', 'TMP', 'windir') as $dir)
      {
        if ($var = isset($_ENV[$dir]) ? $_ENV[$dir] : getenv($dir))
        {
          return $var;
        }
      }

      return getenv('SystemRoot').'\temp';
    }

    if ($var = isset($_ENV['TMPDIR']) ? $_ENV['TMPDIR'] : getenv('TMPDIR'))
    {
      return $var;
    }

    return '/tmp';
  }
}

class lime_output
{
  public function diag()
  {
    $messages = func_get_args();
    foreach ($messages as $message)
    {
      array_map(array($this, 'comment'), (array) $message);
    }
  }

  public function comment($message)
  {
    echo "# $message\n";
  }

  public function info($message)
  {
    echo "> $message\n";
  }

  public function error($message)
  {
    echo "> $message\n";
  }

  public function echoln($message)
  {
    echo "$message\n";
  }

  public function green_bar($message)
  {
    echo "$message\n";
  }

  public function red_bar($message)
  {
    echo "$message\n";
  }
}

class lime_output_color extends lime_output
{
  public $colorizer = null;

  public function __construct()
  {
    $this->colorizer = new lime_colorizer();
  }

  public function diag()
  {
    $messages = func_get_args();
    foreach ($messages as $message)
    {
      echo $this->colorizer->colorize('# '.join("\n# ", (array) $message), 'COMMENT')."\n";
    }
  }

  public function comment($message)
  {
    echo $this->colorizer->colorize(sprintf('# %s', $message), 'COMMENT')."\n";
  }

  public function info($message)
  {
    echo $this->colorizer->colorize(sprintf('> %s', $message), 'INFO_BAR')."\n";
  }

  public function error($message)
  {
    echo $this->colorizer->colorize(sprintf(' %s ', $message), 'RED_BAR')."\n";
  }

  public function echoln($message, $colorizer_parameter = null)
  {
    $message = preg_replace('/(?:^|\.)((?:not ok|dubious) *\d*)\b/e', '$this->colorizer->colorize(\'$1\', \'ERROR\')', $message);
    $message = preg_replace('/(?:^|\.)(ok *\d*)\b/e', '$this->colorizer->colorize(\'$1\', \'INFO\')', $message);
    $message = preg_replace('/"(.+?)"/e', '$this->colorizer->colorize(\'$1\', \'PARAMETER\')', $message);
    $message = preg_replace('/(\->|\:\:)?([a-zA-Z0-9_]+?)\(\)/e', '$this->colorizer->colorize(\'$1$2()\', \'PARAMETER\')', $message);

    echo ($colorizer_parameter ? $this->colorizer->colorize($message, $colorizer_parameter) : $message)."\n";
  }

  public function green_bar($message)
  {
    echo $this->colorizer->colorize($message.str_repeat(' ', 71 - min(71, strlen($message))), 'GREEN_BAR')."\n";
  }

  public function red_bar($message)
  {
    echo $this->colorizer->colorize($message.str_repeat(' ', 71 - min(71, strlen($message))), 'RED_BAR')."\n";
  }
}

class lime_colorizer
{
  static public $styles = array();

  public static function style($name, $options = array())
  {
    self::$styles[$name] = $options;
  }

  public static function colorize($text = '', $parameters = array())
  {
    // disable colors if not supported (windows or non tty console)
    if (DIRECTORY_SEPARATOR == '\\' || !function_exists('posix_isatty') || !@posix_isatty(STDOUT))
    {
      return $text;
    }

    static $options    = array('bold' => 1, 'underscore' => 4, 'blink' => 5, 'reverse' => 7, 'conceal' => 8);
    static $foreground = array('black' => 30, 'red' => 31, 'green' => 32, 'yellow' => 33, 'blue' => 34, 'magenta' => 35, 'cyan' => 36, 'white' => 37);
    static $background = array('black' => 40, 'red' => 41, 'green' => 42, 'yellow' => 43, 'blue' => 44, 'magenta' => 45, 'cyan' => 46, 'white' => 47);

    !is_array($parameters) && isset(self::$styles[$parameters]) and $parameters = self::$styles[$parameters];

    $codes = array();
    isset($parameters['fg']) and $codes[] = $foreground[$parameters['fg']];
    isset($parameters['bg']) and $codes[] = $background[$parameters['bg']];
    foreach ($options as $option => $value)
    {
      isset($parameters[$option]) && $parameters[$option] and $codes[] = $value;
    }

    return "\033[".implode(';', $codes).'m'.$text."\033[0m";
  }
}

lime_colorizer::style('ERROR', array('bg' => 'red', 'fg' => 'white', 'bold' => true));
lime_colorizer::style('INFO',  array('fg' => 'green', 'bold' => true));
lime_colorizer::style('PARAMETER', array('fg' => 'cyan'));
lime_colorizer::style('COMMENT',  array('fg' => 'yellow'));

lime_colorizer::style('GREEN_BAR',  array('fg' => 'white', 'bg' => 'green', 'bold' => true));
lime_colorizer::style('RED_BAR',  array('fg' => 'white', 'bg' => 'red', 'bold' => true));
lime_colorizer::style('INFO_BAR',  array('fg' => 'cyan', 'bold' => true));

class lime_harness extends lime_registration
{
  public $php_cli = '';
  public $stats = array();
  public $output = null;

  public function __construct($output_instance, $php_cli = null)
  {
    if (is_null($php_cli))
    {
      if (getenv('PHP_PATH'))
      {
        $this->php_cli = getenv('PHP_PATH');

        if (!is_executable($this->php_cli))
        {
          throw new Exception('The defined PHP_PATH environment variable is not a valid PHP executable.');
        }
      }
      else
      {
        $this->php_cli = PHP_BINDIR.DIRECTORY_SEPARATOR.'php';
      }
    }
    else
    {
      $this->php_cli = $php_cli;
    }

    if (!is_executable($this->php_cli))
    {
      $this->php_cli = $this->find_php_cli();
    }

    $this->output = $output_instance ? $output_instance : new lime_output();
  }

  protected function find_php_cli()
  {
    $path = getenv('PATH') ? getenv('PATH') : getenv('Path');
    $exe_suffixes = DIRECTORY_SEPARATOR == '\\' ? (getenv('PATHEXT') ? explode(PATH_SEPARATOR, getenv('PATHEXT')) : array('.exe', '.bat', '.cmd', '.com')) : array('');
    foreach (array('php5', 'php') as $php_cli)
    {
      foreach ($exe_suffixes as $suffix)
      {
        foreach (explode(PATH_SEPARATOR, $path) as $dir)
        {
          $file = $dir.DIRECTORY_SEPARATOR.$php_cli.$suffix;
          if (is_executable($file))
          {
            return $file;
          }
        }
      }
    }

    throw new Exception("Unable to find PHP executable.");
  }

  public function run()
  {
    if (!count($this->files))
    {
      throw new Exception('You must register some test files before running them!');
    }

    // sort the files to be able to predict the order
    sort($this->files);

    $this->stats =array(
      '_failed_files' => array(),
      '_failed_tests' => 0,
      '_nb_tests'     => 0,
    );

    foreach ($this->files as $file)
    {
      $this->stats[$file] = array(
        'plan'     =>   null,
        'nb_tests' => 0,
        'failed'   => array(),
        'passed'   => array(),
      );
      $this->current_file = $file;
      $this->current_test = 0;
      $relative_file = $this->get_relative_file($file);

      ob_start(array($this, 'process_test_output'));
      // see http://trac.symfony-project.org/ticket/5437 for the explanation on the weird "cd" thing
      passthru(sprintf('cd & "%s" "%s" 2>&1', $this->php_cli, $file), $return);
      ob_end_clean();

      if ($return > 0)
      {
        $this->stats[$file]['status'] = 'dubious';
        $this->stats[$file]['status_code'] = $return;
      }
      else
      {
        $delta = $this->stats[$file]['plan'] - $this->stats[$file]['nb_tests'];
        if ($delta > 0)
        {
          $this->output->echoln(sprintf('%s%s%s', substr($relative_file, -min(67, strlen($relative_file))), str_repeat('.', 70 - min(67, strlen($relative_file))), $this->output->colorizer->colorize(sprintf('# Looks like you planned %d tests but only ran %d.', $this->stats[$file]['plan'], $this->stats[$file]['nb_tests']), 'COMMENT')));
          $this->stats[$file]['status'] = 'dubious';
          $this->stats[$file]['status_code'] = 255;
          $this->stats['_nb_tests'] += $delta;
          for ($i = 1; $i <= $delta; $i++)
          {
            $this->stats[$file]['failed'][] = $this->stats[$file]['nb_tests'] + $i;
          }
        }
        else if ($delta < 0)
        {
          $this->output->echoln(sprintf('%s%s%s', substr($relative_file, -min(67, strlen($relative_file))), str_repeat('.', 70 - min(67, strlen($relative_file))), $this->output->colorizer->colorize(sprintf('# Looks like you planned %s test but ran %s extra.', $this->stats[$file]['plan'], $this->stats[$file]['nb_tests'] - $this->stats[$file]['plan']), 'COMMENT')));
          $this->stats[$file]['status'] = 'dubious';
          $this->stats[$file]['status_code'] = 255;
          for ($i = 1; $i <= -$delta; $i++)
          {
            $this->stats[$file]['failed'][] = $this->stats[$file]['plan'] + $i;
          }
        }
        else
        {
          $this->stats[$file]['status_code'] = 0;
          $this->stats[$file]['status'] = $this->stats[$file]['failed'] ? 'not ok' : 'ok';
        }
      }

      $this->output->echoln(sprintf('%s%s%s', substr($relative_file, -min(67, strlen($relative_file))), str_repeat('.', 70 - min(67, strlen($relative_file))), $this->stats[$file]['status']));
      if (($nb = count($this->stats[$file]['failed'])) || $return > 0)
      {
        if ($nb)
        {
          $this->output->echoln(sprintf("    Failed tests: %s", implode(', ', $this->stats[$file]['failed'])));
        }
        $this->stats['_failed_files'][] = $file;
        $this->stats['_failed_tests']  += $nb;
      }

      if ('dubious' == $this->stats[$file]['status'])
      {
        $this->output->echoln(sprintf('    Test returned status %s', $this->stats[$file]['status_code']));
      }
    }

    if (count($this->stats['_failed_files']))
    {
      $format = "%-30s  %4s  %5s  %5s  %s";
      $this->output->echoln(sprintf($format, 'Failed Test', 'Stat', 'Total', 'Fail', 'List of Failed'));
      $this->output->echoln("------------------------------------------------------------------");
      foreach ($this->stats as $file => $file_stat)
      {
        if (!in_array($file, $this->stats['_failed_files'])) continue;

        $relative_file = $this->get_relative_file($file);
        $this->output->echoln(sprintf($format, substr($relative_file, -min(30, strlen($relative_file))), $file_stat['status_code'], count($file_stat['failed']) + count($file_stat['passed']), count($file_stat['failed']), implode(' ', $file_stat['failed'])));
      }

      $this->output->red_bar(sprintf('Failed %d/%d test scripts, %.2f%% okay. %d/%d subtests failed, %.2f%% okay.',
        $nb_failed_files = count($this->stats['_failed_files']),
        $nb_files = count($this->files),
        ($nb_files - $nb_failed_files) * 100 / $nb_files,
        $nb_failed_tests = $this->stats['_failed_tests'],
        $nb_tests = $this->stats['_nb_tests'],
        $nb_tests > 0 ? ($nb_tests - $nb_failed_tests) * 100 / $nb_tests : 0
      ));
    }
    else
    {
      $this->output->green_bar(' All tests successful.');
      $this->output->green_bar(sprintf(' Files=%d, Tests=%d', count($this->files), $this->stats['_nb_tests']));
    }

    return $this->stats['_failed_files'] ? false : true;
  }

  private function process_test_output($lines)
  {
    foreach (explode("\n", $lines) as $text)
    {
      if (false !== strpos($text, 'not ok '))
      {
        ++$this->current_test;
        $test_number = (int) substr($text, 7);
        $this->stats[$this->current_file]['failed'][] = $test_number;

        ++$this->stats[$this->current_file]['nb_tests'];
        ++$this->stats['_nb_tests'];
      }
      else if (false !== strpos($text, 'ok '))
      {
        ++$this->stats[$this->current_file]['nb_tests'];
        ++$this->stats['_nb_tests'];
      }
      else if (preg_match('/^1\.\.(\d+)/', $text, $match))
      {
        $this->stats[$this->current_file]['plan'] = $match[1];
      }
    }

    return;
  }
}

class lime_coverage extends lime_registration
{
  public $files = array();
  public $extension = '.php';
  public $base_dir = '';
  public $harness = null;
  public $verbose = false;
  protected $coverage = array();

  public function __construct($harness)
  {
    $this->harness = $harness;

    if (!function_exists('xdebug_start_code_coverage'))
    {
      throw new Exception('You must install and enable xdebug before using lime coverage.');
    }

    if (!ini_get('xdebug.extended_info'))
    {
      throw new Exception('You must set xdebug.extended_info to 1 in your php.ini to use lime coverage.');
    }
  }

  public function run()
  {
    if (!count($this->harness->files))
    {
      throw new Exception('You must register some test files before running coverage!');
    }

    if (!count($this->files))
    {
      throw new Exception('You must register some files to cover!');
    }

    $this->coverage = array();

    $this->process($this->harness->files);

    $this->output($this->files);
  }

  public function process($files)
  {
    if (!is_array($files))
    {
      $files = array($files);
    }

    $tmp_file = lime_test::get_temp_directory().DIRECTORY_SEPARATOR.'test.php';
    foreach ($files as $file)
    {
      $tmp = <<<EOF
<?php
xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE);
include('$file');
echo '<PHP_SER>'.serialize(xdebug_get_code_coverage()).'</PHP_SER>';
EOF;
      file_put_contents($tmp_file, $tmp);
      ob_start();
      // see http://trac.symfony-project.org/ticket/5437 for the explanation on the weird "cd" thing
      passthru(sprintf('cd & "%s" "%s" 2>&1', $this->harness->php_cli, $tmp_file), $return);
      $retval = ob_get_clean();

      if (0 != $return) // test exited without success
      {
        // something may have gone wrong, we should warn the user so they know
        // it's a bug in their code and not symfony's

        $this->harness->output->echoln(sprintf('Warning: %s returned status %d, results may be inaccurate', $file, $return), 'ERROR');
      }

      if (false === $cov = unserialize(substr($retval, strpos($retval, '<PHP_SER>') + 9, strpos($retval, '</PHP_SER>') - 9)))
      {
        if (0 == $return)
        {
          // failed to serialize, but PHP said it should of worked.
          // something is seriously wrong, so abort with exception
          throw new Exception(sprintf('Unable to unserialize coverage for file "%s"', $file));
        }
        else
        {
          // failed to serialize, but PHP warned us that this might have happened.
          // so we should ignore and move on
          continue; // continue foreach loop through $this->harness->files
        }
      }

      foreach ($cov as $file => $lines)
      {
        if (!isset($this->coverage[$file]))
        {
          $this->coverage[$file] = $lines;
          continue;
        }

        foreach ($lines as $line => $flag)
        {
          if ($flag == 1)
          {
            $this->coverage[$file][$line] = 1;
          }
        }
      }
    }

    if (file_exists($tmp_file))
    {
      unlink($tmp_file);
    }
  }

  public function output($files)
  {
    ksort($this->coverage);
    $total_php_lines = 0;
    $total_covered_lines = 0;
    foreach ($files as $file)
    {
      $file = realpath($file);
      $is_covered = isset($this->coverage[$file]);
      $cov = isset($this->coverage[$file]) ? $this->coverage[$file] : array();
      $covered_lines = array();
      $missing_lines = array();

      foreach ($cov as $line => $flag)
      {
        switch ($flag)
        {
          case 1:
            $covered_lines[] = $line;
            break;
          case -1:
            $missing_lines[] = $line;
            break;
        }
      }
      $total_lines = count($covered_lines) + count($missing_lines);

      $output = $this->harness->output;
      $percent = $total_lines ? count($covered_lines) * 100 / $total_lines : 0;

      $total_php_lines += $total_lines;
      $total_covered_lines += count($covered_lines);

      $relative_file = $this->get_relative_file($file);
      $output->echoln(sprintf("%-70s %3.0f%%", substr($relative_file, -min(70, strlen($relative_file))), $percent), $percent == 100 ? 'INFO' : ($percent > 90 ? 'PARAMETER' : ($percent < 20 ? 'ERROR' : '')));
      if ($this->verbose && $is_covered && $percent != 100)
      {
        $output->comment(sprintf("missing: %s", $this->format_range($missing_lines)));
      }
    }

    $output->echoln(sprintf("TOTAL COVERAGE: %3.0f%%", $total_php_lines ? $total_covered_lines * 100 / $total_php_lines : 0));
  }

  public static function get_php_lines($content)
  {
    if (is_readable($content))
    {
      $content = file_get_contents($content);
    }

    $tokens = token_get_all($content);
    $php_lines = array();
    $current_line = 1;
    $in_class = false;
    $in_function = false;
    $in_function_declaration = false;
    $end_of_current_expr = true;
    $open_braces = 0;
    foreach ($tokens as $token)
    {
      if (is_string($token))
      {
        switch ($token)
        {
          case '=':
            if (false === $in_class || (false !== $in_function && !$in_function_declaration))
            {
              $php_lines[$current_line] = true;
            }
            break;
          case '{':
            ++$open_braces;
            $in_function_declaration = false;
            break;
          case ';':
            $in_function_declaration = false;
            $end_of_current_expr = true;
            break;
          case '}':
            $end_of_current_expr = true;
            --$open_braces;
            if ($open_braces == $in_class)
            {
              $in_class = false;
            }
            if ($open_braces == $in_function)
            {
              $in_function = false;
            }
            break;
        }

        continue;
      }

      list($id, $text) = $token;

      switch ($id)
      {
        case T_CURLY_OPEN:
        case T_DOLLAR_OPEN_CURLY_BRACES:
          ++$open_braces;
          break;
        case T_WHITESPACE:
        case T_OPEN_TAG:
        case T_CLOSE_TAG:
          $end_of_current_expr = true;
          $current_line += count(explode("\n", $text)) - 1;
          break;
        case T_COMMENT:
        case T_DOC_COMMENT:
          $current_line += count(explode("\n", $text)) - 1;
          break;
        case T_CLASS:
          $in_class = $open_braces;
          break;
        case T_FUNCTION:
          $in_function = $open_braces;
          $in_function_declaration = true;
          break;
        case T_AND_EQUAL:
        case T_BREAK:
        case T_CASE:
        case T_CATCH:
        case T_CLONE:
        case T_CONCAT_EQUAL:
        case T_CONTINUE:
        case T_DEC:
        case T_DECLARE:
        case T_DEFAULT:
        case T_DIV_EQUAL:
        case T_DO:
        case T_ECHO:
        case T_ELSEIF:
        case T_EMPTY:
        case T_ENDDECLARE:
        case T_ENDFOR:
        case T_ENDFOREACH:
        case T_ENDIF:
        case T_ENDSWITCH:
        case T_ENDWHILE:
        case T_EVAL:
        case T_EXIT:
        case T_FOR:
        case T_FOREACH:
        case T_GLOBAL:
        case T_IF:
        case T_INC:
        case T_INCLUDE:
        case T_INCLUDE_ONCE:
        case T_INSTANCEOF:
        case T_ISSET:
        case T_IS_EQUAL:
        case T_IS_GREATER_OR_EQUAL:
        case T_IS_IDENTICAL:
        case T_IS_NOT_EQUAL:
        case T_IS_NOT_IDENTICAL:
        case T_IS_SMALLER_OR_EQUAL:
        case T_LIST:
        case T_LOGICAL_AND:
        case T_LOGICAL_OR:
        case T_LOGICAL_XOR:
        case T_MINUS_EQUAL:
        case T_MOD_EQUAL:
        case T_MUL_EQUAL:
        case T_NEW:
        case T_OBJECT_OPERATOR:
        case T_OR_EQUAL:
        case T_PLUS_EQUAL:
        case T_PRINT:
        case T_REQUIRE:
        case T_REQUIRE_ONCE:
        case T_RETURN:
        case T_SL:
        case T_SL_EQUAL:
        case T_SR:
        case T_SR_EQUAL:
        case T_SWITCH:
        case T_THROW:
        case T_TRY:
        case T_UNSET:
        case T_UNSET_CAST:
        case T_USE:
        case T_WHILE:
        case T_XOR_EQUAL:
          $php_lines[$current_line] = true;
          $end_of_current_expr = false;
          break;
        default:
          if (false === $end_of_current_expr)
          {
            $php_lines[$current_line] = true;
          }
      }
    }

    return $php_lines;
  }

  public function compute($content, $cov)
  {
    $php_lines = self::get_php_lines($content);

    // we remove from $cov non php lines
    foreach (array_diff_key($cov, $php_lines) as $line => $tmp)
    {
      unset($cov[$line]);
    }

    return array($cov, $php_lines);
  }

  public function format_range($lines)
  {
    sort($lines);
    $formatted = '';
    $first = -1;
    $last = -1;
    foreach ($lines as $line)
    {
      if ($last + 1 != $line)
      {
        if ($first != -1)
        {
          $formatted .= $first == $last ? "$first " : "[$first - $last] ";
        }
        $first = $line;
        $last = $line;
      }
      else
      {
        $last = $line;
      }
    }
    if ($first != -1)
    {
      $formatted .= $first == $last ? "$first " : "[$first - $last] ";
    }

    return $formatted;
  }
}

class lime_registration
{
  public $files = array();
  public $extension = '.php';
  public $base_dir = '';

  public function register($files_or_directories)
  {
    foreach ((array) $files_or_directories as $f_or_d)
    {
      if (is_file($f_or_d))
      {
        $this->files[] = realpath($f_or_d);
      }
      elseif (is_dir($f_or_d))
      {
        $this->register_dir($f_or_d);
      }
      else
      {
        throw new Exception(sprintf('The file or directory "%s" does not exist.', $f_or_d));
      }
    }
  }

  public function register_glob($glob)
  {
    if ($dirs = glob($glob))
    {
      foreach ($dirs as $file)
      {
        $this->files[] = realpath($file);
      }
    }
  }

  public function register_dir($directory)
  {
    if (!is_dir($directory))
    {
      throw new Exception(sprintf('The directory "%s" does not exist.', $directory));
    }

    $files = array();

    $current_dir = opendir($directory);
    while ($entry = readdir($current_dir))
    {
      if ($entry == '.' || $entry == '..') continue;

      if (is_dir($entry))
      {
        $this->register_dir($entry);
      }
      elseif (preg_match('#'.$this->extension.'$#', $entry))
      {
        $files[] = realpath($directory.DIRECTORY_SEPARATOR.$entry);
      }
    }

    $this->files = array_merge($this->files, $files);
  }

  protected function get_relative_file($file)
  {
    return str_replace(DIRECTORY_SEPARATOR, '/', str_replace(array(realpath($this->base_dir).DIRECTORY_SEPARATOR, $this->extension), '', $file));
  }
}
