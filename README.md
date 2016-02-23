# rootOOP

This is designed to create the safest PHP script possible by moving all vulnerable functions to one file and calling them remotely. This automatically creates a MVC environment. Think of rootOOP as an automatic Model. A wiki will be coming soon.

**This project is still in development.**

## How it does/will work?

The objective of this project is to copy this folder onto a development area and configure the `config.php` file to quickly create a simple MySQL database for Users and other projects for custom development.

## Intallation

To intall, simply copy the `rootOOP/` folder in your dev site. Then create an `index.php` and an `ajax.php` page and have them point to the `rootOOP/` folder:

Type this: `<?php include_once 'rootOOP/index.php'; ?>` onto your `index.php` page.
Type this: `<?php include_once 'rootOOP/ajax.php'; ?>` onto your `ajax.php` page.

Then use the `themes/{your-theme}` folder.

Use the `config-sample.php` file to use it ***one directory above this***.

## How it works

The best way to use this is currently by reading the script. It is now very small and can be easily understood if you have a grasp of OOP. (If you don't know what OOP means, wait for the wiki to try this.)

    $MySQL->insert('table', array('name' => 'value', 'name2' => 'value2' ... ));
    $User->authenticate(array('username' => 'posted_user', 'password' => 'posted_password'));

rootOOP will automatically filter and validate posted (raw user) input so you don't have to spend your time worring about security and spend your time developing software. All security issues should be dealt with inside the `includes/class/` folder.

## How to never worry about security:

STEP 1: Study the `rootOOP/includes/classes/` folder and all its functions.

STEP 2: Create desired controller in your `themes/{your-theme}/functions.php` folder:

	<?php
	function addTask()
	{
		if (isset($_POST['description']) && $_POST['description'] != "") {
			if (is_numeric($_POST['hours_billing']) && is_numeric($_POST['hr_rate'])) {
				$Job = new Job();
				return $Job->addTask($_POST);
			} else {
				return array("error" => "You must enter digits in hours/rate.");
			}
		} else {
			return array("error" => "You must add a taskname");
		}
	}
	?>

STEP 3: Instantiate the function through POST and/or AJAX in your `themes/{your-theme}/functions.php` folder:

`<?php $Ajax->add("addTask"); ?>` and/or `<?php $Post->add("addTask"); ?>`

STEP 4: Return your AJAX/POST in your `themes/{your-theme}/index.php` folder:

	<html>
		<head>
			<script>
			function addTask(r)
			{
				var message = "";
				if (typeof r === 'object') {
					if (typeof r.error !== 'undefined') {
						message = "Please fill all boxes to add a new task.";
					} else {
						console.log(r);
					}
				} else {
					message = "There was an error with your request. Please try again later.";
				}
				if (message.length > 0) {
					$("#message").not(":hidden", function(){ $(this).hide();});
					$("#message").addClass("red").html(message).fadeIn(400, function() {
						setTimeout(function() {
							$("#message").fadeOut(400, function() { $(this).removeClass().html(""); });
						}, 5000);
					});
				}
			}
			</script>
		</head>
	</html>

STEP 5: Add Object in Control and call it in View

**This part of the Readme is still under construction.**

(Need to explain how to instantiate the form object. How form has 3 args: id, method, ajax callback name. How if ajax callback name is available, javascript will attempt to run function with the same name as the ajax callback variable.)

## Development of rootOOP:

This is being currently developed for system administration (creating clients, managing clients, sending invoices, managing income, etc...), but can be branched to do many other things. This is the reason why rootOOP is so raw. It is intended to be lightweight and efficient. If you'd like rootOOP to manage your sales, this should be developed as a plugin.


## Help

I am a busy person who is in need of efficient code to manage my businesses. If you are out there and are willing to help me develop this code who can also envision this project (more details of the project's vision will be created in the wiki soon), feel free to contact me.
