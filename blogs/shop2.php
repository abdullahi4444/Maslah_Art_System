<?php include 'Includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Discover & Shop Unique Art</title>
  <style>
        .navbar-center {
            flex: 1;
            margin-top: 10px;
            margin-bottom: -10px;
        }
        .footer h2, .footer h3 {
            color: #040007;
            margin-bottom: 16px;
            font-size: 20px;
            font-weight: bold;
        }
  </style>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Custom Theme -->
  <link rel="stylesheet" href="">
</head>
<body class="custom-body">

  <!-- Hero -->
  <section class="text-white py-5 hero-section">
    <div class="container">
      <div class="col-md-9">
        <h1 class="display-4 fw-bold">Discover & Shop Unique Art</h1>
        <div class="col-md-5">
        <p class="lead">Buy beautiful and original artworks made by talented Somali artists.</p>
        <a href="#shop" class="btn btn-primary btn-lg rounded-pill">Start Shopping</a>
      </div>
    </div>
  </section>
  
  <!-- Shop -->
  <section id="shop" class="container py-5">
  <h2 class="text-center text-dark fw-bold mb-4">Shop Authentic Creations by Somali Artists</h2>
  
  <!-- Search + Filters -->
    <div class="mb-5">
      <div class="search-wrapper mb-3 ms-auto">
        <input type="text" id="searchInput" class="form-control search-input" placeholder="Search artworks...">
        <i class="fa fa-search search-icon-right"></i>
      </div>
      <div class="d-flex flex-wrap justify-content-center gap-2">
        <button class="btn btn-outline-primary filter-btn active" data-filter="all">All</button>
        <button class="btn btn-outline-primary filter-btn" data-filter="price">Price</button>
        <button class="btn btn-outline-primary filter-btn" data-filter="creativity">Creativity</button>
        <button class="btn btn-outline-primary filter-btn" data-filter="techniques">Techniques</button>
      </div>
    </div>

    <!-- Shopping Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cartModalLabel">Your Shopping Cart</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="cartItems" class="mb-3">
              <!-- Cart items will be displayed here -->
              <p class="text-center" id="emptyCartMessage">Your cart is empty</p>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5>Total: <span id="cartTotal">$0.00</span></h5>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
            <button type="button" class="btn btn-primary" id="checkoutBtn">Checkout</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Checkout Form Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="checkoutModalLabel">Complete Your Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="checkoutForm">
              <div class="mb-3">
                <label for="customerName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="customerName" required>
              </div>
              <div class="mb-3">
                <label for="customerEmail" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="customerEmail" required>
              </div>
              <div class="mb-3">
                <label for="customerPhone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="customerPhone" required>
              </div>
              <div class="mb-3">
                <label for="customerAddress" class="form-label">Delivery Address</label>
                <textarea class="form-control" id="customerAddress" rows="3" required></textarea>
              </div>
              <div class="mb-3">
                <label for="customerNotes" class="form-label">Additional Notes (Optional)</label>
                <textarea class="form-control" id="customerNotes" rows="2"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="sendToWhatsAppBtn">Send Order via WhatsApp</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Artworks Grid -->
    <div class="row g-4" id="artworksGrid">
      <?php
      $artworks = [
        ['title'=>'Sheikh Mountains','description'=>'Acrylic portrait capturing the spirit of Somali nomad life.','price'=>'30.00','image'=>'../assets/images/blogss/1.jpg', 'category'=>'creativity'],
        ['title'=>'Women sunset','description'=>'Warm colors and calm skies inspired by coastal evenings.','price'=>'15.00','image'=>'../assets/images/blogss/2.jpg', 'category'=>'techniques'],
        ['title'=>'Somali Motherhood','description'=>'Traditional patterns blended with modern abstract textures.','price'=>'20.00','image'=>'../assets/images/blogss/3.jpg', 'category'=>'creativity'],
        ['title'=>'Vibrant Horse','description'=>'Calm ocean waves with bright reflections.','price'=>'18.00','image'=>'../assets/images/blogss/4.jpg', 'category'=>'price'],
        ['title'=>'Somali Heritage','description'=>'Urban Somali night life painted in vivid strokes.','price'=>'25.00','image'=>'../assets/images/blogss/5.jpg', 'category'=>'techniques'],
        ['title'=>'Cat Glow','description'=>'Somali traditional huts with sunset background.','price'=>'22.00','image'=>'../assets/images/blogss/6.jpg', 'category'=>'price'],
        ['title'=>'Somali Acacia Landscape','description'=>'Contemporary Somali patterns re-imagined in bold colors.','price'=>'28.00','image'=>'../assets/images/blogss/7.jpg', 'category'=>'creativity'],
        ['title'=>'Women of Hope','description'=>'Soft pastel tones reflecting hope and calmness.','price'=>'19.00','image'=>'../assets/images/blogss/8.jpg', 'category'=>'techniques'],
        ['title'=>'Desert Journey','description'=>'Nomadic journey through the Somali desert landscape.','price'=>'35.00','image'=>'../assets/images/blogss/1.jpg', 'category'=>'price'],
        ['title'=>'Coastal Village','description'=>'Vibrant depiction of a traditional Somali coastal village.','price'=>'27.00','image'=>'../assets/images/blogss/2.jpg', 'category'=>'creativity'],
        ['title'=>'Market Day','description'=>'Busy market scene with rich colors and textures.','price'=>'32.00','image'=>'../assets/images/blogss/3.jpg', 'category'=>'techniques'],
        ['title'=>'Nomadic Spirit','description'=>'Expressive portrait of Somali nomadic culture.','price'=>'40.00','image'=>'../assets/images/blogss/4.jpg', 'category'=>'creativity'],
      ];
      
      // Get current page from URL or default to 1
      $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
      $itemsPerPage = 8;
      $totalItems = count($artworks);
      $totalPages = ceil($totalItems / $itemsPerPage);
      
      // Ensure current page doesn't exceed total pages
      if ($currentPage > $totalPages) {
          $currentPage = $totalPages;
      }
      
      // Calculate items to show for current page
      $startIndex = ($currentPage - 1) * $itemsPerPage;
      $currentItems = array_slice($artworks, $startIndex, $itemsPerPage);
      
      foreach ($currentItems as $artwork): ?>
        <div class="col-sm-6 col-md-4 col-lg-3 artwork-item" data-category="<?= $artwork['category'] ?>">
          <div class="card h-100 border-0 shadow-purple">
            <img src="<?= htmlspecialchars($artwork['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($artwork['title']) ?>">
            <div class="card-body d-flex flex-column">
              <h6 class="fw-semibold"><?= htmlspecialchars($artwork['title']) ?></h6>
              <p class="text-muted small flex-grow-1 mb-2"><?= htmlspecialchars($artwork['description']) ?></p>
              <div class="d-flex justify-content-between align-items-center">
                <!-- Price Section -->
                <div class="d-flex flex-column">
                  <span class="fw-bold text-dark" style="font-size: 1.1rem;">Price</span>
                  <span class="fw-semibold text-primary" style="font-size: 0.9rem;">$<?= htmlspecialchars($artwork['price']) ?></span>
                </div>
                <button class="btn-shop add-to-cart" data-title="<?= htmlspecialchars($artwork['title']) ?>" data-price="<?= htmlspecialchars($artwork['price']) ?>">
                  <i class="fa fa-shopping-cart text-white"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <nav class="mt-4">
      <ul class="pagination justify-content-end custom-pagination-square">
        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $currentPage - 1 ?>">Prev</a>
        </li>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        
        <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Next</a>
        </li>
      </ul>
    </nav>
  </section>

  <!-- JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Shopping cart functionality
      let cart = [];
      const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
      const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
      
      // Add to cart buttons
      document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
          const title = this.getAttribute('data-title');
          const price = parseFloat(this.getAttribute('data-price'));
          
          // Check if item already in cart
          const existingItem = cart.find(item => item.title === title);
          
          if (existingItem) {
            existingItem.quantity += 1;
          } else {
            cart.push({
              title: title,
              price: price,
              quantity: 1
            });
          }
          
          updateCart();
          cartModal.show();
        });
      });
      
      // Filter buttons
      document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
          // Remove active class from all buttons
          document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
          });
          
          // Add active class to clicked button
          this.classList.add('active');
          
          const filter = this.getAttribute('data-filter');
          filterArtworks(filter);
        });
      });
      
      // Search functionality
      document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        filterBySearch(searchTerm);
      });
      
      // Checkout button - show form
      document.getElementById('checkoutBtn').addEventListener('click', function() {
        cartModal.hide();
        checkoutModal.show();
      });
      
      // Send to WhatsApp button
      document.getElementById('sendToWhatsAppBtn').addEventListener('click', function() {
        const name = document.getElementById('customerName').value;
        const email = document.getElementById('customerEmail').value;
        const phone = document.getElementById('customerPhone').value;
        const address = document.getElementById('customerAddress').value;
        const notes = document.getElementById('customerNotes').value;
        
        // Validate form
        if (!name || !email || !phone || !address) {
          alert('Please fill in all required fields.');
          return;
        }
        
        // Format order details for WhatsApp
        let message = `*NEW ART ORDER*%0A%0A`;
        message += `*Customer Information:*%0A`;
        message += `Name: ${name}%0A`;
        message += `Email: ${email}%0A`;
        message += `Phone: ${phone}%0A`;
        message += `Address: ${address}%0A`;
        if (notes) {
          message += `Notes: ${notes}%0A`;
        }
        
        message += `%0A*Order Details:*%0A`;
        cart.forEach((item, index) => {
          message += `${index + 1}. ${item.title} - $${item.price} x ${item.quantity}%0A`;
        });
        
        message += `%0A*Total: $${calculateTotal().toFixed(2)}*`;
        
        // Create WhatsApp URL Maslah whatsapp number
        const whatsappNumber = '';
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${message}`;
        
        // Open WhatsApp in a new tab
        window.open(whatsappUrl, '_blank');
        
        // Reset cart and close modals
        cart = [];
        updateCart();
        checkoutModal.hide();
        document.getElementById('checkoutForm').reset();
        
        // Show success message
        alert('Your order has been prepared for WhatsApp. Please complete the process in the opened window.');
      });
      
      // Filter artworks by category
      function filterArtworks(category) {
        const items = document.querySelectorAll('.artwork-item');
        
        items.forEach(item => {
          if (category === 'all' || item.getAttribute('data-category') === category) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      }
      
      // Filter artworks by search term
      function filterBySearch(searchTerm) {
        const items = document.querySelectorAll('.artwork-item');
        
        items.forEach(item => {
          const title = item.querySelector('.fw-semibold').textContent.toLowerCase();
          const description = item.querySelector('.text-muted').textContent.toLowerCase();
          
          if (title.includes(searchTerm) || description.includes(searchTerm)) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      }
      
      // Update cart display
      function updateCart() {
        const cartItems = document.getElementById('cartItems');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const cartTotal = document.getElementById('cartTotal');
        
        // Clear previous items
        cartItems.innerHTML = '';
        
        if (cart.length === 0) {
          emptyCartMessage.style.display = 'block';
          cartTotal.textContent = '$0.00';
          return;
        }
        
        emptyCartMessage.style.display = 'none';
        
        // Add items to cart display
        cart.forEach(item => {
          const itemElement = document.createElement('div');
          itemElement.className = 'd-flex justify-content-between align-items-center border-bottom pb-2 mb-2';
          itemElement.innerHTML = `
            <div>
              <h6 class="mb-0">${item.title}</h6>
              <small class="text-muted">$${item.price.toFixed(2)} x ${item.quantity}</small>
            </div>
            <div>
              <span class="fw-bold">$${(item.price * item.quantity).toFixed(2)}</span>
              <button class="btn btn-sm btn-outline-danger ms-2 remove-item" data-title="${item.title}">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          `;
          cartItems.appendChild(itemElement);
        });
        
        // Add event listeners to remove buttons
        document.querySelectorAll('.remove-item').forEach(button => {
          button.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            cart = cart.filter(item => item.title !== title);
            updateCart();
          });
        });
        
        // Update total
        cartTotal.textContent = '$' + calculateTotal().toFixed(2);
      }
      
      // Calculate cart total
      function calculateTotal() {
        return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
      }
    });
  </script>
</body>
</html>
<?php include 'Includes/footer.php'; ?>

<style>
  :root{
    --primary:#7B1FA2;
    --primary-dark:#4A148C;
    --secondary:#7C3AED;
  }
  /* Custom body background */
  .custom-body{
    background-color: #EAE6FF; 
  }
  /* Hero background */
  .hero-section{
    background:linear-gradient(80deg, rgba(96, 30, 175, 0.6), rgba(124,58,237,0.6)),
              url('../assets/images/blogss/hero.jpg') no-repeat center/cover;
    min-height:400px;
  }

  /* Artworks images size */
  .card-img-top{
    height:220px;
    object-fit:cover;
  }

  /* Purple card shadow */
  .shadow-purple{
    box-shadow:0 7px 15px rgba(123,31,162,.25)!important;
    transition:transform .2s ease;
  }
  .shadow-purple:hover{ transform:translateY(-5px); }

  /* Shop button */
  .btn-shop{
    background-color:var(--primary);
    border:none; width:40px; height:40px;
    border-radius:8px;
    display:flex; justify-content:center; align-items:center;
    box-shadow:0 3px 10px rgba(123,31,162,.3);
    transition:all .2s ease-in-out;
  }
  .btn-shop:hover{ background-color:var(--primary-dark); transform:translateY(-2px); }

  /* Search bar */
  .search-wrapper{
    position:relative;
    width:280px;
    margin:auto;
  }
  .search-input{
    border-radius:50px;
    padding:8px 40px 8px 12px;
    font-size:.9rem;
    border:2px solid var(--primary);
    box-shadow:0 3px 6px rgba(123,31,162,.15);
    transition:all .2s ease;
  }
  .search-input:focus{
    outline:none;
    border-color:var(--secondary);
    box-shadow:0 4px 10px rgba(124,58,237,.25);
  }
  .search-icon-right{
    position:absolute;
    top:50%; right:12px; transform:translateY(-50%);
    font-size:.85rem; color:var(--primary);
    pointer-events:none;
  }

  /* Buttons */
  .text-primary{ color:var(--primary)!important; }
  .btn-primary{ background-color:var(--primary)!important; border-color:var(--primary)!important; }
  .btn-primary:hover{ background-color:var(--primary-dark)!important; border-color:var(--primary-dark)!important; }
  .btn-outline-primary{ color:var(--primary)!important; border-color:var(--primary)!important; }
  .btn-outline-primary:hover,
  .btn-outline-primary.active{ color:#fff!important; background-color:var(--primary)!important; border-color:var(--primary)!important; }

  /* Pagination */
  .custom-pagination-square .page-link{
    color:var(--primary);
    border:1px solid var(--primary);
    border-radius:8px;
    margin:0 3px;
    padding:6px 12px;
    font-size:.9rem;
    transition:all .2s ease;
  }
  .custom-pagination-square .page-link:hover{
    background-color:var(--primary);
    color:#fff;
  }
  .custom-pagination-square .active .page-link{
    background-color:var(--primary);
    color:#fff;
    border-color:var(--primary);
  }
  
  /* Cart item styles */
  .remove-item {
    padding: 0.15rem 0.5rem;
  }
</style>