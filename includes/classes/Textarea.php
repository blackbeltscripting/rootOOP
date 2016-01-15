<?php
/**
 * Textarea Class Page
 */

/**
 * Textarea Class
 */
class Textarea extends Option
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
		$textarea_class = "";
		$type = "text";
		$placeholder = "";
		$autofocus = "";
		$required = "";
		$title = "";
		$maxlength = "";
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
		if (isset($this->maxlength))
			$maxlength = ' maxlength="'.$this->maxlength.'"';

		if (isset($this->label_class))
			$label_class = ' class="'.$this->label_class.'"';
		if (isset($this->textarea_class))
			$textarea_class = ' class="'.$this->textarea_class.'"';
		if ($show_value == true && (!isset($this->type))) {

			if (!isset($this->value) && isset($_POST[$this->id])) {
				$value = $_POST[$this->id];
			} elseif (isset($this->value)) {
				$value = $this->value;
			} else {
				$value = $this->get();
			}
			$show_value = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		}
		if (isset($this->label_id))
			$label = '<label id="'.$this->label_id.'" for="'.$this->id.'"'.$label_class.$title.'>'.$this->name.'</label>';

		return '<textarea name="' . $this->id . '" id="' . $this->id . '"' . $textarea_class . $placeholder. $autofocus.$required.$maxlength.' />'. $show_value .'</textarea>';
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
