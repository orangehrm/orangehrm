/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

const OXD_INPUT_ELEMENTS = {
  input: '.oxd-input',
  radio: '.oxd-radio-input',
  switch: '.oxd-switch-input',
  checkbox: '.oxd-checkbox-input',
  time: '.oxd-time-input',
  file: '.oxd-file-input',
  textbox: '.oxd-textarea',
  select: '.oxd-select-text-input',
  multiselect: '.oxd-select-text-input',
  autocomplete: '.oxd-autocomplete-text-input',
};

const OXD_TOASTS = {
  default: '.oxd-toast--default',
  success: '.oxd-toast--success',
  warn: '.oxd-toast--warn',
  error: '.oxd-toast--error',
  info: '.oxd-toast--info',
};

const OXD_ELEMENTS = {
  form: '.oxd-form',
  toast: '.oxd-toast',
  button: '.oxd-button',
  inputGroup: '.oxd-input-group',
  pageContext: '.oxd-layout-context',
  pageTitle: '.orangehrm-main-title',
  subTitle: '.oxd-text--subtitle-2',
  numRecords: '.oxd-text--span',
  option: '.oxd-select-option',
  option2: '.oxd-autocomplete-option > span',
  selectWrapper: '.oxd-select-wrapper',
  date: '.oxd-date-input',
  leaveBalance: 'orangehrm-leave-balance-text',
  ...OXD_INPUT_ELEMENTS,
};

export {OXD_INPUT_ELEMENTS, OXD_ELEMENTS, OXD_TOASTS};
