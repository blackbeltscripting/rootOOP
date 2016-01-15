<?php
/**
 * Option File
 *
 * Holds all info for Option Class.
 */

/**
 * Option Class
 *
 * Create, edit, add, remove, update your theme options.
 */
class Option
{
	/**
	 * Creates a user table if none is set in DB. Creates an object for Option
	 *
	 * @see MySQL::checkTable() MySQL::checkTable()
	 */
	function __construct($id, $attrs = array())
	{
		global $MySQL;
		$MySQL->checkTable(array(
			'name' => 'options',
			'cols' => array(
				array(
					'name' => 'id',
					'type' => 'BIGINT',
					'type_num' => '20',
					'extra' => 'AUTO_INCREMENT PRIMARY KEY'
				),
				array(
					'name' => 'option_name',
					'type' => 'VARCHAR',
					'type_num' => '64',
					'extra' => false
				),
				array(
					'name' => 'option_value',
					'type' => 'LONGTEXT',
					'type_num' => false,
					'extra' => false
				)
			)
		));
		//  This class must have AT LEAST an id.
		if (!empty($id)) {
			/**
			 *  REGEXP to make sure this follows same structure as a variable.
			 * @see http://php.net/manual/en/language.variables.basics.php Source of REGEXP for variables
			 */
			$filtered = filter_var($id, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/')));
			$this->id = $filtered;
			$this->make($attrs);
		}
	}

	/**
	 * Alias for add()
	 *
	 * @see Option::add() add()
	 *
	 * @param mixed[string|array] $value The value(s) of options.
	 *
	 * @return array The value of the options or false.
	 */
	public function set($value = null)
	{
		$this->add($value);
	}

	/**
	 * Adds Option
	 *
	 * @see Option::getRaw() getRaw()
	 * @see MySQL::insert() MySQL::insert()
	 * @see Option::get() get()
	 *
	 * @param mixed[string|array] $value The value(s) of options.
	 *
	 * @return array The value of the option or false.
	 */
	public function add($value = null)
	{
		if ($this->getRaw() === false) {
			global $MySQL;
			//	Add it, get it, return it.
			$MySQL->insert("options", array("option_name" => $this->id, "option_value" => serialize($value)));
			return $this->get();
		}
	}

	/**
	 * Gets First Option with defined name.
	 *
	 * @see Option::getRaw() getRaw()
	 *
	 * @param string $value The value(s) of options.
	 *
	 * @return array The value of the option.
	 */
	public function get($default_value = false)
	{
		$r = $this->getRaw();

		if ($r != $default_value) {
			return unserialize($r['option_value']);
		} else {
			return $default_value;
		}

	}

	/**
	 * Gets raw options.
	 *
	 * @see MySQL::select() MySQL::select()
	 *
	 * @param string $option The option name.
	 * @param string $value The value(s) of options.
	 *
	 * @return array The value of the option.
	 */
	protected function getRaw()
	{
		global $MySQL;
		$r = $MySQL->select("options", array("option_name" => $this->id));
		if (!empty($r)) {
			return $r;
		} else {
			return false;
		}
	}

	/**
	 * Updates Option.
	 * Will add the option if not found in DB.
	 *
	 * @see MySQL::update() MySQL::update()
	 * @see Option::get() Option::get()
	 *
	 * @param string $value The value(s) of options.
	 *
	 * @return mixed The option value(s).
	 */
	public function update($value = false)
	{
		if ($this->getRaw()) {
			global $MySQL;
			$MySQL->update("options", array("option_value" => serialize($value)), array("option_name" => $this->id));
			return $this->get();
		} else {
			return $this->add($value);
		}
	}

	/**
	 * Deletes Option
	 *
	 * @see MySQL::delete() MySQL::delete()
	 * @see Option::get_raw() get_raw()
	 *
	 * @return boolean True if success.
	 */
	public function delete()
	{
		global $MySQL;
		return $MySQL->delete("options", array("option_name" => $this->id));
	}

	/**
	 * Creates an object for current option
	 *
	 * @ param array $attrs The options in key/val pair.
	 *
	 * @ return null
	 */
	protected function make($attrs)
	{
		foreach ($attrs as $k => $v) {
			$this->{$k} = $v;
		}
	}

	/**
	 * Returns a notice to developer stating that the object does not contain HTML.
	 */
	public function html()
	{
		return "This option has not html.";
	}
}
