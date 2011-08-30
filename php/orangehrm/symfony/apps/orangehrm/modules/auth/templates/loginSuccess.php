<h2>OrangeHRM Login</h2>
<?php
$formFields = $form->renderUsing('BreakTags');
$formFields .= tag('input', array(
    'type' => 'submit',
    'value' => 'Login',
    'class' => 'button',
));

echo content_tag('form', $formFields, array(
    'method' => 'post',
    'action' => url_for('auth/login'),
));

?>
