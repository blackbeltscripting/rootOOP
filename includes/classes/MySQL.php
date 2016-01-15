<?php
/**
 * MySQL File.
 *
 * Contains MySQL Class
 */

/**
 * MySQL Class
 *
 * Controls all MySQL statments needed for API (PDO only!)
 */
class MySQL
{
	/**
	 * Connect to database the moment this class is called.
	 *
	 * @param string $db_host The hostname of database.
	 * @param string $db_user The database username.
	 * @param string $db_password The password needed to access database.
	 * @param string $db_name The name of the database to access.
	 * @param string $charset Database Character Set.
	 */
	function __construct(
		$db_host = DB_HOST,
		$db_user = DB_USER,
		$db_pass = DB_PASS,
		$db_name = DB_NAME,
		$charset = DB_CHARSET
	) {
		$this->connect($db_host, $db_user, $db_pass, $db_name, $charset);
	}

	/**
	 * Checks to see if table is set.
	 *
	 * @see MySQL::createTable() createTable()
	 * @see MySQL::query() query()
	 *
	 * @param array $table The entire table structure
	 * @return boolean True if success.
	 *
	 * @todo NEED TO CREATE A NOTE FOR CLIENT COULDN'T CREATE TABLE IN DB, CHECK WITH YOUR WEB ADMIN. FOR NOW RETURNS FALSE line[62]
	 */
	final public function checkTable($table)
	{
		if (ALLOW_TABLE_INSERT === true) {
			$query = "SHOW TABLES LIKE '" . TABLE_PREFIX . $table['name'] . "'";

			// Check DB
			$Result = $this->query($query);

			// If no Result...
			if ($Result->rowCount() === 0) {
				// Attempt to create table
				$this->createTable($table);
				// Create new Result
				$newResult = $this->query($query);

				// If 2nd attempt works...
				if ($newResult->rowCount() > 0) {
					// Everything's okay
					return true;
				} else {
					die;
					return false;
				}
			}
		}
		// Passed through.
		return true;
	}

	/**
	 * Creates a new table in database if empty.
	 *
	 * @param array $table The entire table structure
	 */
	final protected function createTable($table)
	{
		$cols = "";
		$n = count($table['cols']);
		$i = 0;
		foreach ($table['cols'] as $k => $col) {
			$type = "";
			if (
				strtolower($col['type']) != "bit" &&
				strtolower($col['type']) != "boolean" &&
				strtolower($col['type']) != "date" &&
				strtolower($col['type']) != "datetime" &&
				strtolower($col['type']) != "text" &&
				strtolower($col['type']) != "longtext"
			) {
				$type = "({$col['type_num']})";
			}
			$cols .= " `" . $col['name'] . "` " . $col['type'] . $type . " " . $col['extra'];

			if (
				strtolower($col['type']) == "varchar" &&
				defined('DB_COLLATE') &&
				constant('DB_COLLATE') != ""
			)
			$cols .= " COLLATE " . DB_COLLATE;
			if (++$i !== $n) $cols .= ",";
		}
		$sql = "CREATE TABLE IF NOT EXISTS `" . TABLE_PREFIX . $table['name'] . "` ($cols);";

		$this->connection->exec($sql);
	}

	/**
	 * Connects to databse using PDO
	 *
	 * @param string $db_host The hostname of database.
	 * @param string $db_user The database username.
	 * @param string $db_password The password needed to access database.
	 * @param string $db_name The name of the database to access.
	 * @param string $charset Database Character Set.
	 *
	 * @return mixed The database method.
	 */
	final protected function connect($db_host, $db_user, $db_password, $db_name, $charset)
	{
		$this->connection = new PDO("mysql:host=$db_host;dbname=$db_name;charset=$charset", $db_user, $db_password);
		// debug mode
		//$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		return $this->connection;
	}

	/**
	 * Formats the query from array to string.
	 *
	 * @param array $values The values in {name} => {value} key, value pair.
	 * @return string The formated value.
	 */
	final private function formatWhere($values)
	{
		$where = array();
		foreach ($values as $col => $val) {
			$where[] = $col . " = :" . $col;
		}
		$where = implode(" AND ", $where);
		return $where;
	}

	/**
	 * Formats the query from array to string.
	 *
	 * @param array $values The values in {name} => {value} key, value pair.
	 * @return string The formated value.
	 */
	final private function formatSet($values)
	{
		$where = array();
		foreach ($values as $col => $val) {
			$where[] = $col . " = :" . $col;
		}
		$where = implode(", ", $where);
		return $where;
	}

	/**
	 * The raw query.
	 * Used widely in many functions.
	 *
	 * @param string $query The raw query.
	 *
	 * @return mixed The raw query results.
	 */
	final public function query($query)
	{
		return $this->connection->query($query);
	}

	/**
	 * Insert into DB using locally protected bind_and_execute() function.
	 * Using Preparing Statements, thus following the OWASP Standard
	 * @see https://www.owasp.org/index.php/SQL_Injection_Prevention_Cheat_Sheet Reference source.
	 * @see MySQL::bindAndExecute() bindAndExecute()
	 *
	 * @param string $table The name of the table (without prefixes).
	 * @param array $values The value arrays in {name} => {value} pairs.
	 *
	 * @return array The newly insterted element.
	 */
	final public function insert($table, $values)
	{
		$cols = implode(', ', array_keys($values));
		$vals_arr = array();
		foreach ($values as $key => $val) {
			$vals_arr[] = ":$key";
		}
		$vals = implode(', ', $vals_arr);
		$query = "INSERT INTO " . TABLE_PREFIX . $table . " ($cols) VALUES ($vals)";

		return $this->bindAndExecute($query, $values);
	}

	/**
	 * Selects from the DB using locally protected bind_and_execute() function.
	 * Using Preparing Statements, thus following the OWASP Standard
	 * @see https://www.owasp.org/index.php/SQL_Injection_Prevention_Cheat_Sheet Reference source.
	 * @see MySQL::formatWhere() formatWhere()
	 * @see MySQL::bindAndExecute() bindAndExecute()
	 *
	 * @param string $table The name of the table (without prefixes).
	 * @param array $values The value arrays in {name} => {value} pairs.
	 * @param string $extra Any extra thing you don't need to bind.
	 *
	 * @return object The MySQL Statement
	 */
	final public function select($table, $values, $extra = false)
	{
		$where = $this->formatWhere($values);
		if (isset($extra))
			$extra = " " . $extra;
		$query = "SELECT * FROM " . TABLE_PREFIX . $table . " WHERE " . $where . $extra;
		return $this->bindAndExecute($query, $values)->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Updates into DB using local protected bind_and_execute() function.
	 * Using Preparing Statements, thus following the OWASP Standard
	 * @see https://www.owasp.org/index.php/SQL_Injection_Prevention_Cheat_Sheet Reference source.
	 * @see MySQL::formatWhere() formatWhere()
	 * @see MySQL::bindAndExecute() bindAndExecute()
	 *
	 * @param string $table The name of the table (without prefixes).
	 * @param array $set_values The value arrays in {set} => {value},
	 * @param array $where_values The value arrays in {where} => {value} pairs.
	 *
	 * @return object The MySQL Statement
	 */
	final public function update($table, $set_values, $where_values) {
		$set = $this->formatSet($set_values);
		$where = $this->formatWhere($where_values);
		$values = array_merge($set_values, $where_values);
		$query = "UPDATE " . TABLE_PREFIX . $table . " SET " . $set . " WHERE " . $where;

		return $this->bindAndExecute($query, $values);
	}

	/**
	 * Deletes one row from DB.
	 * Using Preparing Statements, thus following the OWASP Standard
	 *
	 * @see https://www.owasp.org/index.php/SQL_Injection_Prevention_Cheat_Sheet Reference source.
	 * @see MySQL::formatWhere() formatWhere()
	 * @see MySQL::bindAndExecute() bindAndExecute()
	 *
	 * @param string $table The name of the table (without prefixes).
	 * @param array $values The value arrays in {name} => {value} pairs.
	 *
	 * @return object The MySQL Statement
	 */
	final public function delete($table, $values)
	{
		$where = $this->formatWhere($values);
		$query = "DELETE FROM " . TABLE_PREFIX . $table . " WHERE ". $where . " LIMIT 1";

		return $this->bindAndExecute($query, $values);
	}

	/**
	 * Binds potentially unsafe values to query and executes query.
	 *
	 * @see https://www.owasp.org/index.php/SQL_Injection_Prevention_Cheat_Sheet Reference source.
	 *
	 * @param string $query The original query being prepared.
	 * @param array $values The value arrays in {name} => {value} pairs.
	 *
	 * @return array The newly insterted element.
	 */
	final protected function bindAndExecute($query, $values)
	{
		$stmt = $this->connection->prepare($query);
		/**
		 * Binds Values using reference
		 *
		 * @see http://stackoverflow.com/questions/4174524/binding-params-for-pdo-statement-inside-a-loop Reference
		 */
		foreach ($values as $col => &$val) {
			$stmt->bindParam(":$col", $val);
		}

		// Execute and return
		if ($stmt->execute()) {
			return $stmt;
		}
	}
}
