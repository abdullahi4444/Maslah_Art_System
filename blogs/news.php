<?php include 'Includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Discover & Shop Unique Art</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
  <style>
    :root{
      --primary: #7B1FA2;
      --primary-dark: #4A148C;
      --secondary: #7C3AED;
      --light-bg: #f8f9fa;
      --card-bg: #ffffff;
    }
    .custom-body{
      background-color: #EAE6FF;
    }
    body {
      background-color: var(--light-bg);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .hero-section {
      background: linear-gradient(80deg, rgba(96, 30, 175, 0.6), rgba(124,58,237,0.6)),
                url('../assets/images/blogss/IMG_3901.JPG') no-repeat center/cover;
      min-height: 370px;
      display: flex;
      align-items: center;      
      justify-content: center;  
      text-align: center;       
    }

    /* Card styling to match the image */
    .news-card {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background-color: var(--card-bg);
      height: 100%;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.5s ease, transform 0.5s ease, box-shadow 0.3s ease;
    }

    .news-card.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .news-card:hover {
      transform: translateY(-5px) !important;
      box-shadow: 0 12px 20px rgba(123, 31, 162, 0.15) !important;
    }

    .news-card img {
      height: 220px;
      object-fit: cover;
      width: 100%;
    }

    .news-card .card-body {
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
    }

    .news-card .card-title {
      font-weight: 700;
      color: #333;
      margin-bottom: 0.75rem;
      font-size: 1.25rem;
    }

    .news-date {
      color: #6c757d;
      font-size: 0.85rem;
      margin-bottom: 0.5rem;
    }

    .news-tag {
      background-color: rgba(123, 31, 162, 0.1);
      color: var(--primary);
      padding: 0.35rem 0.75rem;
      border-radius: 50px;
      font-size: 0.8rem;
      display: inline-block;
      margin-bottom: 1rem;
      font-weight: 600;
    }

    .news-desc {
      color: #555;
      flex-grow: 1;
      margin-bottom: 1.5rem;
      line-height: 1.5;
    }

    .news-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: auto;
    }

    .read-more {
      color: var(--primary);
      font-weight: 600;
      text-decoration: none;
      font-size: 0.9rem;
      transition: color 0.2s ease;
    }

    .read-more:hover {
      color: var(--primary-dark);
    }

    .news-author {
      color: #333;
      font-size: 0.85rem;
      font-weight: 600;
    }

    /* Search bar — right aligned, icon on RIGHT */
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

    /* Pagination styling */
    .custom-pagination-square .page-link {
      color: var(--primary);
      border: 1px solid #dee2e6;
      border-radius: 8px;
      margin: 0 3px;
      padding: 0.5rem 0.75rem;
      font-size: 0.9rem;
      transition: all 0.2s ease;
    }

    .custom-pagination-square .page-link:hover {
      background-color: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .custom-pagination-square .active .page-link {
      background-color: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    /* Section headings */
    .section-heading {
      color: var(--primary);
      font-weight: 700;
      margin-bottom: 2rem;
      position: relative;
      padding-bottom: 0.5rem;
    }

    .section-heading:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background-color: var(--primary);
      border-radius: 3px;
    }
    
    /* No results message */
    .no-results {
      text-align: center;
      padding: 2rem;
      color: #6c757d;
      display: none;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.5s ease, transform 0.5s ease;
    }
    
    .no-results.visible {
      opacity: 1;
      transform: translateY(0);
    }
    
    /* Filter button animations */
    .btn-outline-primary {
      transition: all 0.3s ease;
    }
    
    /* Animation for search input */
    .search-input {
      transition: all 0.3s ease;
    }
    
    .search-input:focus {
      transform: scale(1.02);
    }
    .all_bg {
        margin-top: -30px;
        background: linear-gradient(135deg, #efe8fb 0%, #f0eaff 100%);
        box-shadow: 0 -4px 20px rgba(123, 31, 162, 0.1);
        border-radius: 20px 20px 0 0;
        position: relative;
        overflow: hidden;
    }
    .all_bg::before {
        content: '';
        position: absolute;
        top: -300px;
        right: -300px;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, #6433f8 0%, rgba(110, 68, 255, 0) 70%);
        opacity: 0.3;
        z-index: 0;
    }
    .all_bg::after {
        content: '';
        position: absolute;
        bottom: -200px;
        left: -200px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, #fb1df0 0%, rgba(255, 77, 141, 0) 70%);
        opacity: 0.2;
        z-index: 0;
    }


    
  </style>
</head>
<body class="custom-body">
  <!-- Hero -->
  <section class="text-white py-5 hero-section text-center">
    <div class="container">
      <div class="col-md-14 mx-auto">
        <h1 class="display-4 fw-bold">The Inspiration Behind Every Brushstroke</h1>
        <p class="lead">insights, stories, and techniques from the vibrant world of art</p>
      </div>
    </div>
  </section>
  <!-- Shop -->
  <div class="all_bg">
    <section id="shop" class="container py-5">
      <h2 class="text-center text-dark fw-bold mb-4">Recent Posts</h2>
        <!-- Search + Filters -->
      <div class="mb-5">
        <div class="search-wrapper mb-3 ms-auto">
          <input type="text" id="searchInput" class="form-control search-input" placeholder="Search blog posts...">
          <i class="fa fa-search search-icon-right"></i>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-2" id="filterButtons">
          <button class="btn btn-outline-primary active" data-filter="all">All</button>
          <button class="btn btn-outline-primary" data-filter="color theory">color theory</button>
          <button class="btn btn-outline-primary" data-filter="art techniques">Creativity</button>
          <button class="btn btn-outline-primary" data-filter="creative insights">Techniques</button>
        </div>
      </div>
      <!-- News Grid (Blog Style with Theme) -->
      <div class="row g-4" id="blogPostsContainer">
        <?php
          $news = [
            [
              'id' => 1,
              'title'=>'Maslah Arts Studio oo Bilowday',
              'date'=>'july 30, 2025',
              'tag'=>'Color Theory',
              'desc'=>'Maslah Arts Studio waxa uu ku dhawaaqay bilaabidda "30-Maalmood Drawing Challenge" oo ah barnaamij....',
              'author'=>'maslax arts',
              'image'=>'../assets/images/blogss/news1.jpg',
              'content' => '<p>Maslah Arts Studio waxa uu ku dhawaaqay bilaabidda "30-Maalmood Drawing Challenge" oo ah barnaamij loogu talagalay dhammaan farshaxaniistayaasha, kuwa bilowga ah iyo kuwa khibradda lehba.</p>
                            <p>Barnaamijkan waxa uu ka kooban yahay maalmo kala duwan oo lagu diyaariyo qorista, fikradaha, iyo hababka lagu kordhinayo xirfada farshaxanka. Waxa jira casharo kala duwan oo loo marayo maalmaha kala duwan.</p>
                            <p>Ujeedada ugu weyn ee barnaamijkan waa in lagu dhiirrigeliyo farshaxaniistayaasha inay si joogto ah u diyaariyaan, ayna bartaan habab cusub oo farshaxan ah.</p>
                            <h3>Qaybta Koowaad</h3>
                            <p>Qaybtan koowaad waxa aan ku baran doonnaa sida loo sameeyo sawirro fudud oo la xiriira noolaha dabeecadda. Waxa aan isticmaali doonnaa qalab fudud oo ka kooban qalin iyo waraaqo.</p>
                            <p>Fadlan soo dhowow barnaamijkan, waxaad ka qeyb qaadan kartaa adigoo booqanaya website-kayaga.</p>'
            ],
            [
              'id' => 2,
              'title'=>'Exploring Texture with Mixed Media',
              'date'=>'july 27, 2025',
              'tag'=>'Art Techniques',
              'desc'=>'Diiwaangelinta oo si xiiso leh u socotay ayaa hadda xirmatay, waxaana isdiiwaangeliyay 80 farshaxaniiste.....',
              'author'=>'maslax arts',
              'image'=>'../assets/images/blogss/news2.jpg',
              'content' => '<p>Diiwaangelinta oo si xiiso leh u socotay ayaa hadda xirmatay, waxaana isdiiwaangeliyay 80 farshaxaniiste oo ka kala yimid gobollada kala duwan ee dalka.</p>
                            <p>Qeybta labaad ee barnaamijkan waxa aan ku baran doonnaa sida loo sameeyo sawirro adag oo leh muuqaal rogrogan. Waxa aan isticmaali doonnaa qalab kala duwan oo farshaxan ah sida sonkorta, rangaadka, iyo waxyaabo kale oo dabiici ah.</p>
                            <p>Ujeedada ugu weyn ee qeybtan waa in farshaxaniistuhu bartaan sida loo isticmaalo qalab kala duwan si loo sameeyo muqaal rogrogan oo xiiso leh.</p>
                            <h3>Hababka Loo Sameeyo Muqaal Rogrogdan</h3>
                            <p>Waxa jira habab kala duwan oo loo sameeyo muqaal rogrogan. Qaabka ugu fudud waa in la isticmaalo shinni meel ay ka baxdo si loo sameeyo muuqaal kala duwan.</p>
                            <p>Hab kale oo loo sameeyo muqaal rogrogan waa in la isticmaalo waraqyo kala duwan oo dhererkiisu kala duwan yahay.</p>'
            ],
            [
              'id' => 3,
              'title'=>'What Inspires a Modern Artist Today',
              'date'=>'aug 23, 2025',
              'tag'=>'Creative Insights',
              'desc'=>'Maslah Arts ayaa si rasmi ah ugu qeyb galay Somali Travel & Tourism Expo iyo Somtex Industry Awards 2025.....',
              'author'=>'maslax arts',
              'image'=>'../assets/images/blogss/news3.jpg',
              'content' => '<p>Maslah Arts ayaa si rasmi ah ugu qeyb galay Somali Travel & Tourism Expo iyo Somtex Industry Awards 2025. Tallaabadani waxay muujinaysaa sida farshaxanka uu muhiim u yahay horumarka dhaqaalaha iyo bulshada.</p>
                            <p>Expo-ga waxa uu ahaa furin ay isugu yimaadeen ganacsatada, wakaaladaha safariiska, iyo hayadaha dalxiiska. Waxa kaloo jiray bandhigyo farshaxan oo ka dhiganaya dhaqanka iyo taariikhda Soomaaliyeed.</p>
                            <h3>Ujeedada Laga Laabtay Expo-ga</h3>
                            <p>Ujeedada ugu weyn ee ka qeyb galka expo-ga ayaa ahayd in la muujiyo muhiimadda farshaxanka u leh dhaqanka iyo dalxiiska. Farshaxanka waxa uu noqon kara qalab wax looga bartaa dhaqanka iyo taariikhda dal.</p>
                            <p>Waxa kale oo aan ku bandhignay farshaxanno cusub oo aan ku matalayno dhaqanka Soomaaliyeed iyo muuqaalka dalka.</p>'
            ],
            [
              'id' => 4,
              'title'=>'Cultural Roots in Modern Painting',
              'date'=>'July 22, 2025',
              'tag'=>'Culture & Art',
              'desc'=>'Ka qaybgalka Expo-ga iyo Abaalmarinta Somtex waa tallaabo muhiim ah oo Maslah Arts ku muujisa.....',
              'author'=>'maslax arts',
              'image'=>'../assets/images/blogss/news4.jpg',
              'content' => '<p>Ka qaybgalka Expo-ga iyo Abaalmarinta Somtex waa tallaabo muhiim ah oo Maslah Arts ku muujinayo sida farshaxanka uu uga qayb qaataayo horumarka dhaqaale iyo dhaqan ee dalka.</p>
                            <p>Abaalmarinta waxa lagu maamusay farshaxaniistayaal, qoraayaal, iyo hal abuurayaal kale oo ka qayb qaatay horumarka dalka. Waxaana loo guddoonsiiyey abaalka ugu sarreeya ee lagu aqoonsan karo hal abuurtooda.</p>
                            <h3>Abaalmarinta Laga Qaatay</h3>
                            <p>Maslah Arts waxa uu helay abaalmarin loogu talagalay "Farshaxanka Ugu Fiican" ee sannadka 2025. Abaalmarintani waxay ka timid golaha arrimaha dhaqanka ee Somaliland.</p>
                            <p>Waxa aan ku guulaysteen abaalmarintan iyadoo la eegayay habka aan u isticmaalnay midabada, fikradaha cusub, iyo sida aan ugu dhiirrigelinayno dhalinyarada inay bartaan farshaxanka.</p>'
            ],
            [
              'id' => 5,
              'title'=>'Motherhood need justice',
              'date'=>'August 10, 2025',
              'tag'=>'Art Movement',
              'desc'=>'Sawirkan waa cod farshaxan oo ka hadlayo dhibaatada dumarka Soomaaliyeed iyo guud ahaan dumarka caalamka.....',
              'author'=>'maslax arts',
              'image'=>'../assets/images/blogss/3.jpg',
              'content' => '<p>Sawirkan waa cod farshaxan oo ka hadlayo dhibaatada dumarka Soomaaliyeed iyo guud ahaan dumarka caalamka ku haysata dulmi, cadaadis, iyo khalkhal.</p>
                            <p>Ujeedada sawirkani waa in la daawado dhibaatooyinka ay soo marto dumarka, gaar ahaan hooyada, iyo sida ay ugu baahan yihiin in la taageero.</p>
                            <h3>Qalabka La Isticmaalay</h3>
                            <p>Sawirkan waxa aan isticmaalay midabka cas oo u taagan dhiigga iyo cadaadiska, midabka madow oo u taagan murugada iyo walbahaarka, iyo midabka cagaaran oo u taagan rajo iyo mustaqbal.</p>
                            <p>Waxa aan isticmaalay hab "expressionist" ah oo lagu muujinayo dareenka iyo dhibaatooyinka.</p>
                            <h3>Ujeedada Laga Laabtay</h3>
                            <p>Ujeedada ugu weyn ee sawirkani waa in la kiciyo cafis iyo in la taageero dumarka. Waxa aan rabnaa in la ogaado in dumarku yihiin qayb muhiim ah oo bulshada, ayna u baahan yihiin in la ixtiraamo.</p>'
            ],
            [
              'id' => 6,
              'title'=>'waxaa bilowday draw chellenge',
              'date'=>'September 5, 2025',
              'tag'=>'Digital Creativity',
              'desc'=>'waxaa si rasmi ubilowday challengii drawing ayka qeybgalaayn farshaxaniistayal hibo leh.....',
              'author'=>'Maslax arts',
              'image'=>'../assets/images/blogss/news2.jpg',
              'content' => '<p>Waxaa si rasmi u bilowday challengii drawing ay ka qeyb galaan farshaxaniistayaal hibo leh. Challenge-kan waxa uu socon doonaa toddobaad, waxaana loo qabanayaa maalmaha Isniinta.</p>
                            <p>Ujeedada challenge-kani waa in lagu dhiirrigeliyo farshaxaniistayaasha inay si joogto ah u diyaariyaan, ayna bartaan habab cusub oo farshaxan ah.</p>
                            <h3>Qaabka Loo Qeyb Galayo</h3>
                            <p>Qof kasta oo raba inuu ka qeyb qaato challenge-kani waa inuu buuxiyo foomka online ah oo aan ku hayno website-kayaga. Waxa uu heli doonaa tilmaamaha iyo macluumaadka lagama maarmaanka u ah challenge-kan.</p>
                            <p>Waxa jira mushaar lagu wadaagi doono kuwa guusha gaadha.</p>
                            <h3>Mushaarrada</h3>
                            <p>Mushaarrada challenge-kani waxa ka mid ah:</p>
                            <ul>
                              <li>Maraakiibta koowaad: Qalab farshaxan oo qiimo ah</li>
                              <li>Maraakiibta labaad: Buug farshaxan</li>
                              <li>Maraakiibta saddexaad: Casharado farshaxan</li>
                            </ul>'
            ]          
          ];

        foreach ($news as $post): ?>
          <div class="col-md-6 col-lg-4 blog-post" data-tags="<?= htmlspecialchars(strtolower($post['tag'])) ?>">
            <div class="news-card card h-100 shadow-sm">
              <img src="<?= htmlspecialchars($post['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['title']) ?>">

              <div class="card-body">
                <!-- Title -->
                <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>

                <!-- Date + Tag same row -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="news-date">
                    <i class="fa fa-calendar me-1"></i> <?= htmlspecialchars($post['date']) ?>
                  </div>
                  <span class="news-tag"><?= htmlspecialchars($post['tag']) ?></span>
                </div>

                <!-- Description -->
                <p class="news-desc"><?= htmlspecialchars($post['desc']) ?></p>

                <!-- Footer -->
                <div class="news-footer">
                  <a href="blog-details.php?id=<?= $post['id'] ?>" class="read-more">
                    Read more <i class="fa fa-arrow-right ms-1"></i>
                  </a>
                  <div class="news-author">By <?= htmlspecialchars($post['author']) ?></div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
      <!-- No results message -->
      <div id="noResults" class="no-results">
        <i class="fas fa-search fa-3x mb-3"></i>
        <h3>No posts found</h3>
        <p>Try adjusting your search or filter to find what you're looking for.</p>
      </div>

      <!-- Pagination + Custom Button -->
      <nav class="mt-5 d-flex justify-content-between align-items-center">
        <!-- Button left -->
        <a href="../blogs/shop2.php" class="btn btn-primary">
          <i class="fa fa-shopping-bag me-1"></i> Go to Shop
        </a>

        <!-- Pagination right -->
        <!-- <ul class="pagination custom-pagination-square mb-0">
          <li class="page-item"><a class="page-link" href="#">Prev</a></li>
          <li class="page-item active"><a class="page-link" href="#">1</a></li>
          <li class="page-item"><a class="page-link" href="#">2</a></li>
          <li class="page-item"><a class="page-link" href="#">3</a></li>
          <li class="page-item"><a class="page-link" href="#">Next</a></li>
        </ul> -->
      </nav>
    </section>
  </div>

  <!-- JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="script.js"></script>
</body>
</html>
<?php include 'Includes/footer.php'; ?>