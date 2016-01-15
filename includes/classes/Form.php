<?php
/**
 * Form Class Page
 */

/**
 * The Form Class
 */
class Form
{
	public $elements;

	protected $ajax;

	/**
	 * Construct for Form Class
	 *
	 * @param string $method Methods are "get/post"
	 */
	function __construct($id, $method = null, $ajax = null)
	{
		if ($ajax !== null) {
			$this->ajax = true;
			$this->func = $ajax;
		}
		/**
		 *  REGEXP to make sure this follows same structure as a variable.
		 * @see http://php.net/manual/en/language.variables.basics.php Source of REGEXP for variables
		 */
		$filtered = filter_var($id, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/')));

		if (!empty($id) && $filtered === $id) {
			$this->id = $filtered;

			if ($method === null)
				$method = "post";
			$this->method = $method;
		} else {
			die("Form Class must have variable name for \$id.");
		}
	}

	/**
	 * Adds particular elements onto form.
	 *
	 * @param object $element The Element to add to Form
	 */
	public function add($element)
	{
		if (is_array($element)) {
			foreach ($element as $key => $value) {
				$this->elements[$key] = $value;
			}
		} else {
			$this->elements[] = $element;
		}
	}


	/**
	 * Deletes particular elements from form.
	 *
	 * @param object $element The Element to delete from Form
	 */
	public function delete($element)
	{
		if (!empty($this->elements[$element]))
			unset($this->elements[$element]);
	}

	/**
	 * Shows the HTML version of the form
	 *
	 * @param boolean $show_value If set to true shows the element's stored value(s)
	 *
	 * @see User::getToken() User::getToken()
	 * @see User::hasAccess() User::hasAccess()
	 * @see Option::html() Option::html()
	 *
	 * @return string The HTML Form
	 */
	public function html($show_value = null)
	{
		global $User;
		if ($User->canAccess($this->id)) {
			$html = "<input type=\"hidden\" id=\"".TOKEN."\" name=\"".TOKEN."\" value=\"".$_SESSION[TOKEN]."\" />";
			foreach ($this->elements as $el) {
				/** The following Logic allows any string logic to pass through */
				if (is_object($el))
					$html .= $el->html($show_value);
				else
					$html .= $el;
			}
			$ajax = "";
			if (isset($this->ajax))
				$ajax = ' ajax="true" callback="'.$this->func.'"';
			return "<form method=\"".$this->method."\" id=\"".$this->id."\"$ajax>
				$html
				</form>";
		} else {
			return false;
		}
	}
}
