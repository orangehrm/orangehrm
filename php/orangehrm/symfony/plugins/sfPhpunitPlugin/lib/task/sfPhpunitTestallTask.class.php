<?php

/*
* This file is part of the symfony package.
* (c) Fabien Potencier <fabien.potencier@symfony-project.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * Promote a user as a super administrator.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardCreateAdminTask.class.php 10127 2008-07-04 21:02:40Z fabien $
 */
class sfPhpunitTestallTask extends sfBaseTask
{
    /**
   * @see sfTask
   */
    protected function configure()
    {
        $this->namespace = 'phpunit';
        $this->name = 'testall';
        $this->briefDescription = 'Runs PHPUnit AllTests';

        $this->detailedDescription = <<<EOF
The [phpunit:testall] task Runs PHPUnit AllTests
EOF;
}

    /**
     * @see sfTask
     */
    protected function execute($arguments = array(), $options = array())
    {
        chdir( 'test' );
        passthru( 'phpunit AllTests.php');
    }

}
