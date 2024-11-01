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
<!--    <form method="post" class="menu_search">
            <p id="menu_search_p"><input type="text" name="search" size="40" id="menu_search_input"></p>
        </form>     -->
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
        if(!empty($_SESSION['user_id'])){
        //ログイン者に対する処理
            if(isset($_POST['logout'])){
            //ログアウト処理
                //セッションを初期化し、ログイン情報を削除
                session_unset();
                //javascriptでリダイレクト
                ?><script type="text/javascript">
                    setTimeout("redirect()", 0);
                    function redirect(){
                        location.href = "./foodshop_account.php";
                    }
                </script><?php
            } else if(isset($_POST['modify_acc'])){
            //アカウント情報の変更･修正
                //会員情報出力
                $sql = $pdo -> prepare('SELECT * FROM user WHERE user_id = ?');
                $sql -> execute([$_SESSION['user_id']]);
                $user_data = $sql -> fetch(PDO::FETCH_ASSOC);
                echo '<form method="post">';
                echo '<div id="modify_acc">';
                echo '<div id="modify_acc_title">登録情報変更</div>';
                echo '<div id="modify_acc_name_title">氏名</div>';
                echo '<div id="modify_acc_name"><input type="text" required name="name" value="'.$user_data['user_name'].'"></div>';
                echo '<div id="modify_acc_mail_title">メールアドレス</div>';
                //メールアドレスが既に使用されていた場合
                if(isset($_POST['error_mail'])){
                    echo '<div id="modify_acc_mail_error">メールアドレスが正しくありません</div>';
                }
                echo '<div id="modofy_acc_mail"><input type="email" required name="mail" value="'.$user_data['mail_address'].'"></div>';
                echo '<div id="modify_acc_pass_title">パスワード</div>';
                echo '<div id="modify_acc_pass"><input type="password" required name="pass" value="'.$user_data['password'].'"></div>';
                echo '<div id="modify_acc_pass2_title">パスワード確認</div>';
                //パスワードとパスワード確認の入力値が一致しない場合
                if(isset($_POST['error_pass'])){
                    echo '<div id="modify_acc_pass_error">パスワードが一致しません</div>';
                }
                echo '<div id="modify_acc_pass2"><input type="password" required name="pass2"></div>';
                echo '<div id="modify_acc_address_title">住所</div>';
                echo '<div id="modify_acc_address"><input type="text" required name="address" value="'.$user_data['address'].'"></div>';
                echo '<div id="modify_acc_tel_title">電話番号</div>';
                echo '<div id="modify_acc_tel"><input type="text" required name="tel" value="'.$user_data['tel_number'].'"></div>';
                echo '<div id="modify_acc_button"><input type="submit" name="modify_acc_process" value="変更"></div>';
                echo '</div>';
                echo '</form>';
            } else if(isset($_POST['modify_acc_process'])){
            //アカウント情報の変更･修正の処理
                //メールアドレスを変更するかの確認
                $sql = $pdo -> prepare('SELECT * FROM user WHERE user_id = ?');
                $sql -> execute([$_SESSION['user_id']]);
                $user_data = $sql -> fetch(PDO::FETCH_ASSOC);
                $boo = true;
                if($user_data['mail_address'] != $_POST['mail']){
                    //新しいメールアドレスが既に使用されていないか
                    $sql = $pdo -> prepare('SELECT COUNT(*) FROM user WHERE mail_address = ?');
                    $sql -> execute([$_POST['mail']]);
                    $result = $sql -> fetch(PDO::FETCH_ASSOC);
                    if($result['COUNT(*)'] > 0){
                        //メールアドレスが既に使用されている
                        echo '<form method="post" name="error_m">';
                        echo '<input type="hidden" name="modify_acc">';
                        echo '<input type="hidden" name="error_mail">';
                        //フォーム自動送信
                        ?><script>document.error_m.submit()</script><?php
                        echo '</form>';
                        $boo = false;
                    }
                }
                //パスワードとパスワード確認が一致しているか
                if($_POST['pass'] == $_POST['pass2'] && $boo){
                    //一致      →    //電話番号のpreg_match 桁確認しないとdb桁溢れ起きる
                            //  →       パスワードの条件によるパスワード複雑化
                    $sql = $pdo -> prepare('UPDATE user SET user_name = ?, address = ?, tel_number = ?, mail_address = ?, password = ? WHERE user_id = ?');
                    $sql -> execute([$_POST['name'], $_POST['address'], $_POST['tel'], $_POST['mail'], $_POST['pass'], $_SESSION['user_id']]);
                    echo '<form method="post" name="modify_acc_success">';
                    //フォーム自動送信(リダイレクト)
                    ?><script>document.modify_acc_success.submit()</script><?php
                    echo '</form>';
                } else {
                    //不一致
                    echo '<form method="post" name="error_p">';
                    echo '<input type="hidden" name="modify_acc">';
                    echo '<input type="hidden" name="error_pass">';
                    //フォーム自動送信
                    ?><script>document.error_p.submit()</script><?php
                    echo '</form>';
                }
            } else if(isset($_POST['s'])){
            //
                //
            } else if(isset($_POST['c'])){
                //
                    //
            } else {
            //アカウントページ
                //会員情報出力
                $sql = $pdo -> prepare('SELECT * FROM user WHERE user_id = ?');
                $sql -> execute([$_SESSION['user_id']]);
                $user_data = $sql -> fetch(PDO::FETCH_ASSOC);
                echo '<div id="account">';
                echo '<div id="acc_title">会員情報</div>';
                echo '<div id="acc_info_title">基本情報<br>';
                echo '<div id="acc_info_name">',$user_data['user_name'],'</div>';
                echo '<div id="acc_info_address">',$user_data['address'],'</div>';
                echo '<div id="acc_info_tel">',$user_data['tel_number'],'</div>';
                echo '</div>';
                echo '<div id="acc_mail_title">メールアドレス<br>';
                echo '<div id="acc_mail">',$user_data['mail_address'],'</div>';
                echo '</div>';
                echo '</div>';
                //ログアウト
                echo '<form method="post">';
                echo '<div id="logout">';
                echo '<input type="submit" name="logout" value="ログアウト">';
                echo '</div>';
                echo '</form>';
                //アカウント情報の変更･修正
                echo '<form method="post">';
                echo '<div id="modify_acc">';
                echo '<input type="submit" name="modify_acc" value="変更">';
                echo '</div>';
                echo '</form>';
            }
        } else {
        //非ログイン者に対する処理
            if(isset($_POST['login_check'])){
            //ログイン処理
                $sql = $pdo -> prepare('SELECT password FROM user WHERE mail_address = ?');
                $sql -> execute([$_POST['mail']]);
                $result = $sql -> fetch(PDO::FETCH_ASSOC);
                if($result['password'] == $_POST['pword']){
                    $sql = $pdo -> prepare('SELECT user_id FROM user WHERE mail_address = ?');
                    $sql -> execute([$_POST['mail']]);
                    $result = $sql -> fetch(PDO::FETCH_ASSOC);
                    $_SESSION['user_id'] = $result['user_id'];
                } else {
                    $_SESSION['error'] = "パスワード不一致";
                }
                //javascriptでリダイレクト
                ?><script type="text/javascript">
                    setTimeout("redirect()", 0);
                    function redirect(){
                        location.href = "./foodshop_account.php";
                    }
                </script><?php
            } else if(isset($_POST['create_acc'])){
            //アカウント作成
                echo '<div id="create_acc">';
                echo '<form method="post">';
                echo '<div id="create_acc_title">新規登録</div>';
                echo '<div id="create_acc_name_title">氏名</div>';
                echo '<div id="create_acc_name"><input type="text" required name="name"></div>';
                echo '<div id="create_acc_mail_title">メールアドレス</div>';
                //メールアドレスが既に使用されていた場合
                if(isset($_POST['error_mail'])){
                    echo '<div id="create_acc_mail_error">メールアドレスが正しくありません</div>';
                }
                echo '<div id="create_acc_mail"><input type="email" required name="mail"></div>';
                echo '<div id="create_acc_pass_title">パスワード</div>';
                echo '<div id="create_acc_pass"><input type="password" required name="pass"></div>';
                echo '<div id="create_acc_pass2_title">パスワード確認</div>';
                //パスワードとパスワード確認の入力値が一致しない場合
                if(isset($_POST['error_pass'])){
                    echo '<div id="create_acc_pass_error">パスワードが一致しません</div>';
                }
                echo '<div id="create_acc_pass2"><input type="password" required name="pass2"></div>';
                echo '<div id="create_acc_address_title">住所</div>';
                echo '<div id="create_acc_address"><input type="text" required name="address"></div>';
                echo '<div id="create_acc_tel_title">電話番号</div>';
                echo '<div id="create_acc_tel"><input type="text" required name="tel"></div>';
                echo '<div id="create_acc_button"><input type="submit" name="create_acc_process" value="新規登録"></div>';
                echo '</form>';
                echo '</div>';
            } else if(isset($_POST['create_acc_process'])){
            //アカウント作成処理
                //メールアドレスが既に使用されていないか
                $sql = $pdo -> prepare('SELECT COUNT(*) FROM user WHERE mail_address = ?');
                $sql -> execute([$_POST['mail']]);
                $result = $sql -> fetch(PDO::FETCH_ASSOC);
                if($result['COUNT(*)'] == 0){
                    //パスワードとパスワード確認が一致しているか
                    if($_POST['pass'] == $_POST['pass2']){
                        //一致      →    //電話番号のpreg_match 桁確認しないとdb桁溢れ起きる
                                //  →       パスワードの条件によるパスワード複雑化
                        $sql = $pdo -> prepare('INSERT INTO user (user_name, address, tel_number, mail_address, password) VALUES (?, ?, ?, ?, ?)');
                        $sql -> execute([$_POST['name'], $_POST['address'], $_POST['tel'], $_POST['mail'], $_POST['pass']]);
                        echo '<form method="post" name="create_acc_success">';
                        echo '<input type="hidden" name="create_acc_success">';
                        //フォーム自動送信
                        ?><script>document.create_acc_success.submit()</script><?php
                        echo '</form>';
                    } else {
                        //不一致
                        echo '<form method="post" name="error_p">';
                        echo '<input type="hidden" name="create_acc">';
                        echo '<input type="hidden" name="error_pass">';
                        //フォーム自動送信
                        ?><script>document.error_p.submit()</script><?php
                        echo '</form>';
                    }
                } else {
                    //メールアドレスが既に使用されている
                    echo '<form method="post" name="error_m">';
                    echo '<input type="hidden" name="create_acc">';
                    echo '<input type="hidden" name="error_mail">';
                    //フォーム自動送信
                    ?><script>document.error_m.submit()</script><?php
                    echo '</form>';
                }
            } else if(isset($_POST['create_acc_success'])){
            //アカウント作成完了画面
                echo '<div id="create_acc_success">';
                echo '<div id="create_acc_success_title">登録完了</div>';
                echo '<div id="create_acc_success_text">';
                echo 'アカウントの登録が完了しました。<br>';
                echo '引き続きお買い物をお楽しみください。';
                echo '</div>';
                echo '<div id="create_acc_success_btn"><button onclick="location.href='."'./foodshop.php'".'">トップページへ</button></div>';
                echo '</div>';
            } else {
            //ログインページ
                echo '<div id="login">';
                echo '<form method="post">';
                echo '<div id="login_title">ログイン</div>';
                echo '<div id="login_email_title">メールアドレス</div>';
                echo '<div id="login_email_input"><input type="email" required name="mail"></div>';
                echo '<div id="login_pword_title">パスワード</div>';
                echo '<div id="login_pword_input"><input type="password" required name="pword"></div>';
                if(isset($_SESSION['error'])){
                    echo '<div id="login_fail">'.$_SESSION['error'].'</div>';
                    $_SESSION['error'] = "";
                }
                echo '<div id="login_form_btn"><input type="submit" name="login_check" value="ログイン"></div>';
                echo '</form>';
                echo '<form method="post">';
                echo '<div id="login_create_account"><input type="submit" name="create_acc" value="アカウントを新規作成"></div>';
                echo '</form>';
                echo '</div>';
            }
        }
    ?>
</body>
</html>