<?php

	function make_json_txt($id, $updated_at, $created_at, $name, $ruby, $description, $latitude, $longitude, $images_bin, $json, $cnt, $cnt_max)
	{
		$json_head = "{\"Spot_all\":{";
		$json_spots_head = "\"spots\":{";
		$json_spots_main = "\"$cnt\":{" .
			"\"spots_id\":\"$id\"," .
			"\"spots_updated_at\":\"$updated_at\"," .
			"\"spots_created_at\":\"$created_at\"," .
			"\"spots_name\":\"$name\"," .
			"\"spots_ruby\":\"$ruby\"," .
			"\"spots_description\":\"$description\"," .
			"\"spots_latitude\":\"$latitude\"," .
			"\"spots_longitude\":\"$longitude\"," .
			"\"spots_images_bin\":\"$images_bin\"" .
		"}";
		$json_spots_continue = ",";
		$json_spots_tail = "}";
		$json_spots_all_tail = "}";
		$json_tail = "}";
		
		//初回
		if($json == ""){
			$json = $json_head . $json_spots_head;
		}

		//spots追加
		$json = $json . $json_spots_main;

		//次がある
		if($cnt < $cnt_max){
			$json = $json . $json_spots_continue;
		}
		//最後
		else{
			$json = $json . $json_spots_tail . $json_spots_all_tail . $json_tail;
		}
		return $json;
	}

	//セッション開始
	session_start();

	date_default_timezone_set('UTC');

	// $time = time();


    // spots及びspots_imagesからデータ取得
	try {  
		//データベース接続
		$pdo = new PDO('mysql:dbname=andolabo_arproject;host=mysql651.db.sakura.ne.jp', 'andolabo', 'andolabo-root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$pdo->query('SET NAMES utf8');

		//レコード数を取得（主キー設定のため）
		$stmt = $pdo->prepare('SELECT MAX(spots_id) as max_id FROM spots');
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$spot_num = (int)$result['max_id'];


		// 全データ取得
        $stmt = $pdo->prepare('SELECT s.spots_id,s.spots_updated_at,s.spots_created_at,s.spots_name,s.spots_ruby,s.spots_description, s.spots_latitude, s.spots_longitude, si.spots_images_bin FROM spots as s,spots_images as si WHERE s.spots_image_id=si.spots_images_image_id');
        $stmt->execute();
		$spots_all = $stmt->fetchALL(PDO::FETCH_ASSOC);
		
		// 位置及びbinを自動json化不可
		// print_r($spots_all[0]);
		// $json = json_encode($spots_all[0], JSON_UNESCAPED_UNICODE);
		// var_dump($json);
		//json変換
		// $json = json_encode($spots_all, JSON_UNESCAPED_UNICODE);
		// print_r($json);
		// var_dump($json);
		// echo $json;

		//出力、手動でjson化
		// String json_spots_template = "{" +
		// 	"\"spots\":{" +
		// 		"\"$cnt\":{" +
		// 			"\"spots_id\":\"$spots_id\"," +
		// 			"\"spots_name\":\"$spots_name\"," +
		// 			"\"spots_description\":\"$spots_description\"," +
		// 			"\"spots_images_bin\":\"$spots_images_bin\"" +
		// 		"}"
		// 		"\"cnt\":{" +
		// 			"\"spots_id\":\"$spots_id\"," +
		// 			"\"spots_name\":\"$spots_name\"," +
		// 			"\"spots_description\":\"$spots_description\"," +
		// 			"\"spots_images_bin\":\"$spots_images_bin\"," +
		// 		"}," +
		// 	"}" +
		// "}";

		$json = "";
		$cnt = 1;
		$cnt_max = count($spots_all);
		foreach($spots_all as $spots){
			$spots_id = $spots['spots_id'];
			$spots_updated_at = $spots['spots_updated_at'];
			$spots_created_at = $spots['spots_created_at'];
			$spots_name = $spots['spots_name'];
			$spots_ruby = $spots['spots_ruby'];
			$spots_description = $spots['spots_description'];
			$spots_latitude = $spots['spots_latitude'];
			$spots_longitude = $spots['spots_longitude'];
			$spots_images_bin = base64_encode($spots['spots_images_bin']);
			$json = make_json_txt($spots_id, $spots_updated_at, $spots_created_at, $spots_name, $spots_ruby, $spots_description, $spots_latitude, $spots_longitude, $spots_images_bin, $json, $cnt, $cnt_max);
			$cnt++;
		}
		echo $json;
		// $test = json_encode($json);
		// print_r($test);
		// var_dump($test->{"spots_name"});
		// var_dump($json);

	} catch (PDOException $e) {
        echo "Error while fetching data from database spots";
		exit($e->getMessage());
    }
    

    
?>