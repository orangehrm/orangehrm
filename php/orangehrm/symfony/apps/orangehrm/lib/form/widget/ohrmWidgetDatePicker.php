<?php
class ohrmWidgetDatePicker extends sfWidgetFormInput {

	public function render($name, $value = null, $attributes = array(), $errors = array()) {
		
		if (array_key_exists('class', $attributes)) {
			$attributes['class'] .= ' ohrm_datepicker';
		} else {
			$attributes['class'] = 'ohrm_datepicker';
		}
		
		$html = parent::render($name, $value, $attributes, $errors);
		$html .= $this->renderTag('input', array(
			'type' => 'button',
			'id' => "{$this->attributes['id']}_Button",
			'class' => 'calendarBtn',
			'style' => 'float: none; display: inline; margin-left: 16px;',
			'value' => '',
		));
		
		return $html;
		
	}
	
}