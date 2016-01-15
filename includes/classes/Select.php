<?php

/**
 * Select Class Page
 */

/**
 * Select Class
 */
class Select extends Option
{
	/**
	 * Displays HTML Version of the option
	 *
	 * @param boolean $show_value Display escaped current value of option.
	 */
	public function html($show_value = null)
	{
		$title = "";
		$label_class = "";
		$label = "";
		$required = "";
		if (isset($this->placeholder)) {
			$title = ' title="'.$this->placeholder.'"';
		}
		if (isset($this->required))
			$required = " required";
		if (isset($this->label_class))
			$label_class = ' class="'.$this->label_class.'"';

		$options = "";
		if (isset($this->options) && is_array($this->options)) {
			foreach ($this->options as $k => $v) {
				$selected = "";
				$disabled = "";
				if (($this->get() == $k || $this->selected == $k) && $show_value !== null)
					$selected = " selected";
				if (isset($v['disabled']) && $v['disabled'] != false)
					$disabled = " disabled";
				$options .= "<option value=\"$k\"". $selected . $disabled. ">" . $v['name'] . "</option>";
			}
		}
		if (isset($this->label_id))
			$label = '<label id="'.$this->label_id.'" for="'.$this->id.'"'.$label_class.$title.'>'.$this->name.'</label>';
		$select = '<select name="'. $this->id . '" id="'.$this->id.'"'.$required.'>'.$options.'</select>';
		return $label.$select;
	}

	/**
	 * Logic for filtering and saving option
	 *
	 * @param string $value The text value
	 *
	 * @return mixed if true returns the value.
	 */
	public function save($value)
	{
		if (isset($this->options[$value])) {
			return $this->update($value);
		} else {
			return false;
		}
	}
}
