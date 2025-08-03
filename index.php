<?php

header('Content-Type: application/json');
require __DIR__ . '/vendor/autoload.php';

$apiKey = 'd84e21d4cdb12cbb6de913a4e6a1f0f3';

// GET check
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Only GET method allowed']);
    exit;
}

// Fixed values
$sitekey = "51975FD6-CDA1-4D4B-A7BB-D64A05FCED9B";
$url = "https://trial.docusign.com";
$surl = 'https://docusign-api.arkoselabs.com';
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36';

$solver = new \TwoCaptcha\TwoCaptcha($apiKey);

$maxAttempts = 5;
$attempt = 0;
$success = false;

while ($attempt < $maxAttempts) {
    try {
        $result = $solver->funcaptcha([
            'sitekey'    => $sitekey,
            'url'        => $url,
            'surl'       => $surl,
            'userAgent'  => $userAgent,
        ]);

        echo json_encode([
            'success' => true,
            'token'   => $result->code,
        ]);
        $success = true;
        break;

    } catch (\Exception $e) {
        $attempt++;
        if (strpos($e->getMessage(), 'unsolvable') !== false && $attempt < $maxAttempts) {
            sleep(2); // wait 2 seconds before retry
            continue;
        }

        // Any other error or max attempts reached
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
        ]);
        break;
    }
}
