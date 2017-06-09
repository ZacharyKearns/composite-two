<?php
// start the browser session
session_start();

// load the configuration settings
require('includes/config.inc.php');

// connect to the database
require('includes/connect.inc.php');

// load useful functions
require('includes/functions.inc.php');

// array for storing error messages
$errors = array();

// set the default template to show
$template = 'users.tpl.php';

// check if we are on the home page
if (strlen($_SERVER['QUERY_STRING']) == 0) {
   // if so, set the action to a known value of home
   $_GET['action'] = 'home';
}

// route the request to the appropriate function
require('includes/router.inc.php');

// output the data in a template
include('includes/templates/' . $template);
