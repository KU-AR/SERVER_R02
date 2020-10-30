<?php

    //データ取得、jsonに変換
    $json_string = file_get_contents('php://input');
    $json = json_decode($json_string);
    echo $json_string;
    // print_r($json);

    $posts_nickname = $json->{"posts_nickname"};
	$posts_past_address = $json->{"posts_past_address"};
	$posts_current_address = $json->{"posts_current_address"};
	$posts_age = $json->{"posts_age"};
	$posts_job = $json->{"posts_job"};
	$posts_memory = $json->{"posts_memory"};
    $posts_time = $json->{"posts_time"};
    $posts_emotion = $json->{"posts_emotion"};
    $posts_spots_id = $json->{"posts_spots_id"};

    $posts_images_picture = $json->{"posts_picture"};
    $posts_images_bin = base64_decode($posts_images_picture);
    //echo $posts_nickname;

    //時間取得
    $date = new DateTime();
    $date = $date->format('Y-m-d H:i:s');

	//セッション開始
	session_start();

	date_default_timezone_set('UTC');

	// $time = time();

    // posts_imagesに画像情報を保存


    // postsに保存
	try {  
		//データベース接続
		$pdo = new PDO('mysql:dbname=andolabo_arproject;host=mysql651.db.sakura.ne.jp', 'andolabo', 'andolabo-root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		$pdo->query('SET NAMES utf8');

		//レコード数を取得（主キー設定のため）
		$stmt = $pdo->prepare('SELECT MAX(posts_id) as max_id FROM posts');
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$posts_id = (int)$result['max_id'] + 1;


		// データベースに登録
        $stmt = $pdo->prepare('INSERT INTO posts(posts_id, posts_updated_at, posts_created_at, posts_nickname, posts_past_address, posts_current_address, posts_age, posts_job, posts_memory, posts_time, posts_emotion, posts_spots_id) VALUES(:posts_id, :posts_updated_at, :posts_created_at, :posts_nickname, :posts_past_address, :posts_current_address, :posts_age, :posts_job, :posts_memory, :posts_time, :posts_emotion, :posts_spots_id)');
        $stmt->bindValue(':posts_id', $posts_id);
        $stmt->bindValue(':posts_updated_at', $date);
        $stmt->bindValue(':posts_created_at', $date);
        $stmt->bindValue(':posts_nickname', $posts_nickname);
        $stmt->bindValue(':posts_past_address', $posts_past_address);
        $stmt->bindValue(':posts_current_address', $posts_current_address);
        $stmt->bindValue(':posts_age', $posts_age);
        $stmt->bindValue(':posts_job', $posts_job);
        $stmt->bindValue(':posts_memory', $posts_memory);
        $stmt->bindValue(':posts_time', $posts_time);
        $stmt->bindValue(':posts_emotion', $posts_emotion);
        $stmt->bindValue(':posts_spots_id', $posts_spots_id);
        $stmt->execute();

	} catch (PDOException $e) {
        echo "Error while inserting data in database posts";
		exit($e->getMessage());
    }
    

    // posts_imagesに画像を保存
	try {  
		//データベース接続
		$pdo = new PDO('mysql:dbname=andolabo_arproject;host=mysql651.db.sakura.ne.jp', 'andolabo', 'andolabo-root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        $pdo->query('SET NAMES utf8');
        
		//レコード数を取得（主キー設定のため）
		$stmt = $pdo->prepare('SELECT MAX(posts_images_id) as max_id FROM posts_images');
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$posts_images_id = (int)$result['max_id'] + 1;


		// データベースに登録
        $stmt = $pdo->prepare('INSERT INTO posts_images(posts_images_id, posts_images_updated_at, posts_images_created_at, posts_images_bin, posts_images_posts_id) VALUES(:posts_images_id, :posts_images_updated_at, :posts_images_created_at, :posts_images_bin, :posts_images_posts_id)');
        $stmt->bindValue(':posts_images_id', $posts_images_id);
        $stmt->bindValue(':posts_images_updated_at', $date);
        $stmt->bindValue(':posts_images_created_at', $date);
        $stmt->bindValue(':posts_images_bin', $posts_images_bin);
        $stmt->bindValue(':posts_images_posts_id', $posts_id);
        $stmt->execute();

	} catch (PDOException $e) {
        echo "Error while inserting data in database posts_images";
		exit($e->getMessage());
    }
    
?>