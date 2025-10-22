<?php
/**
 * Azure Computer Vision API を使用した画像内容検証クラス
 * 無料枠: 月5,000リクエストまで無料
 */

class AzureImageValidator {
    private $endpoint;
    private $api_key;
    
    public function __construct($endpoint = null, $api_key = null) {
        $this->endpoint = $endpoint ?: AZURE_VISION_ENDPOINT;
        $this->api_key = $api_key ?: AZURE_VISION_KEY;
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
            'image' => $image_data
        ];
        
        // APIリクエストを送信
        $response = $this->sendApiRequest($request_data);
        
        if (!$response['success']) {
            return $response;
        }
        
        // 結果を解析
        $analysis = $response['data']['adult'] ?? null;
        
        if (!$analysis) {
            return [
                'success' => false,
                'error' => '画像解析に失敗しました'
            ];
        }
        
        return [
            'success' => true,
            'is_adult' => $analysis['isAdultContent'],
            'is_racy' => $analysis['isRacyContent'],
            'adult_score' => $analysis['adultScore'],
            'racy_score' => $analysis['racyScore'],
            'details' => $analysis
        ];
    }
    
    /**
     * APIリクエストを送信
     */
    private function sendApiRequest($data) {
        $url = $this->endpoint . 'vision/v3.2/analyze?visualFeatures=Adult';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Ocp-Apim-Subscription-Key: ' . $this->api_key
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
     * 画像が適切かどうかを総合判定
     */
    public function isImageAppropriate($image_path) {
        $result = $this->detectAdultContent($image_path);
        
        if (!$result['success']) {
            // APIエラーの場合は安全のため拒否
            return false;
        }
        
        // アダルトコンテンツまたは露骨なコンテンツが検出された場合は拒否
        // スコアが0.5以上の場合に拒否
        return !($result['is_adult'] || $result['is_racy'] || 
                $result['adult_score'] > 0.5 || $result['racy_score'] > 0.5);
    }
}
?>
