<?php
/**
 * HIEU CEO - Render Health Check Endpoint
 * Returns 200 OK immediately for Render load balancers
 */
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'ok',
    'timestamp' => time(),
    'service' => 'HIEU CEO Master Portal'
]);
exit;
