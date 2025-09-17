<?php
  session_start();

  require_once __DIR__  . '/../admin/db.php';
  require_once __DIR__  . '/../admin/helpers.php';


  // Define the project's web root from the server environment
  if (!defined('WEB_ROOT')) {
      // Assumes events/event.php is in Maslah_Art_System/events/
      // and the web root is Maslah_Art_System/
      define('WEB_ROOT', str_replace('/', DIRECTORY_SEPARATOR, dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR));
  }

  $pdo = Database::getConnection();

  // Pagination and filter constants/variables
  const EVENTS_PER_PAGE = 5; // Display 5 events per page
  $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
  $currentFilter = isset($_GET['filter']) ? $_GET['filter'] : 'upcoming'; // Default to 'upcoming'

  // Calculate offset for SQL query
  $offset = ($currentPage - 1) * EVENTS_PER_PAGE;

  // Build the WHERE clause for filtering
  $whereClause = '';
  $isUpcomingValue = null; // Variable to hold the value for :is_upcoming

  if ($currentFilter === 'upcoming') {
      $whereClause = 'WHERE is_upcoming = :is_upcoming';
      $isUpcomingValue = 1;
  } elseif ($currentFilter === 'past') {
      $whereClause = 'WHERE is_upcoming = :is_upcoming';
      $isUpcomingValue = 0;
  }

  // Query to get total number of events for pagination
  $countSql = "SELECT COUNT(*) FROM events {$whereClause}";
  $countStmt = $pdo->prepare($countSql);
  if ($isUpcomingValue !== null) {
      $countStmt->bindValue(':is_upcoming', $isUpcomingValue, PDO::PARAM_INT);
  }
  $countStmt->execute(); // Execute without arguments
  $totalEvents = $countStmt->fetchColumn();

  // Fetch events for the current page and filter
  $sql = "SELECT *, banner_path, image_path FROM events {$whereClause} ORDER BY start_datetime ASC LIMIT :limit OFFSET :offset";
  $stmt = $pdo->prepare($sql);
  if ($isUpcomingValue !== null) {
      $stmt->bindValue(':is_upcoming', $isUpcomingValue, PDO::PARAM_INT);
  }
  $stmt->bindValue(':limit', EVENTS_PER_PAGE, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute(); // Execute without arguments
  $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Filter out events without banner paths for the slider
  $bannerEvents = array_filter($events, function($e) {
      return !empty($e['banner_path']);
  });

  // Pagination info for display
  $firstResult = $offset + 1;
  $lastResult = min($offset + EVENTS_PER_PAGE, $totalEvents);
  $totalPages = ceil($totalEvents / EVENTS_PER_PAGE);

?>
<?php
// Check if this is an AJAX request for dynamic content
$is_ajax_request = isset($_GET['ajax_content']) && $_GET['ajax_content'] == 'true';

if (!$is_ajax_request): // If not an AJAX request, render full page ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Art Events</title>
  <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/event.css">
</head>
<body>
  <?php include 'includes/header.php'; ?>

    <div class="page-transition-loader" id="pageTransitionLoader">
        <div class="art-palette-loader">
            <div class="palette-color"></div>
            <div class="palette-color"></div>
            <div class="palette-color"></div>
            <div class="palette-color"></div>
            <div class="palette-color"></div>
            <div class="palette-color"></div>
            <div class="palette-color"></div>
            <div class="palette-color"></div>
        </div>
        <div class="loader-text">Loading beautiful artworks...</div>
    </div>
    <?php include 'includes/hero_slider.php'; ?>

  <?php endif; // End of full page render check ?>

  <?php // This section will be loaded dynamically via AJAX for the event list and pagination ?>

  <?php include 'includes/event_list.php'; ?>

  <?php if (!$is_ajax_request): // If not an AJAX request, render footer and scripts ?>
    <!-- Subscription Section -->
    <?php include 'includes/subscription_form.php'; ?>

    <?php include 'includes/footer.php'; ?>

  <script src="assets/js/event.js"></script>
</body>
<?php endif; // End of !$is_ajax_request check for footer and scripts ?>
</html>