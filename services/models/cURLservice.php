<?php
// Clasa pentru comunicarea cu API-ul aplicatiei principale
class CurlClient {
    private $baseUrl;
    
    public function __construct($baseUrl) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    // Metoda GET cu parametri optionali
    public function get($endpoint, $params = []) {
        if (!empty($params)) {
            $endpoint .= '?' . http_build_query($params);
        }
        return $this->request('GET', $endpoint);
    }
    
    // Metoda POST cu date
    public function post($endpoint, $data = null) {
        return $this->request('POST', $endpoint, $data);
    }
    
    // Metoda DELETE pentru stergerea resurselor
    public function delete($endpoint, $data = null) {
        return $this->request('DELETE', $endpoint, $data);
    }
    
    // Metoda interna care proceseaza toate tipurile de cereri
    private function request($method, $endpoint, $data = null) {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        $ch = curl_init();
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json'
            ],
            CURLOPT_HEADER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true
        ];
        
        // Adauga date in corpul cererii daca exista
        if ($data !== null) {
            $jsonData = json_encode($data);
            $options[CURLOPT_POSTFIELDS] = $jsonData;
        }
        
        curl_setopt_array($ch, $options);
        
        $response = curl_exec($ch);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $headerSize);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            return [
                'status_code' => 500,
                'data' => ['error' => $error]
            ];
        }
        
        return [
            'status_code' => $statusCode,
            'data' => json_decode($body, true)
        ];
    }
}