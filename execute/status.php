<?php
if (file_exists('config.php')) {
	require_once('config.php');
} else {
	require_once('../config.php');
}
function checkMongoStatus() {
	global $uri;
    try {
		if (file_exists(__DIR__ . '/../../mito/mongodb/vendor/autoload.php')) {
			require __DIR__ . '/../../mito/mongodb/vendor/autoload.php';
		} else {
			require '../../mito/mongodb/vendor/autoload.php';
		}
		$client = new MongoDB\Client($uri);
        $client->listDatabases();

		$new_status = "Up";
		$dbb_mongo_status = $client->dbb_mongo_status;
		$collection = $dbb_mongo_status->status;
		
		if (file_exists(__DIR__ . '/../views/mongo_status.txt')) {
			$txt_file = __DIR__ . '/../views/mongo_status.txt';
		} else {
			$txt_file = '../views/mongo_status.txt';
		}

		if (file_exists($txt_file) && strpos(file_get_contents($txt_file), 'Down') !== false) {
			$file_content = file_get_contents($txt_file);
			$lines = explode("\n", $file_content);

			$down_lines = array_filter($lines, function ($line) {
				return strpos($line, 'Down') !== false;
			});

			foreach ($down_lines as $line) {
				$document = [
					'status' => 'Down',
					'timestamp' => new MongoDB\BSON\UTCDateTime(str_replace('-Down', '', $line) * 1000)
				];
				$insertOneResult = $collection->insertOne($document);
			}

			file_put_contents($txt_file, '');
		}
		$document = [
			'status' => $new_status,
			'timestamp' => new MongoDB\BSON\UTCDateTime(time() * 1000)
		];
		$insertOneResult = $collection->insertOne($document);

    } catch (Exception $e) {
        $new_status = "Down";
		
		if (file_exists(__DIR__ . '/../views/mongo_status.txt')) {
			$txt_file = __DIR__ . '/../views/mongo_status.txt';
		} else {
			$txt_file = '../views/mongo_status.txt';
		}
		
        $timestamp = time();
        file_put_contents($txt_file, $timestamp . "-" . $new_status . "\n", FILE_APPEND);
    }

    return $new_status;
}
if (CHECK_STATUS) echo checkMongoStatus();

?>