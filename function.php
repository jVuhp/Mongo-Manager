<?php
if (file_exists('config.php')) {
	require_once('config.php');
} else {
	require_once('../config.php');
}
use Exception;
session_start();
require __DIR__ . '/../mito/mongodb/vendor/autoload.php';
//session_destroy();


if (isset($_SESSION['dbb_user'])) {
$client = new MongoDB\Client($_SESSION['dbb_user']['url']);
$adminDB = $client->admin;

$databaseNamesToHide = [
	//"admin", "config", "local", "dbb_mongo_status"
];
$show = [
    /*
	'unique' => [
        'columns' => ['u_user', 'license'],
    ], 
	*/
];

$dbb_mongo_status = $client->dbb_mongo_status;

function checkMongoStatus() {
	
    try {
		global $client;
        $client->listDatabases();
        $new_status = "Up";
    } catch (Exception $e) {
        $new_status = "Down";
    }

    return $new_status;
}
}

function cleanData($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$page_not_found = '<div class="page page-center">
      <div class="container-tight py-4">
        <div class="empty">
          <div class="empty-header">404</div>
          <p class="empty-title">Oops… You just found an error page</p>
          <p class="empty-subtitle text-secondary">
            We are sorry but the page you are looking for was not found
          </p>
          <div class="empty-action">
            <a href="./." class="btn btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l14 0"></path><path d="M5 12l6 6"></path><path d="M5 12l6 -6"></path></svg>
              Take me home
            </a>
          </div>
        </div>
      </div>
    </div>';
?>