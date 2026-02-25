<?php
// Simple standalone script to download fonts

$fontsDir = __DIR__ . '/public/fonts';

if (!is_dir($fontsDir)) {
    mkdir($fontsDir, 0777, true);
}

function downloadFont($url, $saveTo) {
    if (file_exists($saveTo)) {
        return true;
    }
    
    // First try file_get_contents with stream context
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );  
    $content = @file_get_contents($url, false, stream_context_create($arrContextOptions));
    
    // If that fails, try cURL
    if ($content === false) {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            $content = curl_exec($ch);
            curl_close($ch);
        }
    }
    
    if ($content) {
        file_put_contents($saveTo, $content);
        return true;
    }
    
    return false;
}

$normalUrl = 'https://raw.githubusercontent.com/watsanon/font-th-sarabun-new/master/fonts/THSarabunNew.ttf';
$boldUrl = 'https://raw.githubusercontent.com/watsanon/font-th-sarabun-new/master/fonts/THSarabunNew%20Bold.ttf';

$s1 = downloadFont($normalUrl, $fontsDir . '/THSarabunNew.ttf');
$s2 = downloadFont($boldUrl, $fontsDir . '/THSarabunNew-Bold.ttf');

if ($s1 && $s2) {
    echo "SUCCESS: Fonts downloaded to $fontsDir\n";
} else {
    echo "ERROR: Could not download fonts.\n";
}
