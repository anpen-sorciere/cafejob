<?php
/**
 * 無料のローカル画像検証クラス
 * サーバー不要、API不要の画像検証機能
 */

class LocalImageValidator {
    
    /**
     * 不適切なファイル名のパターン
     */
    private $inappropriate_patterns = [
        // アダルト関連
        '/adult|porn|sex|nude|naked|xxx|hentai/i',
        '/nsfw|18\+|adult|erotic/i',
        '/breast|boob|ass|pussy|dick|cock/i',
        
        // 暴力的な内容
        '/violence|blood|gore|kill|murder/i',
        '/weapon|gun|knife|bomb/i',
        
        // その他の不適切な内容
        '/drug|cocaine|marijuana/i',
        '/hate|racist|discrimination/i'
    ];
    
    /**
     * 画像の基本検証
     * @param string $image_path 画像ファイルのパス
     * @param string $original_filename 元のファイル名
     * @return array 検出結果
     */
    public function validateImage($image_path, $original_filename = '') {
        $result = [
            'success' => true,
            'is_appropriate' => true,
            'warnings' => [],
            'reasons' => []
        ];
        
        // 1. ファイル名の検証
        if (ENABLE_FILENAME_VALIDATION && $original_filename) {
            $filename_check = $this->validateFilename($original_filename);
            if (!$filename_check['is_appropriate']) {
                $result['is_appropriate'] = false;
                $result['reasons'][] = '不適切なファイル名が検出されました';
                $result['warnings'] = array_merge($result['warnings'], $filename_check['warnings']);
            }
        }
        
        // 2. ファイルサイズの検証
        $size_check = $this->validateFileSize($image_path);
        if (!$size_check['is_appropriate']) {
            $result['is_appropriate'] = false;
            $result['reasons'][] = 'ファイルサイズが異常です';
            $result['warnings'] = array_merge($result['warnings'], $size_check['warnings']);
        }
        
        // 3. 画像の基本情報検証
        $image_info = $this->getImageInfo($image_path);
        if ($image_info) {
            $dimension_check = $this->validateDimensions($image_info);
            if (!$dimension_check['is_appropriate']) {
                $result['is_appropriate'] = false;
                $result['reasons'][] = '画像の寸法が異常です';
                $result['warnings'] = array_merge($result['warnings'], $dimension_check['warnings']);
            }
        }
        
        // 4. ファイル形式の詳細検証
        $format_check = $this->validateImageFormat($image_path);
        if (!$format_check['is_appropriate']) {
            $result['is_appropriate'] = false;
            $result['reasons'][] = '画像形式が不正です';
            $result['warnings'] = array_merge($result['warnings'], $format_check['warnings']);
        }
        
        return $result;
    }
    
    /**
     * ファイル名の検証
     */
    private function validateFilename($filename) {
        $result = [
            'is_appropriate' => true,
            'warnings' => []
        ];
        
        foreach ($this->inappropriate_patterns as $pattern) {
            if (preg_match($pattern, $filename)) {
                $result['is_appropriate'] = false;
                $result['warnings'][] = '不適切なファイル名: ' . $filename;
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * ファイルサイズの検証
     */
    private function validateFileSize($image_path) {
        $result = [
            'is_appropriate' => true,
            'warnings' => []
        ];
        
        $file_size = filesize($image_path);
        $max_size = 5 * 1024 * 1024; // 5MB
        $min_size = 1024; // 1KB
        
        if ($file_size > $max_size) {
            $result['is_appropriate'] = false;
            $result['warnings'][] = 'ファイルサイズが大きすぎます: ' . round($file_size / 1024 / 1024, 2) . 'MB';
        }
        
        if ($file_size < $min_size) {
            $result['is_appropriate'] = false;
            $result['warnings'][] = 'ファイルサイズが小さすぎます: ' . round($file_size / 1024, 2) . 'KB';
        }
        
        return $result;
    }
    
    /**
     * 画像の寸法検証
     */
    private function validateDimensions($image_info) {
        $result = [
            'is_appropriate' => true,
            'warnings' => []
        ];
        
        $width = $image_info['width'];
        $height = $image_info['height'];
        
        // 異常に大きな画像を拒否
        if ($width > 4000 || $height > 4000) {
            $result['is_appropriate'] = false;
            $result['warnings'][] = '画像サイズが大きすぎます: ' . $width . 'x' . $height;
        }
        
        // 異常に小さな画像を拒否
        if ($width < 50 || $height < 50) {
            $result['is_appropriate'] = false;
            $result['warnings'][] = '画像サイズが小さすぎます: ' . $width . 'x' . $height;
        }
        
        // 縦横比が極端な画像を拒否
        $ratio = $width / $height;
        if ($ratio > 10 || $ratio < 0.1) {
            $result['is_appropriate'] = false;
            $result['warnings'][] = '画像の縦横比が異常です: ' . round($ratio, 2);
        }
        
        return $result;
    }
    
    /**
     * 画像形式の検証
     */
    private function validateImageFormat($image_path) {
        $result = [
            'is_appropriate' => true,
            'warnings' => []
        ];
        
        $image_info = getimagesize($image_path);
        
        if (!$image_info) {
            $result['is_appropriate'] = false;
            $result['warnings'][] = '有効な画像ファイルではありません';
            return $result;
        }
        
        $mime_type = $image_info['mime'];
        $allowed_types = [
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
        
        if (!in_array($mime_type, $allowed_types)) {
            $result['is_appropriate'] = false;
            $result['warnings'][] = '許可されていない画像形式: ' . $mime_type;
        }
        
        return $result;
    }
    
    /**
     * 画像の基本情報を取得
     */
    private function getImageInfo($image_path) {
        $image_info = getimagesize($image_path);
        
        if (!$image_info) {
            return null;
        }
        
        return [
            'width' => $image_info[0],
            'height' => $image_info[1],
            'mime' => $image_info['mime'],
            'type' => $image_info[2]
        ];
    }
    
    /**
     * 画像が適切かどうかを総合判定
     */
    public function isImageAppropriate($image_path, $original_filename = '') {
        $result = $this->validateImage($image_path, $original_filename);
        return $result['is_appropriate'];
    }
    
    /**
     * 詳細な検証結果を取得
     */
    public function getValidationDetails($image_path, $original_filename = '') {
        return $this->validateImage($image_path, $original_filename);
    }
}
?>
