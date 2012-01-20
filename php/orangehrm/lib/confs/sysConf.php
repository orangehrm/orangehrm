<?php
/*
// OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
// all the essential functionalities required for any enterprise.
// Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

// OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
// the GNU General Public License as published by the Free Software Foundation; either
// version 2 of the License, or (at your option) any later version.

// OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
// See the GNU General Public License for more details.

// You should have received a copy of the GNU General Public License along with this program;
// if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
// Boston, MA  02110-1301, USA
*/

class sysConf {

  var $itemsPerPage;

  /** $accessDenied is depreciated and no longer in use
         *  Please use the language files to change the access denied message.
         */
  var $accessDenied;
  var $viewDescLen;
  var $userEmail;
  var $maxEmployees;
  var $dateFormat;
  var $timeFormat;

  var $dateInputHint;
  var $timeInputHint;
  public $javascriptInputHint = "YYYY-MM-DD";
  var $styleSheet;

  /**
   * Following variable decides if admin users can edit the sendmail path through a web browser.
   * If set to false, the mailConf.php file has to be edited manually to set sendmail path.
   *
   * WARNING: Setting to true is not secure.
   */
  protected $allowSendmailPathEdit = false; // Set to true to edit sendmail path through a browser

  /**
   * Following variable limits sendmail path edit to a browser running on the same computer as OrangeHRM.
   * Set to false to allow editing from anywhere.
   *
   * WARNING: Setting to false is not secure.
   */
  protected $sendmailPathEditOnlyFromLocalHost = true; // Set to edit sendmail path from

  function sysConf() {

    $this->itemsPerPage=50;

    /* $accessDenied is depreciated and no longer in use
     *  Please use the language files to change the access denied message.
     */
    $this->accessDenied="Access Denied";

    $this->viewDescLen=60;
    $this->userEmail = 'youremail@mailhost.com';
    $this->maxEmployees = '4999';
    $this->dateFormat = "Y-m-d";
    $this->dateInputHint = "YYYY-mm-DD";
    $this->timeFormat = "H:i";
    $this->timeInputHint = "HH:MM";
    $this->styleSheet = "orange";
  }

  function getEmployeeIdLength() {
    return strlen($this->maxEmployees);
  }

  function getDateFormat() {
    return $this->dateFormat;
  }

  function getTimeFormat() {
    return $this->timeFormat;
  }

  function getDateInputHint() {
    return $this->dateInputHint;
  }

  function getTimeInputHint() {
    return $this->timeInputHint;
  }

  function getStyleSheet() {
    return $this->styleSheet;
  }

  public function allowSendmailPathEdit() {
      return $this->allowSendmailPathEdit;
  }

  public function sendmailPathEditOnlyFromLocalHost() {
    return $this->sendmailPathEditOnlyFromLocalHost;
  }
}

?>
