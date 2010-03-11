<?php

/**
 * Movie form.
 *
 * @package    form
 * @subpackage movie
 * @version    SVN: $Id: MovieForm.class.php 12854 2008-11-09 20:08:32Z fabien $
 */
class MovieForm extends BaseMovieForm
{
  public function configure()
  {
    $this->embedI18n(array('en', 'fr'));
  }
}
