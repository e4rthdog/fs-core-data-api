<?php
header('Content-Type: application/json');
if (
    isset($_SERVER['HTTP_ORIGIN']) &&
    $_SERVER['HTTP_ORIGIN'] === 'http://localhost:7777'
) {
    header('Access-Control-Allow-Origin: http://localhost:7777');
}

// Load configuration
$configPath = __DIR__ . '/../config.php';

// Check if config file exists
if (!file_exists($configPath)) {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration file missing']);
    exit;
}

// Load config
$config = include $configPath;

// Rate limiting: configurable requests per minute per IP
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateFile = sys_get_temp_dir() . "/api_rate_" . md5($ip);
$limit = $config['rate_limit_per_minute'] ?? 60;
$now = time();
$requests = [];
if (file_exists($rateFile)) {
    $requests = json_decode(file_get_contents($rateFile), true) ?: [];
    $requests = array_filter($requests, fn($t) => $t > $now - 60);
}
$requests[] = $now;
file_put_contents($rateFile, json_encode($requests));
if (count($requests) > $limit) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests']);
    exit;
}

// Get airport ICAO code from query string
$icao = isset($_GET['icao']) ? $_GET['icao'] : null;

if (!$icao) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Missing required parameter: icao',
        'example' => '/airports.php?icao=OOAL'
    ]);
    exit;
}

// Sanitize input (simple version)
$icao = preg_replace('/[^A-Z0-9]/', '', strtoupper($icao));

// Connect to SQLite database
$dbPath = __DIR__ . '/../data/airports.db';
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare and execute query
    $stmt = $db->prepare('SELECT airport_icao, runway, heading_degrees, airport_name FROM airport_runways WHERE airport_icao = ? LIMIT 1');
    $stmt->execute([$icao]);
    $firstResult = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$firstResult) {
        http_response_code(404);
        echo json_encode([
            'error' => 'No data found for ICAO: ' . $icao
        ]);
        exit;
    }

    // Get airport name from first result
    $airportName = $firstResult['airport_name'];
    
    // Now get all runway data
    $stmt = $db->prepare('SELECT airport_icao, runway, heading_degrees FROM airport_runways WHERE airport_icao = ?');
    $stmt->execute([$icao]);
    $runways = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    $response = [
        'airport_icao' => $icao,
        'airport_name' => $airportName,
        'runways' => array_map(function($row) {
            return [
                'runway' => $row['runway'],
                'heading_degrees' => (int)$row['heading_degrees']
            ];
        }, $runways)
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error',
        'message' => 'Could not retrieve runway data'
    ]);
    
    // Log the actual error (not visible to API consumers)
    error_log('Database error in airports.php: ' . $e->getMessage());
}