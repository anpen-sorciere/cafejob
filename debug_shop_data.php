<?php
require_once 'config/config.php';
require_once 'includes/functions.php';

echo "<h1>店舗データ確認 (ID: 21)</h1>";

try {
    // ID21の店舗データを取得
    $shop = $db->fetch(
        "SELECT s.*, p.name as prefecture_name, c.name as city_name
         FROM shops s
         LEFT JOIN prefectures p ON s.prefecture_id = p.id
         LEFT JOIN cities c ON s.city_id = c.id
         WHERE s.id = 21"
    );
    
    if ($shop) {
        echo "<h2>店舗情報</h2>";
        echo "<table border='1'>";
        echo "<tr><th>項目</th><th>値</th></tr>";
        echo "<tr><td>ID</td><td>" . $shop['id'] . "</td></tr>";
        echo "<tr><td>店舗名</td><td>" . htmlspecialchars($shop['name']) . "</td></tr>";
        echo "<tr><td>郵便番号</td><td>" . htmlspecialchars($shop['postal_code']) . "</td></tr>";
        echo "<tr><td>都道府県ID</td><td>" . $shop['prefecture_id'] . "</td></tr>";
        echo "<tr><td>都道府県名</td><td>" . htmlspecialchars($shop['prefecture_name'] ?? 'NULL') . "</td></tr>";
        echo "<tr><td>市区町村ID</td><td>" . $shop['city_id'] . "</td></tr>";
        echo "<tr><td>市区町村名</td><td>" . htmlspecialchars($shop['city_name'] ?? 'NULL') . "</td></tr>";
        echo "<tr><td>住所</td><td>" . htmlspecialchars($shop['address']) . "</td></tr>";
        echo "</table>";
        
        echo "<h2>完全な住所</h2>";
        $full_address = '';
        if (!empty($shop['postal_code'])) {
            $postal_code_str = str_pad($shop['postal_code'], 7, '0', STR_PAD_LEFT);
            $formatted_postal_code = substr($postal_code_str, 0, 3) . '-' . substr($postal_code_str, 3);
            $full_address .= '〒' . $formatted_postal_code . ' ';
        }
        if (!empty($shop['prefecture_name'])) {
            $full_address .= $shop['prefecture_name'];
        }
        if (!empty($shop['city_name'])) {
            $full_address .= $shop['city_name'];
        }
        if (!empty($shop['address'])) {
            $full_address .= $shop['address'];
        }
        echo "<p><strong>" . htmlspecialchars($full_address) . "</strong></p>";
        
    } else {
        echo "<p>ID21の店舗データが見つかりません。</p>";
    }
    
    echo "<h2>prefecturesテーブルの内容</h2>";
    $prefectures = $db->fetchAll("SELECT * FROM prefectures ORDER BY id");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>都道府県名</th></tr>";
    foreach ($prefectures as $pref) {
        echo "<tr><td>" . $pref['id'] . "</td><td>" . htmlspecialchars($pref['name']) . "</td></tr>";
    }
    echo "</table>";
    
    echo "<h2>citiesテーブルの内容（大阪府関連）</h2>";
    $cities = $db->fetchAll("SELECT c.*, p.name as prefecture_name FROM cities c LEFT JOIN prefectures p ON c.prefecture_id = p.id WHERE c.prefecture_id = (SELECT id FROM prefectures WHERE name LIKE '%大阪%') ORDER BY c.id");
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>市区町村名</th><th>都道府県名</th></tr>";
    foreach ($cities as $city) {
        echo "<tr><td>" . $city['id'] . "</td><td>" . htmlspecialchars($city['name']) . "</td><td>" . htmlspecialchars($city['prefecture_name']) . "</td></tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>エラー: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
