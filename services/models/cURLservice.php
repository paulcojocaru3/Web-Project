<?php
// filepath: c:\xampp\htdocs\services\models\CurlClient.php
class CurlClient {
    private $baseUrl;
    private $timeout;
    
    public function __construct($baseUrl = 'http://localhost/services', $timeout = 30) {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
    }
    
    public function get($endpoint, $headers = []) {
        return $this->makeRequest('GET', $endpoint, null, $headers);
    }
    
    public function post($endpoint, $data = null, $headers = []) {
        return $this->makeRequest('POST', $endpoint, $data, $headers);
    }
    
    public function put($endpoint, $data = null, $headers = []) {
        return $this->makeRequest('PUT', $endpoint, $data, $headers);
    }
    
    public function delete($endpoint, $headers = []) {
        return $this->makeRequest('DELETE', $endpoint, null, $headers);
    }
    
    private function makeRequest($method, $endpoint, $data = null, $headers = []) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        $ch = curl_init();
        
        // Configurări cURL de bază
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array_merge([
                'Content-Type: application/json',
                'Accept: application/json'
            ], $headers),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        
        // Adaugă date pentru POST/PUT
        if ($data !== null && in_array($method, ['POST', 'PUT'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new Exception("cURL Error: $error");
        }
        
        return [
            'status_code' => $httpCode,
            'body' => $response,
            'data' => json_decode($response, true)
        ];
    }
}