<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        if(isset($_POST['cart_rem'])){
            $i = 0;
            foreach($_SESSION['cart'] as $row){
                //削除対象の商品を探索
                if($row['goods_id'] == $_POST['goods_id']){
                    //削除対象の商品が見つかった場合
                    array_splice($_SESSION['cart'], $i, 1);
                }
                $i++;
            }
            //リダイレクトによるカート一覧自動遷移
            ?><script type="text/javascript">
            setTimeout("redirect()", 0);
            function redirect(){
                location.href = "./foodshop_cart.php";
            }
        </script><?php
        } else if(isset($_POST['order'])){
        //注文手続き
            //カート表示
            foreach($_SESSION['cart'] as $row){
                $sql = $pdo -> prepare('SELECT * FROM goods WHERE goods_id = ?');
                $sql -> execute([$row['goods_id']]);
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                echo '<div id="order_cart_list">';
                echo "<div id='order_cart_list_name'>", htmlspecialchars($data['goods_name']), "</div>";
                echo "<div id='order_cart_list_description'>説明:", htmlspecialchars($data['goods_explanation']), "</div>";
                echo '<div id="order_cart_list_img_div"><img src="', htmlspecialchars($data['goods_photo']), '" id="order_cart_list_img"></div>';
                echo "<div id='order_cart_list_price'>値段: ", htmlspecialchars($data['price']), "</div>";
                echo '<div id="order_cart_list_num">購入数: ', htmlspecialchars($row['num']) ,'</div>';
                echo '<input type="hidden" name="goods_id" value="',$data['goods_id'],'">';
                echo '</div>';
            }
            //支払方法
            echo '<form method="post">';
            echo '<div id="payment">';
            echo '<div id="payment_title">支払方法</div>';
            echo '<div id="payment_cash"><input type="radio" required name="payment_method" value="cash">現金</div>';
            echo '<div id="payment_credit"><input type="radio" name="payment_method" value="credit">クレジットカード</div>';
            echo '<div id="payment_convenience"><input type="radio" name="payment_method" value="convenience">コンビニ決済</div>';
            echo '<div id="payment_electrical"><input type="radio" name="payment_method" value="electrical">電子決済</div>';
            echo '</div>';
            //送り先入力
            //非ログイン者のみ表示
            if(empty($_SESSION['user_id'])){
                echo '<div id="input_address">';
                echo '<div id="input_address_title">送り先入力</div>';
                echo '<div id="input_address_name_title">名前：</div>';
                echo '<div id="input_address_name"><input type="text" name="name" required></div>';
                echo '<div id="input_address_address_title">住所：</div>';
                echo '<div id="input_address_address"><input type="text" name="address" required></div>';
                echo '<div id="input_address_tel_title">電話番号：</div>';
                echo '<div id="input_address_tel"><input type="text" name="tel" required></div>';
                echo '</div>';
            }
            echo '<div id="order_submit"><input type="submit" name="order_process" value="確定"></div>';
            echo '</form>';
        } else if(isset($_POST['order_process'])){
        //注文処理
            //処理
                //支払方法($_POST['payment_method'])
                //ログイン済： ユーザー情報($_SESSION['user_id'])からuserデータベース参照
                //非ログイン： 名前($_POST['name'])、住所($_POST['address'])、電話番号($_POST['tel'])から参照
            //カートリセット
            unset($_SESSION['cart']);
            //注文完了表示
            echo '<div id="order_success">';
            echo '<div id="order_success_title">注文完了</div>';
            echo '<div id="order_success_text">';
            echo '商品注文が完了しました。<br>';
            echo '引き続きお買い物をお楽しみください。';
            echo '</div>';
            echo '<div id="order_success_btn"><button onclick="location.href='."'./foodshop.php'".'">トップページへ</button></div>';
            echo '</div>';
        } else {
        //カート一覧
            //一覧表示
            echo '<div id="cart">カート一覧</div>';
            foreach($_SESSION['cart'] as $row){
                $sql = $pdo -> prepare('SELECT * FROM goods WHERE goods_id = ?');
                $sql -> execute([$row['goods_id']]);
                $data = $sql->fetch(PDO::FETCH_ASSOC);
                echo '<form method="post" action="foodshop.php">';
                echo '<div id="cart_list">';
                echo "<div id='cart_list_name'>", htmlspecialchars($data['goods_name']), "</div>";
                echo "<div id='cart_list_description'>説明:", htmlspecialchars($data['goods_explanation']), "</div>";
                echo '<div id="cart_list_img_div"><img src="', htmlspecialchars($data['goods_photo']), '" id="cart_list_img"></div>';
                echo "<div id='cart_list_price'>値段: ", htmlspecialchars($data['price']), "</div>";
                echo '<div id="cart_list_num">購入数: ', htmlspecialchars($row['num']) ,'</div>';
                echo '<input type="hidden" name="goods_id" value="',$data['goods_id'],'">';
                echo '<div id="cart_list_btn"><input id="cart_list_button" type="submit" name="detail" value="詳細"></div>';
                echo '</form>';
                echo '<form method="post">';
                echo '<input type="hidden" name="goods_id" value="',$data['goods_id'],'">';
                echo '<div id="cart_list_rem"><input id="cart_list_remove" type="submit" name="cart_rem" value="削除"></div>';
                echo '</div>';
                echo '</form>';
            }
            //注文ボタン
            if(!empty($_SESSION['cart'])){
                echo '<form method="post">';
                echo '<div id="cart_order_btn"><input id="cart_order_button" type="submit" name="order" value="注文"></div>';
                echo '</form>';
            } else {
                echo 'カートには何も入っていません';
            }
        }
    ?>
</body>
</html>