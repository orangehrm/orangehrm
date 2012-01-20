<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Creates a task skeleton
 *
 * @package    symfony
 * @subpackage task
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfGenerateTaskTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('task_name', sfCommandArgument::REQUIRED, 'The task name (can contain namespace)'),
    ));

    $this->addOptions(array(
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED, 'The directory to create the task in', 'lib/task'),
      new sfCommandOption('use-database', null, sfCommandOption::PARAMETER_REQUIRED, 'Whether the task needs model initialization to access database', sfConfig::get('sf_orm')),
      new sfCommandOption('brief-description', null, sfCommandOption::PARAMETER_REQUIRED, 'A brief task description (appears in task list)'),
    ));

    $this->namespace = 'generate';
    $this->name = 'task';
    $this->briefDescription = 'Creates a skeleton class for a new task';

    $this->detailedDescription = <<<EOF
The [generate:task|INFO] creates a new sfTask class based on the name passed as
argument:

  [./symfony generate:task namespace:name|INFO]

The [namespaceNameTask.class.php|COMMENT] skeleton task is created under the [lib/task/|COMMENT]
directory. Note that the namespace is optional.

If you want to create the file in another directory (relative to the project
root folder), pass it in the [--dir|COMMENT] option. This directory will be created
if it does not already exist.

  [./symfony generate:task namespace:name --dir=plugins/myPlugin/lib/task|INFO]

If you want the task to default to a connection other than [doctrine|COMMENT], provide
the name of this connection with the [--use-database|COMMENT] option:

  [./symfony generate:task namespace:name --use-database=main|INFO]

The [--use-database|COMMENT] option can also be used to disable database
initialization in the generated task:

  [./symfony generate:task namespace:name --use-database=false|INFO]

You can also specify a description:

  [./symfony generate:task namespace:name --brief-description="Does interesting things"|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $taskName = $arguments['task_name'];
    $taskNameComponents = explode(':', $taskName);
    $namespace = isset($taskNameComponents[1]) ? $taskNameComponents[0] : '';
    $name = isset($taskNameComponents[1]) ? $taskNameComponents[1] : $taskNameComponents[0];
    $taskClassName = str_replace('-', '', ($namespace ? $namespace.ucfirst($name) : $name)).'Task';

    // Validate the class name
    if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $taskClassName))
    {
      throw new sfCommandException(sprintf('The task class name "%s" is invalid.', $taskClassName));
    }

    $briefDescription = $options['brief-description'];
    $detailedDescription = <<<HED
The [$taskName|INFO] task does things.
Call it with:

  [php symfony $taskName|INFO]
HED;

    $useDatabase = sfToolkit::literalize($options['use-database']);
    $defaultConnection = is_string($useDatabase) ? $useDatabase : sfConfig::get('sf_orm');

    if ($useDatabase)
    {
      $content = <<<HED
<?php

class $taskClassName extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // \$this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    \$this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', '$defaultConnection'),
      // add your own options here
    ));

    \$this->namespace        = '$namespace';
    \$this->name             = '$name';
    \$this->briefDescription = '$briefDescription';
    \$this->detailedDescription = <<<EOF
$detailedDescription
EOF;
  }

  protected function execute(\$arguments = array(), \$options = array())
  {
    // initialize the database connection
    \$databaseManager = new sfDatabaseManager(\$this->configuration);
    \$connection = \$databaseManager->getDatabase(\$options['connection'])->getConnection();

    // add your code here
  }
}

HED;
    }
    else
    {
      $content = <<<HED
<?php

class $taskClassName extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // \$this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    // // add your own options here
    // \$this->addOptions(array(
    //   new sfCommandOption('my_option', null, sfCommandOption::PARAMETER_REQUIRED, 'My option'),
    // ));

    \$this->namespace        = '$namespace';
    \$this->name             = '$name';
    \$this->briefDescription = '$briefDescription';
    \$this->detailedDescription = <<<EOF
$detailedDescription
EOF;
  }

  protected function execute(\$arguments = array(), \$options = array())
  {
    // add your code here
  }
}

HED;
    }

    // check that the task directory exists and that the task file doesn't exist
    if (!is_readable(sfConfig::get('sf_root_dir').'/'.$options['dir']))
    {
      $this->getFilesystem()->mkdirs($options['dir']);
    }

    $taskFile = sfConfig::get('sf_root_dir').'/'.$options['dir'].'/'.$taskClassName.'.class.php';
    if (is_readable($taskFile))
    {
      throw new sfCommandException(sprintf('A "%s" task already exists in "%s".', $taskName, $taskFile));
    }

    $this->logSection('task', sprintf('Creating "%s" task file', $taskFile));
    file_put_contents($taskFile, $content);
  }
}
