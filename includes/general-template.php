<?php
function rO_header()
{
	echo '<!-- Add jQuery & UI libraries -->
		<script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<!-- Adds Font Awesome Libs -->
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link href="'.SITE_URL.'/rootOOP/includes/css/login.css" rel="stylesheet">
		<!-- Add rootOOP Ajax & Login JS -->
		<script type="text/javascript" src="'.SITE_URL.'rootOOP/includes/js/header.js"></script>';
}

function post_header($template_name = null)
{
	$name = "";
	if (isset($template_name) && filter_var($template_name, FILTER_SANITIZE_STRING))
		$name = "-" . $template_name;

	include_once THEME_PATH . "header" . $name . ".php";
}
function post_footer($template_name = null)
{
	$name = "";
	if (isset($template_name) && filter_var($template_name, FILTER_SANITIZE_STRING))
		$name = "-" . $template_name;

	include_once THEME_PATH . "footer" . $name . ".php";
}
