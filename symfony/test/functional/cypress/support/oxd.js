const OXD_INPUT_ELEMENTS = {
  input: '.oxd-input',
  radio: '.oxd-radio-input',
  switch: '.oxd-switch-input',
  checkbox: '.oxd-checkbox-input',
  file: '.oxd-file-input',
  date: '.oxd-date-input',
  time: '.oxd-time-input',
  textbox: '.oxd-textarea',
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
  ...OXD_INPUT_ELEMENTS,
};

export {OXD_INPUT_ELEMENTS, OXD_ELEMENTS, OXD_TOASTS};
