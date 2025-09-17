<?php // This section will be loaded dynamically via AJAX for the event list and pagination ?>

<!-- Events Tabs -->
<center>
  <div class="events-tabs">
    <button class="tab <?php echo ($currentFilter === 'upcoming') ? 'active' : ''; ?>" data-filter="upcoming">Upcoming Event</button>
    <button class="tab <?php echo ($currentFilter === 'past') ? 'active' : ''; ?>" data-filter="past">Past Event</button>
  </div>
</center>

<div id="eventsContainer" class="containers">
  <?php if (!empty($events)): ?>
      <?php foreach ($events as $e): ?>
          <div class="card" style="border-radius: 20px;" data-type="<?php echo $e['is_upcoming'] ? 'upcoming' : 'past'; ?>">
              <?php
              $displayImage = '../../admin/assets/Assets/Image/event-defualt.jpg'; // Default placeholder image for events/images/
              $altText = 'Event Thumbnail'; // Default alt text

              $dbImagePath = $e['image_path'];

              // Correctly construct the full relative path from project root
              $fullRelativeImagePath = 'admin/' . $dbImagePath;

              // Constructing absolute file system path for file_exists() check
              // This path should be directly accessible by the server's file system
              $fsImagePath = WEB_ROOT . str_replace('/', DIRECTORY_SEPARATOR, $fullRelativeImagePath);

              // Use thumbnail for event list cards if available and file exists
              if (!empty($dbImagePath) && file_exists($fsImagePath)) {
                  // Web path relative to events/event.php
                  // $dbImagePath already starts with 'admin/', so we just need to go up one level from 'events/'
                  $displayImage = '../' . htmlspecialchars($fullRelativeImagePath);
              }

              ?>
              <img src="<?php echo $displayImage; ?>" alt="<?php echo $altText; ?>" class="image">
              <div class="info">
                  <div class="meta"><img src="../events/assets/Icons/icon-5.svg" alt=""> <?php echo date('j. M - Y', strtotime($e['start_datetime'])); ?> • <img src="../events/assets/Icons/icon-7.svg" alt=""> <?php echo date('g:i A', strtotime($e['start_datetime'])) . ' - ' . date('g:i A', strtotime($e['end_datetime'])); ?></div>
                  <div class="title"><?php echo htmlspecialchars($e['title']); ?></div>
                  <div class="location"><img src="../events/assets/Icons/icon-6.svg" alt=""> <?php echo htmlspecialchars($e['location_name']); ?></div>
              </div>
              <div class="action">
                  <a class="touch-btn" href="#subscriptionForm">Get In Touch</a>
              </div>
          </div>
      <?php endforeach; ?>
  <?php else: ?>
      <div class="no-events-message" style="display:flex; flex-direction: column; justify-content: center; align-items: center;">
          <img src="../admin/assets/Assets/Icon/fsearch.svg" alt="No events found" style="width: 50px; height: 50px; margin-bottom: 15px;"> 
          <p style="font-size: 1.2rem; color: #777;">No events found.</p>
      </div>
  <?php endif; ?>
</div>

<!-- Pagination -->
<div class="pagination-wrapper">
    <div class="pagination" id="paginationControls">
      <?php if ($totalPages > 1): ?>
          <button <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?> data-page="<?php echo $currentPage - 1; ?>" data-filter="<?php echo htmlspecialchars($currentFilter); ?>">&lt;</button>

          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <button class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>" data-page="<?php echo $i; ?>" data-filter="<?php echo htmlspecialchars($currentFilter); ?>"><?php echo $i; ?></button>
          <?php endfor; ?>

          <button <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?> data-page="<?php echo $currentPage + 1; ?>" data-filter="<?php echo htmlspecialchars($currentFilter); ?>">&gt;</button>
      <?php endif; ?>
    </div> 
</div>