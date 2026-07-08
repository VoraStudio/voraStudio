<?php
$url = 'http://localhost:8000/api/vorastudio-projects?locale=ca';
$token = 'UJIv45gTpMGckBdJjDg3UmkuqZzOWqHV';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"],
    CURLOPT_TIMEOUT => 5,
    CURLOPT_HEADER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) echo "CURL Error: $error\n";
echo "Headers:\n$headers\n";
echo "Body:\n$body\n";
