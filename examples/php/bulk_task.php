<?php
$key = getenv('CARRIERLOOKUP_API_KEY');
if (!$key) {
    throw new Exception('Set CARRIERLOOKUP_API_KEY');
}
$curl = curl_init('https://carrierlookup.online/api/v1/bulk-tasks');
curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['X-API-Key: '.$key],
    CURLOPT_POSTFIELDS => [
        'product' => 'global_carrier_batch',
        'country' => 'US',
        'file' => new CURLFile('numbers.txt'),
    ],
]);
$response = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
if ($response === false || $status >= 300) {
    throw new Exception($response ?: curl_error($curl));
}
echo $response, PHP_EOL;
