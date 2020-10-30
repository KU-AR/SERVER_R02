<?php
	function make_json_txt($spots_id, $count, $json, $cnt, $cnt_max)
	{

		$json_head = "{\"Memory\":{";
		$json_posts_head = "\"posts\":{";
		$json_posts_main = "\"$cnt\":{" .
			"\"posts_spots_id\":\"$spots_id\"," .
			"\"count\":\"$count\"" .
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

	//セッション開始
	session_start();

	date_default_timezone_set('Asia/Tokyo');

	// $time = time();

    // posts_imagesに画像情報を保存


    // 見学スポットごとのメモリーフロート数を取得
	try {  
		//データベース接続
		$pdo = new PDO('mysql:dbname=andolabo_arproject;host=mysql651.db.sakura.ne.jp', 'andolabo', 'andolabo-root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$pdo->query('SET NAMES utf8');

		// クエリ
        $stmt = $pdo->prepare('SELECT p.posts_spots_id, COUNT(p.posts_spots_id) as count from posts as p GROUP BY p.posts_spots_id ORDER BY p.posts_spots_id asc');
        $stmt->execute();

		$posts_all = $stmt->fetchALL(PDO::FETCH_ASSOC);

		$json = "";
		$cnt = 1;
		$cnt_max = count($posts_all);
		foreach($posts_all as $posts){
			$posts_spots_id = $posts['posts_spots_id'];
			$count = $posts['count'];
			$json = make_json_txt($posts_spots_id, $count, $json, $cnt, $cnt_max);
			$cnt++;
		}
		echo $json;

	} catch (PDOException $e) {
        echo "Error while obtaining memory count from database posts";
		exit($e->getMessage());
    }
    
?>
