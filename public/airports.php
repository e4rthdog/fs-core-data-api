<?php
header('Content-Type: application/json');

// Load configuration (with fallback to example config if needed)
$configPath = __DIR__ . '/../config.php';
$exampleConfigPath = __DIR__ . '/../config.php.example';

// First try to load the real config, fall back to example if needed
if (file_exists($configPath)) {
    $config = include $configPath;
} elseif (file_exists($exampleConfigPath)) {
    $config = include $exampleConfigPath;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Configuration file missing']);
    exit;
}

$validApiKey = $config['api_key'];

// Check API key
$apiKey = isset($_GET['key']) ? $_GET['key'] : null;

if (!$apiKey || $apiKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode([
        'error' => 'Invalid or missing API key',
        'example' => '/airports.php?icao=OOAL&key=your_api_key'
    ]);
    exit;
}

// Get airport ICAO code from query string
$icao = isset($_GET['icao']) ? $_GET['icao'] : null;

if (!$icao) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Missing required parameter: icao',
        'example' => '/airports.php?icao=OOAL&key=your_api_key'
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