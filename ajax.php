<?php
/**
 * Arty // Web Design and Programming
 *
 * Ajax Functionality
 *
 * @author Eric Ghiraldello <ericg@arty-web-design.com>
 */

// Loads all prereqs
require_once dirname(__FILE__).DIRECTORY_SEPARATOR."load.php";

//	Logic Page
require_once(THEME_PATH . "functions.php");

//  Processes all Ajax
$Results = $Ajax->process();

header('Content-Type: application/json');
if($Results === false) 
    $Results = json_encode(array("error" => array("date" => date("Y-m-d H:i:s"), "output" => "Failed to output ajax request. No \$Result defined.", "server" => $_SERVER)));


echo json_encode($Results); exit;
