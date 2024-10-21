<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/foodshop.css" type="text/css">
    <title>FoodShop</title>
</head>
<body>
    <div id="menu_bar">
        <a href="foodshop.php" name="icon"><img src="img/menu/icon_65.png"></a>
        <form method="post">
            <p><input id="menu_search" type="text" name="search"></p>
        </form>
        <div id="menu_warp">
            <a href="foodshop_cart.php"><img src="img/menu/cart.png" id="menu_cart"></a>
            <a href="foodshop_account.php"><img src="img/menu/account.png" id="menu_acc"></a>
        </div>
    </div>
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
                echo "<h3>", htmlspecialchars($data['goods_name']), "</h3>";
                echo "<p>説明:", htmlspecialchars($data['goods_explanation']), "</p>";
                echo '<p><img src="', htmlspecialchars($data['goods_photo']), '"style="height:80px"></p>';
                echo "<p>値段: ", htmlspecialchars($data['price']), "</p>";
                echo '<input type="hidden" name="goods_id" value="',$data['goods_id'],'">';
                echo '<p><input type="submit" name="detail"></p>';
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
                echo "検索したキーワードに合う商品が見つかりませんでした。";
            }
        } else if(isset($_POST['detail'])){
        //商品詳細
            //商品データ参照
            $sql = $pdo -> prepare('SELECT * FROM goods WHERE goods_id = ?');
            $sql -> execute([$_POST['goods_id']]);
            $data = $sql->fetch(PDO::FETCH_ASSOC);
            //商品データ表示
            echo "<h2>", htmlspecialchars($data['goods_name']), "</h2>";
            echo "<p>説明:", htmlspecialchars($data['goods_explanation']), "</p>";
            echo "<p><img src='", htmlspecialchars($data['goods_photo']) ,"' style='height:120px'></p>";
            echo "<p>値段:", htmlspecialchars($data['price']), "</p>";
            echo "<p>購入数:<input type='number' name='num'></p>";
            //レビュー表示
            echo "<h3>レビュー一覧</h3>";
            foreach($pdo -> query('SELECT * FROM review WHERE goods_id = '.$_POST['goods_id'].' ORDER BY review_id DESC') as $row){
                //評価
                echo "<p>";
                echo "<strong>", htmlspecialchars($row['review_title']),"</strong><br>";
                for($i = 0; $i < $row['assessment']; $i++){
                    echo "★";
                }
                for($i = 0; $i < 5-$row['assessment']; $i++){
                    echo "☆";
                }
                echo "<br>";
                //内容
                echo htmlspecialchars($row['sentence']), "<br>";
                echo htmlspecialchars($row['create_day']), "</p>";
            }
            //レビュー投稿遷移
            echo '<form method="post">';
            echo '<input type="submit" name="review">';
            echo '</form>';
        } else if(isset($_POST['review'])){
        //レビュー投稿
            
        } else if(isset($_POST['aa'])){
        //

        } else {
        //トップページ
            foreach($pdo -> query('SELECT * FROM goods ORDER BY goods_id DESC') as $row){
                echo '<form method="post" name="detail">';
                echo '<div id="goods_list">';
                echo "<h3>", htmlspecialchars($row['goods_name']), "</h3>";
                echo "<p>説明:", htmlspecialchars($row['goods_explanation']), "</p>";
                echo '<p><img src="', htmlspecialchars($row['goods_photo']), '"style="height:80px"></p>';
                echo "<p>値段: ", htmlspecialchars($row['price']), "</p>";
                echo '<input type="hidden" name="goods_id" value="',$row['goods_id'],'">';
                echo '<p><input type="submit" name="detail"></p>';
                echo '</div>';
                echo '</form>';
            }
        }
    ?>
</body>
</html>