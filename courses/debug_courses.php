<?php
/**
 * Debug script to check what courses exist in the database
 * Run this to see what course IDs are available
 */

require_once __DIR__ . '/../admin/db.php';

try {
    $pdo = Database::getConnection();
    
    echo "<h2>Database Debug Information</h2>";
    
    // Check if courses table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'courses'");
    if ($tableCheck->rowCount() > 0) {
        echo "<h3>✅ Courses table exists</h3>";
        
        // Get all courses
        $stmt = $pdo->query('SELECT course_id, title, instructor_name, created_at FROM courses ORDER BY course_id');
        $courses = $stmt->fetchAll();
        
        if (count($courses) > 0) {
            echo "<h3>Available Courses:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>Course ID</th><th>Title</th><th>Instructor</th><th>Created</th><th>Link</th></tr>";
            
            foreach ($courses as $course) {
                echo "<tr>";
                echo "<td>" . $course['course_id'] . "</td>";
                echo "<td>" . htmlspecialchars($course['title']) . "</td>";
                echo "<td>" . htmlspecialchars($course['instructor_name']) . "</td>";
                echo "<td>" . $course['created_at'] . "</td>";
                echo "<td><a href='course-details.php?id=" . $course['course_id'] . "'>View Course</a></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<h3>❌ No courses found in database</h3>";
        }
    } else {
        echo "<h3>❌ Courses table does not exist</h3>";
    }
    
    // Check if course_reviews table exists
    $reviewsTableCheck = $pdo->query("SHOW TABLES LIKE 'course_reviews'");
    if ($reviewsTableCheck->rowCount() > 0) {
        echo "<h3>✅ Course reviews table exists</h3>";
        
        $reviewsStmt = $pdo->query('SELECT COUNT(*) as count FROM course_reviews');
        $reviewsCount = $reviewsStmt->fetch();
        echo "<p>Total reviews: " . $reviewsCount['count'] . "</p>";
    } else {
        echo "<h3>❌ Course reviews table does not exist</h3>";
        echo "<p>Run <code>php setup_reviews_table.php</code> to create it.</p>";
    }
    
    // Check course_categories table
    $categoriesTableCheck = $pdo->query("SHOW TABLES LIKE 'course_categories'");
    if ($categoriesTableCheck->rowCount() > 0) {
        echo "<h3>✅ Course categories table exists</h3>";
    } else {
        echo "<h3>❌ Course categories table does not exist</h3>";
    }
    
} catch (Exception $e) {
    echo "<h3>❌ Database Error:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
</style>
