<?php
// entries.php - with authentication and db connection
require_once 'auth.php';
requireAuth();
require_once __DIR__ . '/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        try {
            $stmt = $pdo->query('SELECT * FROM entries ORDER BY date DESC, id DESC');
            $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($entries);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            break;
        }

        try {
            $stmt = $pdo->prepare('
                INSERT INTO entries (date, start_time, lunch_start, lunch_end, end_time, pay_rate, paid) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            
            $stmt->execute([
                $data['date'] ?? '',
                $data['start_time'] ?? '',
                $data['lunch_start'] ?? '',
                $data['lunch_end'] ?? '', 
                $data['end_time'] ?? '',
                $data['pay_rate'] ?? 0,
                $data['paid'] ?? 0
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data or missing ID']);
            break;
        }

        try {
            $stmt = $pdo->prepare('
                UPDATE entries 
                SET date = ?, start_time = ?, lunch_start = ?, lunch_end = ?, end_time = ?, pay_rate = ?, paid = ?
                WHERE id = ?
            ');
            
            $stmt->execute([
                $data['date'],
                $data['start_time'],
                $data['lunch_start'], 
                $data['lunch_end'],
                $data['end_time'],
                $data['pay_rate'],
                $data['paid'],
                $data['id']
            ]);
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data || !isset($data['id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing entry ID']);
            break;
        }

        try {
            $stmt = $pdo->prepare('DELETE FROM entries WHERE id = ?');
            $stmt->execute([$data['id']]);
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
}
?>