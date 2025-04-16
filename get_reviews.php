<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';

try {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $itemsPerPage = 6;
    $offset = ($page - 1) * $itemsPerPage;

    // Debug: Print total reviews
    $stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'approved'");
    $totalReviews = $stmt->fetchColumn();
    error_log("Total reviews: " . $totalReviews); // Add this line

    $totalPages = ceil($totalReviews / $itemsPerPage);

    // Modified query to get all reviews without status filter initially
    $stmt = $pdo->prepare("
        SELECT name, rating, review, DATE_FORMAT(date, '%M %d, %Y') as formatted_date 
        FROM reviews 
        ORDER BY date DESC 
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([$itemsPerPage, $offset]);
    $reviews = $stmt->fetchAll();

    // Debug: Print retrieved reviews
    error_log("Retrieved reviews: " . print_r($reviews, true)); // Add this line

    $formattedReviews = array_map(function($review) {
        return [
            'name' => htmlspecialchars($review['name']),
            'rating' => (int)$review['rating'],
            'review' => htmlspecialchars($review['review']),
            'date' => $review['formatted_date']
        ];
    }, $reviews);

    echo json_encode([
        'success' => true,
        'reviews' => $formattedReviews,
        'totalPages' => $totalPages,
        'currentPage' => $page
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading reviews',
        'error' => $e->getMessage()
    ]);
}
?>