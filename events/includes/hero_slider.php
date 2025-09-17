<section class="art-header">
    <div class="overlay">
      <h1>Art Events</h1>
      <p>Home &nbsp; >> &nbsp; <span>Events</span></p>
    </div>
  </section>
  <?php if (!empty($bannerEvents)): ?>
  <!-- Hero Banner Slider -->
  <div class="event-slider-wrapper">
    <?php foreach ($bannerEvents as $idx => $bannerEvent): ?>
      <?php
      $bannerDisplayPath = '../admin/assets/Assets/Image/event-defualt.jpg'; // Default placeholder
      $dbBannerPath = $bannerEvent['banner_path'];
      $fullRelativeBannerPath = 'admin/' . $dbBannerPath;
      $fsBannerPath = WEB_ROOT . str_replace('/', DIRECTORY_SEPARATOR, $fullRelativeBannerPath);
      if (!empty($dbBannerPath) && file_exists($fsBannerPath)) {
          $bannerDisplayPath = '../' . htmlspecialchars($fullRelativeBannerPath);
      }
      ?>
      <div class="event-image-slide <?php echo $idx === 0 ? 'active' : ''; ?>" style="background-image: linear-gradient(to right,rgba(0,0,0,0.7), rgba(82,78,78,0.7)), url('<?php echo $bannerDisplayPath; ?>');">
          <span class="upcoming-label">Upcoming Event</span>
          <div class="event-details">
              <div class="event-date"><i class="fa-regular fa-calendar"></i> <?php echo date('M j, Y', strtotime($bannerEvent['start_datetime'])) . '–' . date('M j, Y', strtotime($bannerEvent['end_datetime'])); ?></div>
              <div class="event-title"><?php echo htmlspecialchars($bannerEvent['title']); ?></div>
              <div class="event-location"><i class="fa-solid fa-location-dot"></i> <?php echo htmlspecialchars($bannerEvent['location_name']); ?></div>
              <a href="#subscriptionForm" class="join-btn">Join now</a>
          </div>
      </div>
    <?php endforeach; ?>
    <?php if (count($bannerEvents) > 1): ?>
      <div class="arrows">
         <button class="arrow-btn" id="prevBtn"><i class="fa-solid fa-arrow-left"></i></button>
         <button class="arrow-btn" id="nextBtn"><i class="fa-solid fa-arrow-right"></i></button>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>