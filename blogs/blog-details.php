<?php 
    // Include database connection if needed
    // For now, we'll use the same array as in blog.php
    include 'Includes/header.php'; 

    // Sample blog data (same as in blog.php)
    $news = [
    1 => [
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
    2 => [
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
                    <p>Hab kale oo lou sameeyo muqaal rogrogan waa in la isticmaalo waraqyo kala duwan oo dhererkiisu kala duwan yahay.</p>'
    ],
    3 => [
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
    4 => [
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
    5 => [
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
    6 => [
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

    // Get the blog post ID from the URL
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

    // If the ID doesn't exist, default to the first post
    if (!isset($news[$id])) {
        $id = 1;
    }

    $post = $news[$id];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($post['title']); ?> - Maslah Arts Blog</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <!-- AOS Animation Library -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  
  <style>
    :root{
      --primary: #7B1FA2;
      --primary-dark: #4A148C;
      --primary-light: #E1BEE7;
      --secondary: #7C3AED;
      --secondary-light: #D0BFFF;
      --accent: #FF4081;
      --light-bg: #f8f9fa;
      --dark-text: #333;
      --light-text: #6c757d;
      --white: #ffffff;
      --gradient: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
      --shadow: 0 10px 30px rgba(0,0,0,0.1);
      --shadow-hover: 0 15px 35px rgba(0,0,0,0.15);
    }

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

    
    body {
      background-color: white;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--dark-text);
      line-height: 1.7;
    }

    
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-weight: 700;
    }
    
    .blog-detail-hero {
      background: linear-gradient(100deg, rgba(96, 30, 175, 0.85), rgba(124,58,237,0.85)),
                url('<?php echo $post['image']; ?>') no-repeat center/cover;
      min-height: 500px;
      display: flex;
      align-items: center;
      padding: 4rem 0;
      color: white;
      position: relative;
      overflow: hidden;
    }
    
    .blog-detail-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
      opacity: 0.5;
    }
    
    .hero-content {
      position: relative;
      z-index: 2;
    }
    
    .blog-content {
      background: white;
      border-radius: 16px;
      padding: 3rem;
      margin-top: -80px;
      box-shadow: var(--shadow);
      position: relative;
      z-index: 10;
    }
    
    .blog-tag {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      color: white;
      padding: 0.5rem 1.2rem;
      border-radius: 50px;
      font-size: 0.85rem;
      display: inline-flex;
      align-items: center;
      margin-bottom: 1.5rem;
      font-weight: 500;
      letter-spacing: 0.5px;
    }
    
    .blog-tag i {
      margin-right: 8px;
    }
    
    .blog-meta {
      color: rgba(255, 255, 255, 0.9);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 20px;
    }
    
    .blog-meta span {
      display: inline-flex;
      align-items: center;
      font-size: 0.95rem;
    }
    
    .blog-meta i {
      margin-right: 8px;
      font-size: 1rem;
    }
    
    .blog-content-body {
      line-height: 1.9;
      color: #555;
      font-size: 1.05rem;
    }
    
    .blog-content-body h2, 
    .blog-content-body h3, 
    .blog-content-body h4 {
      color: var(--primary);
      margin-top: 2.5rem;
      margin-bottom: 1.2rem;
      position: relative;
      padding-bottom: 10px;
    }
    
    .blog-content-body h2::after,
    .blog-content-body h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 60px;
      height: 3px;
      background: var(--gradient);
      border-radius: 3px;
    }
    
    .blog-content-body p {
      margin-bottom: 1.8rem;
    }
    
    .blog-content-body ul, 
    .blog-content-body ol {
      margin-bottom: 1.8rem;
      padding-left: 1.8rem;
    }
    
    .blog-content-body li {
      margin-bottom: 0.7rem;
      position: relative;
    }
    
    .blog-content-body ul li::before {
      content: '•';
      color: var(--primary);
      font-weight: bold;
      display: inline-block;
      width: 1em;
      margin-left: -1em;
    }
    
    .blog-content-body blockquote {
      border-left: 4px solid var(--primary);
      padding-left: 1.5rem;
      margin: 2rem 0;
      font-style: italic;
      color: var(--primary-dark);
      background: var(--primary-light);
      padding: 1.5rem;
      border-radius: 0 12px 12px 0;
    }
    
    .blog-navigation {
      display: flex;
      justify-content: space-between;
      margin-top: 3rem;
      padding-top: 2.5rem;
      border-top: 1px solid #eee;
    }
    
    .nav-btn {
        position: relative;
        display: inline-block;
        padding: 12px 32px;
        font-size: 15px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #fff;
        background: linear-gradient(135deg, #4f46e5, #6e8efb);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        text-decoration: none;
    }

    /* Hover - elegant effect */
    .nav-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
        background: linear-gradient(135deg, #6e8efb, #4f46e5);
    }

    /* Pressed/active state */
    .nav-btn:active {
        transform: scale(0.97);
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.2);
    }

    /* Smooth glow line effect */
    .nav-btn::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            120deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.3) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        transition: all 0.5s ease;
    }

    .nav-btn:hover::before {
        left: 100%;
    }

    /* Optional subtle border effect */
    .nav-btn::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        pointer-events: none;
    }
    
    .author-section {
      background: linear-gradient(to right, #f9f9f9, #f1f1f1);
      border-radius: 16px;
      padding: 2rem;
      margin-top: 3rem;
      display: flex;
      align-items: center;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .author-img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid white;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .author-info {
      margin-left: 1.5rem;
    }
    
    .author-name {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--primary-dark);
      margin-bottom: 0.5rem;
    }
    
    .social-links {
        display: flex;
        gap: 15px;
        margin-top: 1.5rem;
        justify-content: center;
    }

    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(145deg, #e6e6e6, #ffffff);
        color: #555;
        text-decoration: none; /* ❌ No underline */
        line-height: 1; /* Prevents extra spacing */
        box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1),
                    -4px -4px 10px rgba(255, 255, 255, 0.8);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        font-size: 18px;
    }

        /* Hover Animation */
    .social-links a:hover {
        color: #fff;
        transform: translateY(-6px) scale(1.1);
        box-shadow: 0 0 20px rgba(0, 123, 255, 0.6);
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        animation: glow 1.5s infinite alternate;
    }

    /* Rotating shine effect */
    .social-links a::before {
        content: "";
        position: absolute;
        width: 140%;
        height: 140%;
        background: rgba(255, 255, 255, 0.2);
        top: -50%;
        left: -50%;
        transform: rotate(45deg);
        transition: all 0.5s ease;
    }

    .social-links a:hover::before {
        top: 0;
        left: 0;
        transform: rotate(180deg);
    }

    /* Glow animation */
    @keyframes glow {
        0% { box-shadow: 0 0 5px #6e8efb, 0 0 10px #6e8efb; }
        50% { box-shadow: 0 0 20px #a777e3, 0 0 30px #a777e3; }
        100% { box-shadow: 0 0 5px #6e8efb, 0 0 10px #6e8efb; }
    }

    
    .related-posts {
      margin-top: 5rem;
    }
    
    .section-title {
      position: relative;
      padding-bottom: 1rem;
      margin-bottom: 2.5rem;
      text-align: center;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 80px;
      height: 4px;
      background: var(--gradient);
      border-radius: 2px;
    }
    
    .related-card {
      border: none;
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.4s ease;
      height: 100%;
      box-shadow: var(--shadow);
    }
    
    .related-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-hover);
    }
    
    .related-card img {
      height: 200px;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .related-card:hover img {
      transform: scale(1.05);
    }
    
    .card-body {
      padding: 1.5rem;
    }
    
    .card-tag {
      background: var(--primary-light);
      color: var(--primary);
      padding: 0.35rem 0.8rem;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
      display: inline-block;
      margin-bottom: 1rem;
    }
    
    .card-title {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 1.25rem;
      margin-bottom: 0.8rem;
      color: var(--dark-text);
    }
    
    .card-text {
      color: var(--light-text);
      margin-bottom: 1.2rem;
      font-size: 0.95rem;
    }
    
    .read-more-btn {
      display: inline-flex;
      align-items: center;
      color: var(--primary);
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .read-more-btn i {
      margin-left: 5px;
      transition: transform 0.3s ease;
    }
    
    .read-more-btn:hover {
      color: var(--secondary);
    }
    
    .read-more-btn:hover i {
      transform: translateX(5px);
    }
    
    .back-to-blog {
      padding: 1rem 2rem;
      border-radius: 50px;
      background: var(--gradient);
      color: white;
      font-weight: 500;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      text-decoration: none;
      box-shadow: 0 5px 15px rgba(123, 31, 162, 0.3);
    }
    
    .back-to-blog:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(123, 31, 162, 0.4);
      color: white;
    }
    
    .back-to-blog i {
      margin-right: 8px;
    }
    
    .share-section {
      display: flex;
      align-items: center;
      margin: 2rem 0;
      padding: 1.5rem;
      background: #f8f9fa;
      border-radius: 12px;
    }
    
    .share-text {
      margin-right: 1rem;
      font-weight: 500;
      color: var(--dark-text);
    }
    
    .share-buttons {
      display: flex;
      gap: 10px;
    }
    
    .share-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: white;
      color: var(--dark-text);
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .share-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    
    .share-btn.facebook:hover { background: #3b5998; color: white; }
    .share-btn.twitter:hover { background: #1da1f2; color: white; }
    .share-btn.linkedin:hover { background: #0077b5; color: white; }
    .share-btn.pinterest:hover { background: #bd081c; color: white; }
    
    /* Animation classes */
    [data-aos] {
      transition: all 0.8s ease;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .blog-detail-hero {
        min-height: 400px;
        text-align: center;
      }
      
      .blog-content {
        padding: 2rem 1.5rem;
        margin-top: -60px;
      }
      
      .blog-meta {
        justify-content: center;
      }
      
      .author-section {
        flex-direction: column;
        text-align: center;
      }
      
      .author-info {
        margin-left: 0;
        margin-top: 1.5rem;
      }
      
      .social-links {
        justify-content: center;
      }
      
      .blog-navigation {
        flex-direction: column;
        gap: 1rem;
      }
      
      .nav-btn {
        justify-content: center;
      }
    }
    #related-posts-section{
        background: #2a043aff;
        padding: 1rem 0;
    }
    #related-posts-section .section-title{
        color: white;
    }
    #related-posts-section .related-card{
        background: white;
    }
    #related-posts-section .card-tag{
        background: #7b1fa234;
        color: #7B1FA2;
    }
    #related-posts-section .card-title{
        color: var(--primary-dark);
    }
    #related-posts-section .read-more-btn{
        color: var(--primary);
    }
    #related-posts-section .read-more-btn:hover{
        color: var(--secondary);
    }

  </style>
</head>
<body>
  <!-- Blog Detail Hero -->
  <section class="blog-detail-hero">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 mx-auto hero-content">
          <span class="blog-tag"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($post['tag']); ?></span>
          <h1 class="display-4 fw-bold"><?php echo htmlspecialchars($post['title']); ?></h1>
          <div class="blog-meta">
            <span><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($post['date']); ?></span>
            <span><i class="fas fa-user"></i> By <?php echo htmlspecialchars($post['author']); ?></span>
            <span><i class="fas fa-clock"></i> 5 min read</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Blog Content -->
  <section class="container second-section mb-5">
    <div class="row">
      <div class="col-lg-8 mx-auto">
        <div class="blog-content" data-aos="fade-up">
          <div class="blog-content-body">
            <?php echo $post['content']; ?>
          </div>
          
          <!-- Blog Navigation -->
          <div class="blog-navigation" data-aos="fade-up" data-aos-delay="200">
            <?php if ($id > 1): ?>
            <a href="blog-details.php?id=<?php echo $id-1; ?>" class="nav-btn">
              <i class="fas fa-arrow-left me-2"></i> Previous Post
            </a>
            <?php else: ?>
            <span></span>
            <?php endif; ?>
            
            <?php if ($id < count($news)): ?>
            <a href="blog-details.php?id=<?php echo $id+1; ?>" class="nav-btn">
              Next Post <i class="fas fa-arrow-right ms-2"></i>
            </a>
            <?php endif; ?>
          </div>
          
          <!-- Author Section -->
          <div class="author-section" data-aos="fade-up" data-aos-delay="300">
            <img src="maslah.jpg" alt="Author" class="author-img">
            <div class="author-info">
              <h5 class="author-name"><?php echo htmlspecialchars($post['author']); ?></h5>
              <p class="mb-0">Maslah Arts is dedicated to promoting Somali art and culture through various creative expressions. We believe in the power of art to transform communities and inspire change.</p>
              <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Related Posts Section -->
  <section id="related-posts-section" class="mb-5">
    <!-- Related Posts -->
    <section class="container related-posts">
        <h3 class="section-title text-center" data-aos="fade-up">You Might Also Like</h3>
        <div class="row g-4">
        <?php
        // Display 3 related posts (excluding the current one)
        $relatedCount = 0;
        foreach ($news as $relatedId => $relatedPost):
            if ($relatedId != $id && $relatedCount < 3):
            $relatedCount++;
        ?>
        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $relatedCount * 100; ?>">
            <div class="card related-card h-100">
            <img src="<?php echo htmlspecialchars($relatedPost['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($relatedPost['title']); ?>">
            <div class="card-body">
                <span class="card-tag"><?php echo htmlspecialchars($relatedPost['tag']); ?></span>
                <h5 class="card-title"><?php echo htmlspecialchars($relatedPost['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($relatedPost['desc']); ?></p>
                <a href="blog-details.php?id=<?php echo $relatedId; ?>" class="read-more-btn">
                Read More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            </div>
        </div>
        <?php
            endif;
        endforeach;
        ?>
        </div>
    </section>

    <!-- Back to Blog -->
    <section class="container text-center my-5" data-aos="fade-up">
        <a href="news.php" class=" nav-btn">
        <i class="fas fa-arrow-left me-1"></i> Back to All Posts
        </a>
    </section>
  </section>

  <!-- JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AOS Animation Library -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    // Initialize AOS animation library
    document.addEventListener('DOMContentLoaded', function() {
      AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true
      });
    });
  </script>
</body>
</html>
<?php include 'Includes/footer.php'; ?>