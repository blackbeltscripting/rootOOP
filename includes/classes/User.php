<?php
/**
 * User File.
 *
 * Contains User Class
 */

/**
 * User Class
 *
 * Responsible for login/out, authentification, forgot passwords auth tokens, etc.
 * ##### WARNING: This page requires PHP5 >= 5.5
 */
class User
{
	/**
	 * SANITIZING Filters used against XSS attacks.
	 *
	 * All Filters that will be saved to the DB table MUST be filtered before entering the DB.
	 * Note: All user fields MUST be Sanitized here. 'email' is Validated because it's an identifying variable.
	 *
	 * @see User::getAll() User::getAll()
	 * @see https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet OWAST CSS Cheat Sheet
	 * @see http://php.net/manual/en/filter.filters.sanitize.php PHP Sanitizing Filters
	 *
	 * @var array $validateArgs
	 */
	protected $sanitizeArgs = array(
		'id' => FILTER_SANITIZE_NUMBER_INT,
		'email' => FILTER_VALIDATE_EMAIL, // Validate instead of Sanitize because this is an identifying variable
		'password' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'company' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'first_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'last_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'contact_number' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'verified' => FILTER_SANITIZE_NUMBER_INT,
	);

	/**
	 * Used to VALIDATE Forgotten Password Token given to client's email.
	 *
	 * @see User::authForgottenToken() User::authForgottenToken()
	 *
	 * @var array $authTokenValidate
	 */
	protected $authTokenValidate = array(
		"options" => array(
			"regexp" => "/^[a-zA-Z0-9]*$/"
		)
	);

	/**
	 * Used to VALIDATE User ID min/max range.
	 *
	 * @see User::get() User::get()
	 *
	 * @var array $userIdValidate
	 */
	protected $userIdValidate = array(
		"options"=> array(
			"min_range" => 0
		)
	);

	/**
	 * Using Higher Entropy functionality to create PRNG characters.
	 *
	 * (Used specifically for forgot password auth token).
	 *
	 * @see http://stackoverflow.com/questions/3290283/what-is-a-good-way-to-produce-a-random-site-salt-to-be-used-in-creating-passwo/3291689#3291689 Source of code.
	 * @todo I'm guessing this will have to be switched later to a higher entropy PRNG as this seems to be a weak area of programming itself.
	 * @todo Figure out how much this thing works, how much entropy can and is creating and bitlengths.
	 *
	 * @param integer $min minimum entropy.
	 * @param integer $max maximum entropy.
	 *
	 * @todo What is this returning?
	 */
	private function cryptoRandSecure($min, $max)
	{
		$range = $max - $min;
		if ($range < 0) return $min; // not so random...
		$log = log($range, 2);
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd >= $range);
		return $min + $rnd;
	}

	/**
	 * Create a Token Using Higher Entropy functionality to create PRNG characters.
	 *
	 * (Used specifically for forgot password auth token).
	 *
	 * @see http://stackoverflow.com/questions/3290283/what-is-a-good-way-to-produce-a-random-site-salt-to-be-used-in-creating-passwo/3291689#3291689 Source of code.
	 * @see User::cryptoRandSecure() cryptoRandSecure()
	 *
	 * @param integer $length The length of the token.
	 *
	 * @return string The authToken.
	 */
	public function getToken($length=32)
	{
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		for ($i=0;$i<$length;$i++) {
			$token .= $codeAlphabet[$this->cryptoRandSecure(0,strlen($codeAlphabet))];
		}
		return $token;
	}

	/**
	 * Creates a user table if none is set in DB.
	 *
	 * @see MySQL::checkTable() MySQL::checkTable()
	 */
	function __construct()
	{
		global $MySQL;

		$MySQL->checkTable(array(
			'name' => 'users',
			'cols' => array(
				array(
					'name' => 'id',
					'type' => 'BIGINT',
					'type_num' => '20',
					'extra' => 'AUTO_INCREMENT PRIMARY KEY'
				),
				array(
					'name' => 'email',
					'type' => 'VARCHAR',
					'type_num' => '100',
					'extra' => false
				),
				array(
					'name' => 'password',
					'type' => 'VARCHAR',
					'type_num' => '64',
					'extra' => false
				),
				array(
					'name' => 'company',
					'type' => 'VARCHAR',
					'type_num' => '255',
					'extra' => false
				),
				array(
					'name' => 'first_name',
					'type' => 'VARCHAR',
					'type_num' => '50',
					'extra' => false
				),
				array(
					'name' => 'last_name',
					'type' => 'VARCHAR',
					'type_num' => '50',
					'extra' => false
				),
				array(
					'name' => 'contact_number',
					'type' => 'VARCHAR',
					'type_num' => '20',
					'extra' => false
				),
				array(
					'name' => 'verified',
					'type' => 'INT',
					'type_num' => '1',
					'extra' => 'DEFAULT 0'
				),
				array(
					'name' => 'verifyToken',
					'type' => 'VARCHAR',
					'type_num' => '50',
					'extra' => false
				),
				array(
					'name' => 'registered_date',
					'type' => 'DATETIME',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'last_login',
					'type' => 'DATETIME',
					'type_num' => false,
					'extra' => false
				)
			)
		));

		$MySQL->checkTable(array(
			'name' => 'forgot_pass_auth',
			'cols' => array(
				array(
					'name' => 'id',
					'type' => 'BIGINT',
					'type_num' => '20',
					'extra' => 'AUTO_INCREMENT PRIMARY KEY'
				),
				array(
					'name' => 'email',
					'type' => 'VARCHAR',
					'type_num' => '100',
					'extra' => false
				),
				array(
					'name' => 'date_created',
					'type' => 'DATETIME',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'has_used',
					'type' => 'BOOLEAN',
					'type_num' => false,
					'extra' => 'NOT NULL'
				),
				array(
					'name' => 'token',
					'type' => 'VARCHAR',
					'type_num' => '205',
					'extra' => false
				),
				array(
					'name' => 'ip_info',
					'type' => 'VARCHAR',
					'type_num' => '205',
					'extra' => false
				),
			)
		));
	}

	/**
	 * Check to see if there is an open session.
	 *
	 * @return boolean True if logged.
	 */
	final public function isLogged()
	{
		return (!empty($_SESSION['email']) && !isset($_SESSION['forgot_password']));
	}

	/**
	 * Validates email.
	 *
	 * @param string $email The raw email from front-end client.
	 *
	 * @return boolean True if fails.
	 */
	final private function validateEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Login function.
	 *
	 * Creates a session and is open for developers to create custom functionality.
	 */
	public function login()
	{
		// Left purposely empty.
	}

	/**
	 * Gets user object
	 *
	 * @see User::$userIdValidate User::$userIdValidate
	 * @see MySQL::select() MySQL::select()
	 *
	 * @param string $email Email of requested user.
	 * @param boolean $safe If set to true, will only give the email and registered date.
	 *
	 * @todo When it doesn't work, it should return an array with error code.
	 */
	final function get($id)
	{
		global $MySQL;
		if (filter_var($id, FILTER_VALIDATE_INT, $this->userIdValidate)) {
			if ($db = $MySQL->select("users", array("id" => $id))) {
				return filter_var_array($db, $this->sanitizeArgs);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Gets all users
	 *
	 * @see MySQL::query()
	 * @see User::$sanitizeArgs User::$sanitizeArgs
	 *
	 * @todo Must create a if auth level, remove sensitive information.
	 * @todo Escape returning values here.
	 *
	 * @return mixed[boolean|object] Gets all the users including sensitive information.
	 */
	final public function getAll()
	{
		global $MySQL;
		//	NOTE: MUST CREATE A IF AUTH LEVEL
		$all_users = $MySQL->query("SELECT * FROM " . TABLE_PREFIX . "users")->fetchAll(PDO::FETCH_ASSOC);
		foreach ($all_users as $k => $user) {
			/**
			 * Sanitize before inserting to DB
			 *
			 */
			$all_users[$k] = filter_var_array($user, $this->sanitizeArgs);

		}
		return $all_users;
	}

	/**
	 * Authenticates User
	 *
	 * @see User::login() login()
	 *
	 * @param string $email User Email
	 * @param string $pass Raw Password
	 *
	 * @return boolean True if success.
	 */
	final public function authenticate($email, $pass)
	{
		if ($this->validateEmail($email)) {
			global $MySQL;
			if ($db = $MySQL->select("users", array("email" => $email))) {
				if ($db['verified'] == 1) {
					if (password_verify($pass, $db['password'])) {
						// Update last login datetime.
						$MySQL->update(
							"users",
							array("last_login" => date("Y-m-d H:i:s")),
							array("email" => $email) // This is ok because it's been validated above.
						);
						/**
						 * Sanitize before displaying to client
						 *
						 * @see User::$sanitizeArgs
						 */
						$clean_user = filter_var_array($db, $this->sanitizeArgs);

						$_SESSION = $clean_user;

						/**
						 * Authetification Complete.
						 *
						 * Create Site Auth Token
						 */
						$_SESSION[TOKEN] = $this->getToken(128);

						$this->login();

						//  If we're not on the core page, let's go there.
						if (basename($_SERVER['PHP_SELF']) != "index.php")
							exit(header("Location: " . SITE_URL));
						return true;
					}
				} else {
					exit(header("Location: " . SITE_URL . "validate_email.php"));
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * User Signup.
	 * Creates a new user if not yet in DB. With the option of automatic user login.
	 *
	 * @see User::get() get()
	 * @see MySQL::insert() MySQL::insert()
	 * @see User::authenticate() User::authenticate()
	 *
	 * @param object $user The new user.
	 * @param boolean $ignoreEmailVerification Ignores the email verification email.
	 *
	 * @return mixed[boolean|object] True if success. False if fail. [object] If automatic user login success.
	 */
	final public function signup($user, $ignoreEmailVerification = false)
	{
		global $MySQL;

		if ($this->validateEmail($user['email'])) {
			/**
			 * Sanitize before doing anything including sending to DB
			 *
			 * @see User::$sanitizeArgs
			 */
			$clean_user = array_filter(filter_var_array($user, $this->sanitizeArgs));

			//	Register now.
			$clean_user['registered_date'] = date("Y-m-d H:i:s");

			//	Hash password.
			$clean_user['password'] = password_hash($clean_user['password'], PASSWORD_DEFAULT);

			// If true then send the email verification.
			if ($ignoreEmailVerification === true) {
				// Admin is adding the user.

				// Bypass email verification.
				$clean_user['verified'] = 1;

				//	Insert into DB.
				$MySQL->insert("users", $clean_user);

				return true;
			} else {
				// Email Verification Token.
				$clean_user['verifyToken'] = $this->getToken(50);

				//	Insert into DB.
				$MySQL->insert("users", $clean_user);

				return $this->verifyEmail($clean_user);
			}
		} else {
			return false;
		}
	}

	/**
	 * Edits Users.
	 *
	 * @param string $email Email of who we will be editing.
	 * @param array $modifiers What we will be editing.
	 *
	 * @return boolean True if success.
	 */
	final public function edit($email, $modifiers = array()) {
		/**
		 * @todo: Finish this function.
		 */
		$clean_user = array_filter(filter_var_array($modifiers, $this->sanitizeArgs));

		if (!empty($clean_user['password'])) // Hashes password.
			$clean_user['password'] = password_hash($clean_user['password'], PASSWORD_DEFAULT);
		else
			unset($clean_user['password']);

		global $MySQL;
		$MySQL->update(
			"users",
			$clean_user,
			array("email" => $email) // This is ok because this is not currently User data, it's Session data.
		);

		return true;
	}

	/**
	 * Forgot Password Function.
	 * Emails auth token and inserts it to the DB.
	 *
	 * @see User::get() get()
	 * @see MySQL::update() MySQL::update()
	 *
	 * @todo Create a maximum auth request per time limit.
	 *
	 * @param string $email Email of requested password renewal.
	 */
	final public function forgotPassword($email)
	{
		global $MySQL;
		if ($this->validateEmail($email) && $db = $MySQL->select("users", array("email" => $email, "verified" => 1))) {
			//	Forge key
			$key = $this->getToken(64);

			$forward = "";
			if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				$forward = $_SERVER['HTTP_X_FORWARDED_FOR'];

			//	Inserts key to DB
			$MySQL->insert(
				"forgot_pass_auth",
				array(
					"email" => $email,
					"date_created" => date("Y-m-d H:i:s"),
					"token" => $key,
					"ip_info" => serialize(array(
						"REMOTE_ADDR" => $_SERVER['REMOTE_ADDR'],
						"HTTP_X_FORWARDED_FOR" => $forward
					))
				)
			);

			//	Send Email
			$headers = 'From: no-reply@' . $_SERVER['HTTP_HOST'] . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
			mail($email, FORGOT_PASSWORD_EMAIL_SUBJECT, sprintf(FORGOT_PASSWORD_EMAIL_BODY, $key), $headers);
			print_r(sprintf(FORGOT_PASSWORD_EMAIL_BODY, $key));
		}
		//exit(header("Location: ".SITE_URL."forgot_password.php?check_email=true"));
	}

	/**
	 * Checks DB to match Forgot Password Token that was sent to client's email.
	 *
	 * Then returns whether or not token is both valid and a match.
	 *
	 * @see User::$authTokenValidate User::$authTokenValidate
	 *
	 * @param string $token The token sent from automatic email.
	 *
	 * @return boolean True if success.
	 */
	final public function authForgottenToken($token)
	{
		// Clean up token in case of XSS
		$token = filter_var($token, FILTER_VALIDATE_REGEXP, $this->authTokenValidate);

		global $MySQL;
		return $MySQL->select("forgot_pass_auth", array("token" => $token, "has_used" => 0), " AND date_created >= now() - INTERVAL 1 DAY");
	}

	/**
	 * Logs out current client.
	 *
	 * @return boolean True if success.
	 */
	final public function logout()
	{
		session_unset();
		session_destroy();
		header("Location: " . SITE_URL);
	}

	/**
	 * Determines whether user can access particular logic.
	 *
	 * @param string $id The logic's unique id.
	 *
	 * @return boolean True if is accessible.
	 */
	final public function canAccess($id)
	{
		return true;
	}

	/**
	 * Authentifies Email
	 *
	 * @param string $token The auth token.
	 *
	 * @return boolean True if success.
	 */
	final public function authEmail($token)
	{
		// Clean up token in case of XSS
		$token = filter_var($token, FILTER_VALIDATE_REGEXP, $this->authTokenValidate);

		global $MySQL;
		if ($user = $MySQL->select("users", array("verifyToken" => $token, "verified" => 0))) {
			$arr = array("verified" => 1);
			$this->edit($user['email'], $arr);
			return true;
		}
	}

	/**
	 * Sends a verification email to client after signup
	 *
	 * @param array $user The user's information
	 *
	 * @return boolean True if success.
	 */
	final protected function verifyEmail($user)
	{
		global $MySQL;
		if ($this->validateEmail($user['email'])) {
			//	Send Email & return true.
			$headers = 'From: no-reply@' . $_SERVER['HTTP_HOST'] . "\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
			mail($user['email'], VERIFY_EMAIL_SUBJECT, sprintf(VERIFY_EMAIL_BODY, $user['first_name'] . " " . $user['last_name'], $user['verifyToken']), $headers);
			exit(header("Location: ".SITE_URL."validate_email.php"));
		} else {
			return false;
		}
	}
}
