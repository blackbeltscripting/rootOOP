<?php
/**
 * Page for Post Class
 */

/**
 * Class for Post
 */
class Post
{
	/** The sum of all posts in the queue currently. */
	public $queue;

	/** Determines whether or not the post will be handled through ajax. */
	protected $ajax;

	/**
	 *
	 *
	 */
	public function __construct($ajax = false)
	{
		if (isset($ajax))
			$this->ajax = true;
	}
	/**
	 * Adds Form to Post queue
	 *
	 * @param object $form
	 */
	public function add($function_to_add, $args = array())
	{
		global $User;
		/** Post will be added and will need a TOKEN. Create if not yet done. */
		if (!isset($_SESSION[TOKEN]))
			$_SESSION[TOKEN] = $User->getToken(128);
		$this->queue[] = array("function" => $function_to_add, "args" => $args);
	}

	/**
	 * Process each form
	 */
	public function process()
	{
		$Results = array();
		if (is_array($this->queue)) {
			if (isset($_POST[TOKEN], $_SESSION[TOKEN]) && $_POST[TOKEN] === $_SESSION[TOKEN]) {
				//  unsets User Auth Token Here.
				if ($this->ajax == false)
					unset($_POST[TOKEN]);

				foreach ($this->queue as $tag => $the_) {
					$result = call_user_func_array($the_['function'], $the_['args']);
					if (!is_null($result))
						$Results[$the_['function']] = $result;
				}
				return $Results;
			}
		}
	}
}
