<?php
class ohrmWidgetCheckbox extends sfWidget
{
	protected $name;
	protected $value;
	protected $label;
	protected $attributes;

	public function __construct($name, $value, $label, $attrubutes = null) {
		if (empty($attrubutes)) {
			$attrubutes = array();
		}

		$this->name = $name;
		$this->value = $value;
		$this->label = $label;
		$this->attributes = $attrubutes;
	}

	/**
	 * @return string An HTML tag string
	 */
	public function render($name, $value = null, $attributes = array(), $errors = array())
	{
		if (empty($name)) {
			$name = $this->name;
		}

		if (empty($value)) {
			$value = $this->value;
		}

		if (empty($attributes)) {
			$attributes = $this->attributes;
		}

		if (!array_key_exists('id', $attributes)) {
			$attributes['id'] = "{$name}_{$value}";
		}

		$content = '';
		if (array_key_exists('content', $this->label)) {
			$content = $this->label['content'];
			unset($this->label['content']);
		}

		$html = $this->renderContentTag('label', __($content), array_merge(array('for' => $attributes['id']), $this->label));
		$html .= $this->renderTag('input', array_merge(array('type' => 'checkbox', 'name' => "{$name}[]", 'value' => __($value)), $attributes));

		return $html;
	}

	public function getName() {
		return $this->name;
	}
}