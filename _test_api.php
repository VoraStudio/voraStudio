<?php
$url = 'http://localhost:8000/api/vorastudio-projects?locale=ca';
$token = 'UJIv45gTpMGckBdJjDg3UmkuqZzOWqHV';

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"],
    CURLOPT_TIMEOUT => 5,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
if ($error) echo "CURL Error: $error\n";
echo "Response:\n";
echo $response;
