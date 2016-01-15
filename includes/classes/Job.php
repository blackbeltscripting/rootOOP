<?php
/**
 * Jobs Page
 *
 * Contains Jobs Class
 */

/**
 * Jobs Class
 *
 * Creates, edits, removes jobs.
 */
class Job
{
	/**
	 * SANITIZE Filters used against XSS attacks.
	 *
	 * All Filters that will be saved to the DB table MUST be filtered before entering the DB.
	 * Note: All user fields MUST be Sanitized here.
	 *
	 * @see https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet OWASP CSS Cheat Sheet
	 * @see http://php.net/manual/en/filter.filters.sanitize.php PHP Sanitizing Filters
	 *
	 * @var array $validateArgs
	 */
	protected $sanitizeArgs = array(
		'id' => FILTER_SANITIZE_NUMBER_INT,
		'client' => FILTER_SANITIZE_NUMBER_INT,
		'name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'description' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'hourly_rate' => array(
			'filter' => FILTER_SANITIZE_NUMBER_FLOAT, // Sanitizes to right format.
			'flags' => FILTER_FLAG_ALLOW_FRACTION,
			'options' => array('decimal' => SQL_FLOAT_DECIMAL_SEPARATOR),
		),
		'date_started' => array(
			'filter' => FILTER_VALIDATE_REGEXP,
			'flags' => FILTER_NULL_ON_FAILURE, // Have not tested this.
			'options' => array('regexp' => '/^\d{4}-\d{2}-\d{2}/') // Date RegEx
		),
		'date_due' => array(
			'filter' => FILTER_VALIDATE_REGEXP,
			'flags' => FILTER_NULL_ON_FAILURE, // Have not tested this.
			'options' => array('regexp' => '/^\d{4}-\d{2}-\d{2}/') // Date RegEx
		),
		'date_completed' => array(
			'filter' => FILTER_VALIDATE_REGEXP,
			'flags' => FILTER_NULL_ON_FAILURE, // Have not tested this.
			'options' => array('regexp' => '/^\d{4}-\d{2}-\d{2}/') // Date RegEx
		)
	);

	/**
	 * SANITIZE Filters used against XSS attacks.
	 *
	 * All Filters that will be saved to the DB table MUST be filtered before entering the DB.
	 * Note: All user fields MUST be Sanitized here.
	 *
	 * @see https://www.owasp.org/index.php/XSS_(Cross_Site_Scripting)_Prevention_Cheat_Sheet OWASP CSS Cheat Sheet
	 * @see http://php.net/manual/en/filter.filters.sanitize.php PHP Sanitizing Filters
	 *
	 * @var array $validateArgs
	 */
	protected $sanitizeTaskArgs = array(
		'job_id' => FILTER_SANITIZE_NUMBER_INT,
		'description' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
		'hours_billing' => array(
			'filter' => FILTER_SANITIZE_NUMBER_FLOAT, // Sanitizes to right format.
			'flags' => FILTER_FLAG_ALLOW_FRACTION,
			'options' => array('decimal' => SQL_FLOAT_DECIMAL_SEPARATOR),
		),
		'hours_actual' => array(
			'filter' => FILTER_SANITIZE_NUMBER_FLOAT, // Sanitizes to right format.
			'flags' => FILTER_FLAG_ALLOW_FRACTION,
			'options' => array('decimal' => SQL_FLOAT_DECIMAL_SEPARATOR),
		),
		'hr_rate' => array(
			'filter' => FILTER_SANITIZE_NUMBER_FLOAT, // Sanitizes to right format.
			'flags' => FILTER_FLAG_ALLOW_FRACTION,
			'options' => array('decimal' => SQL_FLOAT_DECIMAL_SEPARATOR),
		)
	);

	/**
	 * Used to VALIDATE User ID min/max range.
	 *
	 * @see User::get() User::get()
	 *
	 * @var array $userIdValidate
	 */
	protected $jobIdValidate = array(
		"options"=> array(
			"min_range" => 0
		)
	);

	/**
	 * Creates a user table if none is set in DB.
	 *
	 * @see MySQL::check_table() MySQL::check_table()
	 *
	 */
	function __construct()
	{
		global $MySQL;

		$MySQL->checkTable(array(
			'name' => 'jobs',
			'cols' => array(
				array(
					'name' => 'id',
					'type' => 'BIGINT',
					'type_num' => '20',
					'extra' => 'AUTO_INCREMENT PRIMARY KEY'
				),
				array(
					'name' => 'client',
					'type' => 'BIGINT',
					'type_num' => '20',
					'extra' => false
				),
				array(
					'name' => 'name',
					'type' => 'VARCHAR',
					'type_num' => '255',
					'extra' => false
				),
				array(
					'name' => 'description',
					'type' => 'LONGTEXT',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'hourly_rate',
					'type' => 'DECIMAL',
					'type_num' => '10,2',
					'extra' => false
				),
				array(
					'name' => 'date_started',
					'type' => 'DATE',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'date_due',
					'type' => 'DATE',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'date_completed',
					'type' => 'DATE',
					'type_num' => false,
					'extra' => false
				)
			)
		));

		$MySQL->checkTable(array(
			'name' => 'tasks',
			'cols' => array(
				array(
					'name' => 'id',
					'type' => 'BIGINT',
					'type_num' => '20',
					'extra' => 'AUTO_INCREMENT PRIMARY KEY'
				),
				array(
					'name' => 'job_id',
					'type' => 'BIGINT',
					'type_num' => '20',
					'extra' => false
				),
				array(
					'name' => 'order',
					'type' => 'INT',
					'type_num' => '10',
					'extra' => false
				),
				array(
					'name' => 'description',
					'type' => 'TEXT',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'long_description',
					'type' => 'LONGTEXT',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'hours_billing',
					'type' => 'DECIMAL',
					'type_num' => '4,2',
					'extra' => false
				),
				array(
					'name' => 'hours_real',
					'type' => 'DECIMAL',
					'type_num' => '4,2',
					'extra' => false
				),
				array(
					'name' => 'hourly_rate',
					'type' => 'DECIMAL',
					'type_num' => '10,2',
					'extra' => false
				),
				array(
					'name' => 'amount',
					'type' => 'DECIMAL',
					'type_num' => '10,2',
					'extra' => false
				),
				array(
					'name' => 'date_created',
					'type' => 'DATE',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'date_started',
					'type' => 'DATE',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'date_due',
					'type' => 'DATE',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'date_completed',
					'type' => 'DATE',
					'type_num' => false,
					'extra' => false
				),
				array(
					'name' => 'locked',
					'type' => 'BOOLEAN',
					'type_num' => false,
					'extra' => false
				)
			)
		));
	}

	/**
	 * Gets all jobs
	 *
	 * @see MySQL::query() MySQL::query()
	 * @see Job::$sanitizeArgs Job::$sanitizeArgs
	 *
	 * @param integer $id The client's DB ID#
	 *
	 * @return mixed The SANITIZED database result.
	 */
	public function getAll($id = false)
	{
		global $MySQL;
		global $Option;

		$where = "";

		if ($id !== false)
			$where = "WHERE client = '$id'";

		$all_jobs = $MySQL->query("SELECT * FROM " . TABLE_PREFIX . "jobs $where")->fetchAll(PDO::FETCH_ASSOC);

		foreach ($all_jobs as $k => $job) {
			/**
			 * Sanitize before inserting to DB
			 *
			 * @see Job::$sanitizeArgs Job::$sanitizeArgs
			 */
			$all_jobs[$k] = filter_var_array($job, $this->sanitizeArgs);

			// Calculates job status
			$all_jobs[$k]['status'] = $this->getJobStatus(
				$id,
				$all_jobs[$k]['date_started'],
				$all_jobs[$k]['date_completed'],
				$all_jobs[$k]['date_due']
			);
			if (isset($all_jobs[$k]['hourly_rate']))
				$all_jobs[$k]['hourly_rate'] = $Option['hourly_rate']->get();

			$all_jobs[$k]['total'] = $this->getTotal($job['id'], $all_jobs[$k]['hourly_rate']);
		}
		return $all_jobs;
	}

	/**
	 * Calculates current status of Job.
	 *
	 * @param int $id Job ID.
	 * @param date $date_started Date that Job was Created.
	 * @param date $date_completed Date of Job was completed.
	 * @param date $date_due Job's due date.
	 *
	 * @return string Job status.
	 */
	final private function getJobStatus($id, $date_started, $date_completed, $date_due)
	{
		if ($date_started !== null) { // This job has started.
			if ($date_completed === null) { // This job has NOT been completed.
				if ($date_due !== null) { // This job has a due date
					$date_due = new DateTime($date_due);
					$now = new DateTime(date("Y-m-d"));
					if ($date_due >= $now) { // Not Past Due.
						$status = "Working";
					} else {
						$status = "Past Due";
					}
				} else {
					$status = "Working";
				}
			} else {
				$status = "Completed";
			}
		} else { // Hasn't started yet.
			$status = "Draft";
		}
		return $status;
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
	final public function get($id)
	{
		global $MySQL;
		global $Option;
		if (filter_var($id, FILTER_VALIDATE_INT, $this->jobIdValidate)) {
			if ($db = $MySQL->select("jobs", array("id" => $id))) {
				$job = filter_var_array($db, $this->sanitizeArgs);
				// Calculates job status
				$job['status'] = $this->getJobStatus(
					$id,
					$db['date_started'],
					$db['date_completed'],
					$db['date_due']
				);
				if (isset($db['hourly_rate']))
					$db['hourly_rate'] = $Option['hourly_rate']->get();

				$job['total'] = $this->getTotal($id, $db['hourly_rate']);

				return $job;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Edits the job
	 */
	final public function edit($job)
	{
		/**
		 * Sanitize before doing anything including sending to DB
		 */
		$clean_job = filter_var_array($job, $this->sanitizeArgs);
		$clean_id = $clean_job['id'];
		unset($clean_job['id']);

		if (is_array($job)) {
			global $MySQL;
			$MySQL->update(
				"jobs",
				$clean_job,
				array("id" => $clean_id)
			);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Gets total amount of job through tasks.
	 *
	 */
	final private function getTotal($id, $hourly_rate)
	{
		global $MySQL;
		$allTasks = $MySQL->query("SELECT * FROM ".TABLE_PREFIX."tasks WHERE job_id='$id'")->fetchAll(PDO::FETCH_ASSOC);

		$total = 0;
		$total_actual = 0;
		foreach ($allTasks as $task) {
			$task_total = 0;
			if ($task['hr_rate'] !== null) {
				$task_total = $task['hr_rate'] * $task['hours_billing'];
			} else {
				$task_total = $hourly_rate * $task['hours_billing'];
			}
			$total_actual += $task['hours_actual'] * $task['hr_rate'];
			$total += $task_total;
		}
		$total = number_format($total, 2);
		$total_actual = number_format($total_actual, 2);
		return "$$total <span>($$total_actual)</span>";
	}

	/**
	 * Adds a job
	 *
	 * @see MySQL::insert() MySQL::insert()
	 *
	 * @param array $job Job information: name, date created, etc.
	 *
	 * @return boolean True if success.
	 */
	public function add($job = false)
	{
		/**
		 * Sanitize before doing anything including sending to DB
		 */
		$clean_job = filter_var_array($job, $this->sanitizeArgs);

		if (is_array($job)) {
			global $MySQL;
			$MySQL->insert("jobs", $clean_job);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Adds a task
	 *
	 * @see MySQL::insert() MySQL::insert()
	 *
	 * @param array $job Job information: name, date created, etc.
	 *
	 * @return boolean True if success.
	 */
	public function addTask($task = false)
	{
		/**
		 * Sanitize before doing anything including sending to DB
		 */

		$clean_task = filter_var_array($task, $this->sanitizeTaskArgs);

		if (is_array($clean_task)) {
			global $MySQL;
			$MySQL->insert("tasks", $clean_task);
			return $clean_task;
		} else {
			return false;
		}
	}

	public function getAllTasks($id)
	{
		$id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
		global $MySQL;
		return $MySQL->query("SELECT * FROM ".TABLE_PREFIX."tasks WHERE job_id='$id'")->fetchAll(PDO::FETCH_ASSOC);
	}
}
