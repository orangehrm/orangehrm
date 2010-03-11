<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFormLanguage is a form to change the symfony user culture.
 *
 * Usage:
 *
 * class mainActions extends sfActions
 * {
 *   public function executeChangeLanguage($request)
 *   {
 *     $this->form = new sfFormLanguage($this->getUser(), array('languages' => array('en', 'fr')));
 *     if ($this->form->process($request))
 *     {
 *       // culture has changed
 *       return $this->redirect('@homepage');
 *     }
 *
 *     // the form is not valid (can't happen... but you never know)
 *     return $this->redirect('@homepage');
 *   }
 * }
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFormLanguage.class.php 7277 2008-02-02 21:19:47Z fabien $
 */
class sfFormLanguage extends sfForm
{
  protected
    $user = null;

  /**
   * Constructor.
   *
   * @param sfUser A sfUser instance
   * @param array  An array of options
   * @param string A CSRF secret (false to disable CSRF protection, null to use the global CSRF secret)
   *
   * @see sfForm
   */
  public function __construct(sfUser $user, $options = array(), $CSRFSecret = null)
  {
    $this->user = $user;

    if (!isset($options['languages']))
    {
      throw new RuntimeException(sprintf('%s requires a "languages" option.', get_class($this)));
    }

    parent::__construct(array('language' => $user->getCulture()), $options, $CSRFSecret);
  }

  /**
   * Changes the current user culture.
   */
  public function save()
  {
    $this->user->setCulture($this->getValue('language'));
  }

  /**
   * Processes the current request.
   *
   * @param  sfRequest A sfRequest instance
   *
   * @return Boolean   true if the form is valid, false otherwise
   */
  public function process(sfRequest $request)
  {
    $data = array('language' => $request->getParameter('language'));
    if ($request->hasParameter(self::$CSRFFieldName))
    {
      $data[self::$CSRFFieldName] = $request->getParameter(self::$CSRFFieldName);
    }

    $this->bind($data);

    if ($isValid = $this->isValid())
    {
      $this->save();
    }

    return $isValid;
  }

  /**
   * @see sfForm
   */
  public function configure()
  {
    $this->setValidators(array(
      'language' => new sfValidatorI18nChoiceLanguage(array('culture' => 'en', 'languages' => $this->options['languages'])),
    ));

    $this->setWidgets(array(
      'language' => new sfWidgetFormI18nSelectLanguage(array('culture' => 'en', 'languages' => $this->options['languages'])),
    ));
  }
}
