<?php
// セッションを完全にクリア
session_start();
session_destroy();
session_start();

echo "<h1>セッションクリア完了</h1>";
echo "<p>セッションがクリアされました。</p>";
echo "<p><a href='../?page=admin_login'>システム管理者ログイン</a></p>";
echo "<p><a href='../?page=shop_login'>店舗管理者ログイン</a></p>";
echo "<p><a href='../'>サイトトップ</a></p>";
?>
