<?php
/**
 * 画像内容検証クラス
 * Google Cloud Vision APIを使用してアダルトコンテンツを検出
 */

class ImageContentValidator {
    private $api_key;
    private $api_url = 'https://vision.googleapis.com/v1/images:annotate';
    
    public function __construct($api_key = null) {
        $this->api_key = $api_key ?: 'YOUR_GOOGLE_CLOUD_API_KEY';
    }
    
    /**
     * 画像のアダルトコンテンツを検出
     * @param string $image_path 画像ファイルのパス
     * @return array 検出結果
     */
    public function detectAdultContent($image_path) {
        if (!file_exists($image_path)) {
            return [
                'success' => false,
                'error' => '画像ファイルが見つかりません'
            ];
        }
        
        // 画像をBase64エンコード
        $image_data = base64_encode(file_get_contents($image_path));
        
        // APIリクエストの準備
        $request_data = [
            'requests' => [
                [
                    'image' => [
                        'content' => $image_data
                    ],
                    'features' => [
                        [
                            'type' => 'SAFE_SEARCH_DETECTION',
                            'maxResults' => 1
                        ]
                    ]
                ]
            ]
        ];
        
        // APIリクエストを送信
        $response = $this->sendApiRequest($request_data);
        
        if (!$response['success']) {
            return $response;
        }
        
        // 結果を解析
        $safe_search = $response['data']['responses'][0]['safeSearchAnnotation'] ?? null;
        
        if (!$safe_search) {
            return [
                'success' => false,
                'error' => '画像解析に失敗しました'
            ];
        }
        
        return [
            'success' => true,
            'is_adult' => $this->isAdultContent($safe_search),
            'is_violent' => $this->isViolentContent($safe_search),
            'is_racy' => $this->isRacyContent($safe_search),
            'confidence' => $this->getConfidenceLevel($safe_search),
            'details' => $safe_search
        ];
    }
    
    /**
     * APIリクエストを送信
     */
    private function sendApiRequest($data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url . '?key=' . $this->api_key);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code !== 200) {
            return [
                'success' => false,
                'error' => 'APIリクエストが失敗しました (HTTP: ' . $http_code . ')'
            ];
        }
        
        $decoded_response = json_decode($response, true);
        
        if (isset($decoded_response['error'])) {
            return [
                'success' => false,
                'error' => 'API エラー: ' . $decoded_response['error']['message']
            ];
        }
        
        return [
            'success' => true,
            'data' => $decoded_response
        ];
    }
    
    /**
     * アダルトコンテンツかどうかを判定
     */
    private function isAdultContent($safe_search) {
        $adult_likelihood = $safe_search['adult'] ?? 'UNKNOWN';
        return in_array($adult_likelihood, ['LIKELY', 'VERY_LIKELY']);
    }
    
    /**
     * 暴力的コンテンツかどうかを判定
     */
    private function isViolentContent($safe_search) {
        $violence_likelihood = $safe_search['violence'] ?? 'UNKNOWN';
        return in_array($violence_likelihood, ['LIKELY', 'VERY_LIKELY']);
    }
    
    /**
     * 性的に露骨なコンテンツかどうかを判定
     */
    private function isRacyContent($safe_search) {
        $racy_likelihood = $safe_search['racy'] ?? 'UNKNOWN';
        return in_array($racy_likelihood, ['LIKELY', 'VERY_LIKELY']);
    }
    
    /**
     * 信頼度レベルを取得
     */
    private function getConfidenceLevel($safe_search) {
        $levels = ['VERY_UNLIKELY', 'UNLIKELY', 'POSSIBLE', 'LIKELY', 'VERY_LIKELY'];
        $adult_level = $safe_search['adult'] ?? 'UNKNOWN';
        return array_search($adult_level, $levels);
    }
    
    /**
     * 画像が適切かどうかを総合判定
     */
    public function isImageAppropriate($image_path) {
        $result = $this->detectAdultContent($image_path);
        
        if (!$result['success']) {
            // APIエラーの場合は安全のため拒否
            return false;
        }
        
        // アダルト、暴力、露骨なコンテンツのいずれかが検出された場合は拒否
        return !($result['is_adult'] || $result['is_violent'] || $result['is_racy']);
    }
}
?>

