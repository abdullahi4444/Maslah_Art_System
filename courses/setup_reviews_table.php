<?php
/**
 * Setup script for course reviews table
 * Run this script once to create the course_reviews table and insert sample data
 */

require_once __DIR__ . '/../admin/db.php';

try {
    $pdo = Database::getConnection();
    
    // Read and execute the schema
    $schema = file_get_contents(__DIR__ . '/../admin/course_reviews_schema.sql');
    
    // Split the schema into individual statements
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
        }
    }
    
    echo "✅ Course reviews table created successfully!\n";
    echo "✅ Sample data inserted successfully!\n";
    echo "\nYou can now:\n";
    echo "1. View reviews on course detail pages\n";
    echo "2. Manage reviews at: ../admin/course-reviews-manage.php\n";
    echo "3. The reviews will be displayed dynamically on course-details.php\n";
    
} catch (Exception $e) {
    echo "❌ Error setting up reviews table: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}
?>
