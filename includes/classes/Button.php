<?php
/**
 * Button Class Page
 */

/**
 * Button Class
 */
class Button extends Option
{
	/**
	 * Displays HTML version of the option.
	 *
	 * @param boolean $show_value Display escaped current value of option.
	 */
	public function html()
	{
		$name = "";
		$value = "";
		$id = "";
		$btn_name = "Submit";
		$type = "";
		$class = "";

		if (isset($this->type)) {
			$type = " type=\"".$this->type. "\"";
		} else {
			$type = " ajax=\"true\"";
		}

		if (isset($this->name))
			$name = " name=\"{$this->name}\"";
		if (isset($this->value))
			$value = " value=\"{$this->value}\"";
		if (isset($this->id))
			$id = " id=\"{$this->id}\"";
		if (isset($this->class))
			$class = " class=\"{$this->class}\"";
		if (isset($this->btn_name))
			$btn_name = $this->btn_name;
		if (isset($this->button_name))
			$btn_name = $this->button_name;

		return "<button" . $type . $name . $value . $id . $class . ">$btn_name</button>";
	}

	/**
	 * Logic for filtering and saving option
	 *
	 * @return false Will not do any logic for buttons.
	 */
	function save()
	{
		return false;
	}
}
