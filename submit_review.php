<?php
// Prevent any output before headers
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';

try {
    // Validate input
    if (empty($_POST['name']) || empty($_POST['rating']) || empty($_POST['review'])) {
        throw new Exception('All fields are required');
    }

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT);
    $review = filter_var($_POST['review'], FILTER_SANITIZE_STRING);

    // Validate rating range
    if ($rating < 1 || $rating > 5) {
        throw new Exception('Invalid rating value');
    }

    // Insert review
    $stmt = $pdo->prepare("INSERT INTO reviews (name, rating, review) VALUES (?, ?, ?)");
    $result = $stmt->execute([$name, $rating, $review]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully'
        ]);
    } else {
        throw new Exception('Failed to insert review');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>