<?php
/**
 * Review Helper Functions
 * These functions help generate star ratings, review statistics, and format review data
 */

/**
 * Generate HTML for star rating display
 * @param float $rating The rating value (0-5)
 * @param bool $showHalfStars Whether to show half stars
 * @return string HTML string with star icons
 */
function generateStarRating($rating, $showHalfStars = true) {
    $html = '';
    $fullStars = floor($rating);
    $hasHalfStar = $showHalfStars && ($rating - $fullStars) >= 0.5;
    
    // Generate full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fas fa-star"></i>';
    }
    
    // Generate half star if needed
    if ($hasHalfStar) {
        $html .= '<i class="fas fa-star-half-alt"></i>';
        $fullStars++; // Count half star as one star for empty stars calculation
    }
    
    // Generate empty stars
    for ($i = $fullStars; $i < 5; $i++) {
        $html .= '<i class="far fa-star"></i>';
    }
    
    return $html;
}

/**
 * Generate star rating as text (for display purposes)
 * @param float $rating The rating value (0-5)
 * @return string Text representation of stars
 */
function generateStarRatingText($rating) {
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    
    $stars = str_repeat('★', $fullStars);
    if ($hasHalfStar) {
        $stars .= '☆';
    }
    $emptyStars = str_repeat('☆', 5 - $fullStars - ($hasHalfStar ? 1 : 0));
    
    return $stars . $emptyStars;
}

/**
 * Calculate review statistics for a course
 * @param array $reviews Array of review data
 * @return array Statistics including average rating, total reviews, and rating distribution
 */
function calculateReviewStatistics($reviews) {
    if (empty($reviews)) {
        return [
            'average_rating' => 0,
            'total_reviews' => 0,
            'rating_distribution' => [
                5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0
            ],
            'stars_html' => generateStarRating(0),
            'stars_text' => generateStarRatingText(0)
        ];
    }
    
    $totalReviews = count($reviews);
    $totalRating = 0;
    $ratingDistribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    
    foreach ($reviews as $review) {
        $rating = (int)$review['rating'];
        $totalRating += $rating;
        $ratingDistribution[$rating]++;
    }
    
    $averageRating = round($totalRating / $totalReviews, 1);
    
    // Calculate percentages for rating bars
    $ratingBars = [];
    foreach ($ratingDistribution as $stars => $count) {
        $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        $ratingBars[] = [
            'label' => $stars . ' star' . ($stars > 1 ? 's' : ''),
            'width' => $percentage . '%',
            'percent' => $percentage . '%',
            'count' => $count
        ];
    }
    
    return [
        'average_rating' => $averageRating,
        'total_reviews' => $totalReviews,
        'rating_distribution' => $ratingDistribution,
        'rating_bars' => $ratingBars,
        'stars_html' => generateStarRating($averageRating),
        'stars_text' => generateStarRatingText($averageRating)
    ];
}

/**
 * Format review data for display
 * @param array $review Raw review data from database
 * @return array Formatted review data
 */
function formatReviewData($review) {
    return [
        'id' => $review['review_id'],
        'name' => htmlspecialchars($review['student_name']),
        'email' => htmlspecialchars($review['student_email'] ?? ''),
        'rating' => (int)$review['rating'],
        'text' => htmlspecialchars($review['review_text']),
        'avatar' => $review['avatar_image'] ?: 'assets/Image/avatar-1.png',
        'stars' => generateStarRatingText($review['rating']),
        'stars_html' => generateStarRating($review['rating']),
        'created_at' => $review['created_at'],
        'is_featured' => (bool)$review['is_featured']
    ];
}

/**
 * Get default avatar if none is provided
 * @param string $name Student name
 * @return string Avatar path
 */
function getDefaultAvatar($name) {
    // You can implement logic here to generate avatars based on name initials
    // For now, return a default avatar
    return 'assets/Image/avatar-1.png';
}

/**
 * Sanitize and validate review data
 * @param array $data Raw review data
 * @return array Sanitized and validated data
 */
function sanitizeReviewData($data) {
    return [
        'student_name' => trim(htmlspecialchars($data['student_name'] ?? '')),
        'student_email' => filter_var($data['student_email'] ?? '', FILTER_SANITIZE_EMAIL),
        'rating' => max(1, min(5, (int)($data['rating'] ?? 5))),
        'review_text' => trim(htmlspecialchars($data['review_text'] ?? '')),
        'avatar_image' => htmlspecialchars($data['avatar_image'] ?? ''),
        'is_approved' => (int)($data['is_approved'] ?? 1),
        'is_featured' => (int)($data['is_featured'] ?? 0)
    ];
}
?>
