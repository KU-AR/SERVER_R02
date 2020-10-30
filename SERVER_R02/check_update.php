<?php

    //データ取得、jsonに変換
    $json_string = file_get_contents('php://input');
    $json = json_decode($json_string);
    // print_r($json);

    $request_posts_updated_at = strtotime($json->{"posts_updated_at"});
    $request_spots_updated_at = strtotime($json->{"spots_updated_at"});

	//セッション開始
	session_start();

	date_default_timezone_set('UTC');

    // リクエストを受けた見学スポットのメモリーフロート取得
	try {  
		//データベース接続
		$pdo = new PDO('mysql:dbname=andolabo_arproject;host=mysql651.db.sakura.ne.jp', 'andolabo', 'andolabo-root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$pdo->query('SET NAMES utf8');

		// データベースに登録
        $stmt = $pdo->prepare('SELECT MAX(p.posts_updated_at) as posts_updated_at, MAX(s.spots_updated_at) as spots_updated_at from posts as p, spots as s');
        $stmt->execute();

		$recent_update = $stmt->fetch(PDO::FETCH_ASSOC);

		$posts_updated_at = strtotime($recent_update['posts_updated_at']);	
		$spots_updated_at = strtotime($recent_update['spots_updated_at']);
	
		if($request_posts_updated_at == $posts_updated_at){
			//最新
			$posts_update = "0";
		}
		else{
			//更新
			$posts_update = "1";
		}

		if($request_spots_updated_at == $spots_updated_at){
			//最新
			$spots_update = "0";
		}
		else{
			//更新
			$spots_update = "1";
		}
		$json = "{\"spots_update\":\"$spots_update\"," . "\"posts_update\":\"$posts_update\"}" ;
		echo $json;
	}
	catch (PDOException $e) {
        echo "Error while obtaining memory from database posts, posts_images";
		exit($e->getMessage());
    }
    
?>