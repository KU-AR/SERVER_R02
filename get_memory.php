<?php
	function make_json_txt($id, $spots_id, $updated_at, $created_at, $nickname, $past_address, $current_address, $age, $job, $memory, $time, $emotion, $images_bin, $json, $cnt, $cnt_max)
	{

		$json_head = "{\"Post_all\":{";
		$json_posts_head = "\"posts\":{";
		$json_posts_main = "\"$cnt\":{" .
			"\"posts_id\":\"$id\"," .
			"\"posts_spots_id\":\"$spots_id\"," .
			"\"posts_updated_at\":\"$updated_at\"," .
			"\"posts_nickname\":\"$nickname\"," .
			"\"posts_past_address\":\"$past_address\"," .
			"\"posts_current_address\":\"$current_address\"," .
			"\"posts_age\":\"$age\"," .
			"\"posts_job\":\"$job\"," .
			"\"posts_memory\":\"$memory\"," .
			"\"posts_time\":\"$time\"," .
			"\"posts_emotion\":\"$emotion\"," .
			"\"posts_images_bin\":\"$images_bin\"" .
		"}";
		$json_posts_continue = ",";
		$json_posts_tail = "}";
		$json_posts_all_tail = "}";
		$json_tail = "}";
		
		//初回
		if($json == ""){
			$json = $json_head . $json_posts_head;
		}

		//posts追加
		$json = $json . $json_posts_main;

		//次がある
		if($cnt < $cnt_max){
			$json = $json . $json_posts_continue;
		}
		//最後
		else{
			$json = $json . $json_posts_tail . $json_posts_all_tail . $json_tail;
		}
		return $json;
	}

    //データ取得、jsonに変換
    $json_string = file_get_contents('php://input');
    $json = json_decode($json_string);
    // print_r($json);

    $request_spots_id = $json->{"spots_id"};

	//セッション開始
	session_start();

	date_default_timezone_set('UTC');

	// $time = time();

    // posts_imagesに画像情報を保存


    // リクエストを受けた見学スポットのメモリーフロート取得
	try {  
		//データベース接続
		$pdo = new PDO('mysql:dbname=andolabo_arproject;host=mysql651.db.sakura.ne.jp', 'andolabo', 'andolabo-root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$pdo->query('SET NAMES utf8');

		// データベースに登録
        $stmt = $pdo->prepare('SELECT p.posts_id, p.posts_spots_id, p.posts_updated_at, p.posts_created_at, p.posts_nickname, p.posts_past_address, p.posts_current_address, p.posts_age, p.posts_job, p.posts_memory, p.posts_time, p.posts_emotion, pi.posts_images_bin from posts as p, posts_images as pi WHERE p.posts_id=pi.posts_images_posts_id AND p.posts_spots_id=:spots_id');
        $stmt->bindValue(':spots_id', $request_spots_id);
        $stmt->execute();

		$posts_all = $stmt->fetchALL(PDO::FETCH_ASSOC);

		$json = "";
		$cnt = 1;
		$cnt_max = count($posts_all);
		foreach($posts_all as $posts){
			$posts_id = $posts['posts_id'];
			$posts_spots_id = $posts['posts_spots_id'];
			$posts_updated_at = $posts['posts_updated_at'];
			$posts_created_at = $posts['posts_created_at'];
			$posts_nickname = $posts['posts_nickname'];
            $posts_past_address = $posts['posts_past_address'];
			$posts_current_address = $posts['posts_current_address'];
            $posts_age = $posts['posts_age'];
			$posts_job = $posts['posts_job'];
			$posts_memory = $posts['posts_memory'];
			$posts_time = $posts['posts_time'];
			$posts_emotion = $posts['posts_emotion'];
			$posts_images_bin = base64_encode($posts['posts_images_bin']);
			$json = make_json_txt($posts_id, $posts_spots_id, $posts_updated_at, $posts_created_at, $posts_nickname, $posts_past_address, $posts_current_address, $posts_age, $posts_job, $posts_memory, $posts_time, $posts_emotion, $posts_images_bin, $json, $cnt, $cnt_max);
			$cnt++;
		}
		echo $json;

	} catch (PDOException $e) {
        echo "Error while obtaining memory from database posts, posts_images";
		exit($e->getMessage());
    }
    
?>