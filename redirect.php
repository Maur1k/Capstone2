<?php
// Simple redirect to the Laravel public directory
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/Click2Serve/Click2Serve/public';

// Remove the /Click2Serve prefix if it exists
if (strpos($request_uri, '/Click2Serve') === 0) {
    $request_uri = substr($request_uri, strlen('/Click2Serve'));
}

// Redirect to the Laravel public directory
$redirect_url = $base_path . $request_uri;
header('Location: ' . $redirect_url);
exit;
?>