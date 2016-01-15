<?php
/**
 * Input Class Page
 */

/**
 * Input Class
 */
class Input extends Option
{
	/**
	 * Displays HTML version of the option.
	 *
	 * @param boolean $show_value Display escaped current value of option.
	 */
	public function html($show_value = null)
	{
		$label = "";
		$label_class = "";
		$input_class = "";
		$type = "text";
		$placeholder = "";
		$autofocus = "";
		$required = "";
		$title = "";
		$inputname = "";
		if (isset($this->autofocus))
			$autofocus = " autofocus";
		if (isset($this->required))
			$required = " required";
		if (isset($this->type))
			$type = $this->type;
		if (isset($this->placeholder)) {
			$placeholder = ' placeholder="'.$this->placeholder.'"';
			$title = ' title="'.$this->placeholder.'"';
		}
		if (isset($this->label_class))
			$label_class = ' class="'.$this->label_class.'"';
		if (isset($this->input_class))
			$input_class = ' class="'.$this->input_class.'"';
		if ($show_value == true && (!isset($this->type) || ($this->type != "password" && $this->type != "checkbox"))) {

			if (!isset($this->value) && isset($_POST[$this->id])) {
				$value = $_POST[$this->id];
			} elseif (isset($this->value)) {
				$value = $this->value;
			} else {
				$value = $this->get();
			}
			$show_value = ' value="' . filter_var($value, FILTER_SANITIZE_STRING) . '"';
		}
		if (isset($this->type) && $this->type == "checkbox") {
			if (isset($this->value) && $this->value == "checked")
				$show_value = ' checked';

		}
		if (isset($this->label_id))
			$label = '<label id="'.$this->label_id.'" for="'.$this->id.'"'.$label_class.$title.'>'.$this->name.'</label>';
		if (isset($this->inputname))
			$inputname = $this->inputname;
		else
			$inputname = $this->id;
		$input = '<input type="'.$type.'" name="' . $inputname . '" id="' . $this->id . '"' . $show_value . $input_class . $placeholder. $autofocus . $required.' />';

		if ($type == "checkbox" || $type == "radio")
			return $input.$label;
		else
			return $label.$input;
	}

	/**
	 * Logic for filtering and saving option
	 *
	 * @param string $value The text value
	 *
	 * @return mixed if true returns the value.
	 */
	function save($value)
	{
		if ($clean = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS)) {
			return $this->update($clean);
		} else {
			return false;
		}
	}
}
