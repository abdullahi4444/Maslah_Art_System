# Dynamic Student Reviews System

This system makes the "Student Reviews" section in `course-details.php` completely dynamic by fetching real reviews from a database. The system is designed to be flexible and ready for future admin integration.

## Features

- ✅ **Dynamic Review Display**: Reviews are fetched from the database in real-time
- ✅ **Star Rating System**: Automatic star rating generation with HTML and text formats
- ✅ **Review Statistics**: Automatic calculation of average ratings and rating distribution
- ✅ **Featured Reviews**: Support for featured reviews that appear first
- ✅ **Approval System**: Reviews can be approved/pending for moderation
- ✅ **Admin Management**: Basic admin interface for managing reviews
- ✅ **Responsive Design**: Maintains the same styling and structure
- ✅ **Database Agnostic**: Works with any database structure

## Files Created/Modified

### New Files:
1. `../admin/course_reviews_schema.sql` - Database table schema
2. `includes/review_helpers.php` - Helper functions for reviews
3. `../admin/course-reviews-manage.php` - Admin interface for managing reviews
4. `setup_reviews_table.php` - Setup script to create the table
5. `DYNAMIC_REVIEWS_README.md` - This documentation

### Modified Files:
1. `course-details.php` - Updated to fetch dynamic reviews
2. `assets/css/style.css` - Added styles for no-reviews message

## Database Schema

The `course_reviews` table includes:

```sql
CREATE TABLE course_reviews (
  review_id INT AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  student_name VARCHAR(100) NOT NULL,
  student_email VARCHAR(150) NULL,
  rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  review_text TEXT NOT NULL,
  avatar_image VARCHAR(255) NULL,
  is_approved TINYINT NOT NULL DEFAULT 1,
  is_featured TINYINT NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);
```

## Setup Instructions

### 1. Create the Database Table

Run the setup script to create the table and insert sample data:

```bash
php setup_reviews_table.php
```

Or manually execute the SQL from `../admin/course_reviews_schema.sql` in your database.

### 2. Verify the Setup

1. Visit any course detail page (e.g., `course-details.php?id=1`)
2. Check that the "Student Reviews" section displays dynamic content
3. Verify that star ratings and statistics are calculated correctly

### 3. Admin Management

Access the admin interface at: `../admin/course-reviews-manage.php`

Features:
- Add new reviews
- Edit existing reviews
- Delete reviews
- Approve/pending reviews
- Mark reviews as featured
- View all reviews in a table format

## How It Works

### 1. Data Fetching

The system fetches reviews in `course-details.php`:

```php
// Fetch course reviews
$reviewsStmt = $pdo->prepare('SELECT * FROM course_reviews WHERE course_id = ? AND is_approved = 1 ORDER BY is_featured DESC, created_at DESC');
$reviewsStmt->execute([$id]);
$reviews = $reviewsStmt->fetchAll();
```

### 2. Statistics Calculation

Review statistics are automatically calculated:

```php
$reviewStats = calculateReviewStatistics($reviews);
```

This includes:
- Average rating
- Total review count
- Rating distribution (5-star, 4-star, etc.)
- Percentage bars for each rating level

### 3. Review Formatting

Reviews are formatted for display:

```php
foreach ($reviews as $review) {
    $reviewsList[] = formatReviewData($review);
}
```

### 4. Dynamic Display

The HTML template uses the dynamic data:

```php
<?php if (!empty($course['reviews_summary']) && $course['reviews_summary']['total_reviews'] > 0): ?>
    <!-- Display reviews -->
<?php else: ?>
    <!-- Display "No reviews yet" message -->
<?php endif; ?>
```

## Helper Functions

### `generateStarRating($rating, $showHalfStars = true)`
Generates HTML star rating display using Font Awesome icons.

### `generateStarRatingText($rating)`
Generates text-based star rating (★★★★☆).

### `calculateReviewStatistics($reviews)`
Calculates comprehensive review statistics including averages and distributions.

### `formatReviewData($review)`
Formats raw database review data for display.

### `sanitizeReviewData($data)`
Sanitizes and validates review input data.

## Admin Integration

The system is designed to integrate seamlessly with your existing admin system:

1. **Database Structure**: Uses the same database connection and follows existing patterns
2. **Authentication**: Uses existing admin authentication (`includes/auth.php`)
3. **Styling**: Matches existing admin interface styling
4. **Functionality**: Provides full CRUD operations for reviews

## Future Enhancements

The system is ready for these future enhancements:

1. **User Authentication**: Link reviews to actual user accounts
2. **Review Moderation**: Advanced approval workflows
3. **Review Analytics**: Detailed reporting and analytics
4. **Email Notifications**: Notify instructors of new reviews
5. **Review Responses**: Allow instructors to respond to reviews
6. **Review Images**: Support for review attachments
7. **Review Helpfulness**: Allow users to mark reviews as helpful

## Customization

### Adding New Review Fields

1. Add the field to the database table
2. Update the `formatReviewData()` function
3. Update the admin interface form
4. Update the display template

### Changing Star Rating Display

Modify the `generateStarRating()` function to use different icons or styling.

### Custom Review Ordering

Change the ORDER BY clause in the review fetching query:

```php
// Example: Order by rating (highest first)
ORDER BY rating DESC, created_at DESC

// Example: Order by most recent
ORDER BY created_at DESC
```

## Troubleshooting

### No Reviews Displaying
1. Check if the `course_reviews` table exists
2. Verify there are reviews for the course ID
3. Check if reviews are approved (`is_approved = 1`)
4. Check database connection

### Star Ratings Not Showing
1. Ensure Font Awesome CSS is loaded
2. Check if the `generateStarRating()` function is working
3. Verify the rating values are between 1-5

### Admin Interface Not Working
1. Check admin authentication
2. Verify database permissions
3. Check for PHP errors in the admin file

## Support

The system maintains the same styling and structure as the original static reviews, ensuring seamless integration. All helper functions are well-documented and can be easily modified for specific needs.

For questions or issues, check the PHP error logs and ensure all required files are in place.
