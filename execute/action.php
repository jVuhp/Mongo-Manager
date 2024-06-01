<?php
session_start();
if (file_exists('config.php')) {
	require_once('config.php');
} else {
	require_once('../config.php');
}
if (file_exists('function.php')) {
	require_once('function.php');
} else {
	require_once('../function.php');
}

$request = $_POST['result'];
if ($request == 'paginations') {
    $page = $_POST['page'];
    $url = $_POST['url'];
    
	switch ($page) {
		case 'license':
			$pagetab = license_overview();
			break;
		case 'product':
			$pagetab = product_overview();
			break;
		default:
			echo "Unsupported page type.";
			return;
	}
	
    echo json_encode(array("html" => $pagetab));
}
if ($request == 'add_collection') {
	$table = $_POST['table'];
	
	$database = $client->$table;

	$collectionName = $_POST['collectionName'];

	$collection = $database->$collectionName;

	$collection->createIndex(['_id' => 1]);

	if(isset($_POST['fieldName'])) {
		foreach($_POST['fieldName'] as $fieldName) {
			$collection->insertOne([$fieldName => '']);
		}
	}
}

if ($request == 'login') {

	$username = cleanData($_POST['username']);
	$password = cleanData($_POST['password']);
	
	if(empty($username) || empty($password)) {
		echo 'Please enter a valid username and password.';
		exit;
	}
	try {
		$url_connect = "mongodb://" . $username . ":" . $password . "@localhost:27017";
		$client = new MongoDB\Client($url_connect);
	} catch (MongoDB\Driver\Exception\ConnectionException $e) {
		echo "Error al conectar a la base de datos: " . $e->getMessage();
		exit;
	}
	if ($client) {
		$userData = [
			'url' => $url_connect,
			'user' => $username,
			'pwd' => $password,
		];
			
		$_SESSION['dbb_user'] = $userData;
		echo 1;
	} else {
		echo 0;
		exit;
	}
}
?>