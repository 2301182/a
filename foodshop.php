<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="./css/foodshop.css" type="text/css">
    <title>FoodShop</title>
</head>
<body>
<header>
    <div class="menu_top">
        <a href="foodshop.php" name="icon">
        <img src="img/menu/icon_65.png">
        </a>
    </div>
    <form method="post" class="menu_search">
        <p id="menu_search_p"><input type="text" name="search" size="40" id="menu_search_input"></p>
    </form>
    <div class="menu_cart">
        <a href="foodshop_cart.php">
        <img src="img/menu/cart.png" height="50" >
        </a>
    </div>
    <div class="menu_acc">
        <a href="foodshop_account.php">
        <img src="img/menu/account.png" height="50">
        </a>
    </div>
</header>
<br><br><br><br>
    
    <?php
        $pdo=new PDO('mysql:host=mysql311.phy.lolipop.lan;
                dbname=LAA1554150-foodshop;charset=utf8',
                'LAA1554150',
                'Pass0330');
        if(isset($_POST['search'])){
        //検索結果
            //出力関数
            function search_result($data){
                echo '<form method="post" name="detail">';
                echo '<div id="search_list">';
                echo '<p id="search_list_name">', htmlspecialchars($data['goods_name']), "</p>";
                echo '<p id="search_list_description">説明:', htmlspecialchars($data['goods_explanation']), "</p>";
                echo '<p id="search_list_img_p"><img src="', htmlspecialchars($data['goods_photo']), '" id="search_list_img"></p>';
                echo '<p id="search_list_price">値段: ', htmlspecialchars($data['price']), '</p>';
                echo '<input type="hidden" name="goods_id" value="',$data['goods_id'],'">';
                echo '<p id="search_list_button"><input type="submit" name="detail"></p>';
                echo '</div>';
                echo '</form>';
            }
            $boo = true;
            //完全一致
            $sql = $pdo -> prepare('SELECT * FROM goods WHERE goods_name LIKE ?');
            $sql -> execute([$_POST['search']]);
            $count = $sql->rowCount();
            if($count > 0){
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                search_result($data);
            }
            //部分一致
            $sql = $pdo -> prepare('SELECT * FROM goods WHERE goods_name LIKE ?');
            $code = "%".$_POST['search']."%";
            $sql -> execute([$code]);
            if($sql){
                $datalist = $sql->fetchall();
                foreach($datalist as $data){
                    $boo = false;
                    if($data['goods_name'] != $_POST['search']){
                        search_result($data);
                    }
                }
            }
            //不一致
            if($boo){
                echo '<p id="search_fail">検索したキーワードに合う商品が見つかりませんでした。</p>';
            }
        } else if(isset($_POST['detail'])){
        //商品詳細
            //商品データ参照
            $sql = $pdo -> prepare('SELECT * FROM goods WHERE goods_id = ?');
            $sql -> execute([$_POST['goods_id']]);
            $data = $sql->fetch(PDO::FETCH_ASSOC);
            //商品データ表示
            echo "<div id='detail_list'>";
            echo "<p id='detail_list_name'>", htmlspecialchars($data['goods_name']), "</p>";
            echo "<p id='detail_list_description'>説明:", htmlspecialchars($data['goods_explanation']), "</p>";
            echo "<p id='detail_list_img_p'><img src='", htmlspecialchars($data['goods_photo']) ,"' id='detail_list_img'></p>";
            echo "<p id='detail_list_price'>値段:", htmlspecialchars($data['price']), "</p>";
            echo "<p id='detail_list_buynum_p'>購入数:<input type='number' name='num' id='detail_list_buynum'></p>";
            echo "</div>";
            //レビュー表示
            echo "<div id='detail_review_list'>レビュー一覧</div><br>";
            foreach($pdo -> query('SELECT * FROM review WHERE goods_id = '.$_POST['goods_id'].' ORDER BY review_id DESC') as $row){
                //評価
                echo "<div id='detail_review'>";
                echo "<div id='detail_review_title'>", htmlspecialchars($row['review_title']),"</div>";
                echo "<div id='detail_review_assessment'>";
                for($i = 0; $i < $row['assessment']; $i++){
                    echo "★";
                }
                for($i = 0; $i < 5-$row['assessment']; $i++){
                    echo "☆";
                }
                echo "</div>";
                //内容
                echo "<div id='detail_review_sentence'>", htmlspecialchars($row['sentence']), "</div>";
                echo "<div id='detail_review_createday'>", htmlspecialchars($row['create_day']), "</div>";
                echo "</div>";
            }
            //レビュー投稿遷移
            echo '<form method="post">';
            echo '<input id="detail_review_button" type="submit" name="review" value="投稿">';
            echo '</form>';
        } else if(isset($_POST['review'])){
        //レビュー投稿
            //商品データ参照
            $sql = $pdo -> prepare('SELECT * FROM goods WHERE goods_id = ?');
            $sql -> execute([$_POST['goods_id']]);
            $data = $sql->fetch(PDO::FETCH_ASSOC);
            echo '<div id="review_post">';
            echo '<div id="review_goods_title">商品レビュー</div>';
            echo '<div id="review_goods_description">', htmlspecialchars($data['goods_explanation']) ,'</div>';
            echo '<div id="review_assessment_title">総合評価</div>';
            echo '<div id="review_assessment_star">';
            echo '<input type="radio" id="star5" name="rating" value="5">
            <label for="star5" title="5 stars"></label>
            <input type="radio" id="star4" name="rating" value="4">
            <label for="star4" title="4 stars"></label>
            <input type="radio" id="star3" name="rating" value="3">
            <label for="star3" title="3 stars"></label>
            <input type="radio" id="star2" name="rating" value="2">
            <label for="star2" title="2 stars"></label>
            <input type="radio" id="star1" name="rating" value="1">
            <label for="star1" title="1 star"></label>';
            echo '</div>';
            echo '</div>';
        } else if(isset($_POST['aa'])){
        //

        } else {
        //トップページ
            foreach($pdo -> query('SELECT * FROM goods ORDER BY goods_id DESC') as $row){
                echo '<form method="post" name="detail">';
                echo '<div id="top_list">';
                echo "<p id='top_list_name'>", htmlspecialchars($row['goods_name']), "</p>";
                echo "<p id='top_list_description'>説明:", htmlspecialchars($row['goods_explanation']), "</p>";
                echo '<p id="top_list_img_p"><img src="', htmlspecialchars($row['goods_photo']), '" id="top_list_img"></p>';
                echo "<p id='top_list_price'>値段: ", htmlspecialchars($row['price']), "</p>";
                echo '<input type="hidden" name="goods_id" value="',$row['goods_id'],'">';
                echo '<p><input id="top_list_button" type="submit" name="detail"></p>';
                echo '</div>';
                echo '</form>';
            }
        }
    ?>
</body>
</html>