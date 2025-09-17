<!-- Includes/footer.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
    .footer {
        background-color: white;
        padding: 40px 20px;
        font-family: Arial, sans-serif;
        margin-bottom: -30px;
    }

    .footer-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 100px;
        max-width: 1300px;
        margin: auto;
    }

    .footer-column {
        flex: 1;
        min-width: 300px;
    }

    .footer h2, .footer h3 {
        color: #040007;
        margin-bottom: 16px;
    }

    .footer h2 {
        font-size: 20px;
        font-weight: bold;
    }

    .footer p, .footer a {
        color: #333;
        line-height: 1.8;
        text-decoration: none;
    }

    .footer a:hover {
        color: #8A2BE2;
    }

    .footer .logo {
        width: 60px;
        height: auto;
        border-radius: 40px;
    }

    .subscribe-box {
        margin-top: 15px;
    }

    .subscribe-box p {
        margin: 0;
    }

    .subscribe-box button {
        background: #8A2BE2;
        color: white;
        padding: 8px 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .footer hr.main-line {
        border: none;
        border-top: 3px solid #8A2BE2;
        margin: 20px 0 0;
        width: 218px;
    }

    .social-icons {
        margin-top: 20px;
        display: flex;
        gap: 12px;
    }

    .social-icons a {
        background-color: #8A2BE2;
        color: white;
        width: 38px;
        height: 38px;
        border-radius: 10%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s ease;
        font-size: 18px;
    }

    .social-icons a:hover {
        background-color: #9785afff;
    }

    .footer ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer ul li {
        margin-bottom: 10px;
    }

    .footer-bottom {
        text-align: center;
        color: #666;
        margin-top: 30px;
        border-top: 1px solid #ccc;
        padding-top: 20px;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .footer-container {
            flex-direction: column;
            gap: 40px;
        }
    }
</style>

<footer class="footer">
    <div class="footer-container">
        
        <!-- Column 1: Brand -->
        <div class="footer-column">
            <img src="../logo.jpg" alt="Maslax Arts Logo" class="logo">&nbsp;&nbsp;&nbsp;&nbsp;
            <h2>Maslax Arts</h2>
            <p>Empowering creativity through art and connection.</p>

            <div class="subscribe-box">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <p>Subscribe Our Art studio</p>
                    <button><i class="fas fa-arrow-right"></i></button>
                </div>
                <hr class="main-line">
            </div>

            <!-- Social Icons -->
            <div class="social-icons">
                <a href="https://www.facebook.com/share/1D5DWJiAz9/"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.tiktok.com/@maslah_abdi_dahir?_t=ZM-8zYD25fiUkC&_r=1"><i class="fab fa-tiktok"></i></a>
                <a href="https://youtube.com/@maslaharts?si=-6aOv6ZiK0pUClRD"><i class="fab fa-x-twitter"></i></a>
                <a href="https://www.instagram.com/maslah_arts?igsh=aDhtN2tubnFub3c2"><i class="fab fa-instagram"></i></a>
            </div>
        </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

        <!-- Column 2: Quick Links -->
        <div class="footer-column">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="../contact/contact.php">Contact</a></li>
                <li><a href="workshops.php">Workshops</a></li>
                <li><a href="../blogs/news.php">Blogs</a></li>
                <li><a href="../gallery/maslah_gallery.php">Gallery</a></li>
                <li><a href="../about/about.php">About Maslah</a></li>
            </ul>
        </div>

        <!-- Column 3: Contact -->
        <div class="footer-column">
            <h3>Contact Us</h3>
            <p><i class="bi bi-telephone" style="margin-right: 10px; color:#8A2BE2;"></i>+252-61-77-77-77-77</p>
            <p>
                <i class="bi bi-envelope" style="margin-right: 10px; color:#8A2BE2;"></i>
                <a href="mailto:Supports@gmail.com">Supports@gmail.com</a>
            </p>
            <p>
                <i class="bi bi-geo-alt" style="margin-right: 10px; color:#8A2BE2;"></i>
                Hodan, Banadir,<br>Mogadishu, Somalia
            </p>
        </div>
    </div>

    <div class="footer-bottom">
        ©<?php echo date("Y"); ?> Maslax Studio Arts. All rights reserved.
    </div>
</footer>
