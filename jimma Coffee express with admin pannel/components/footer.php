<?php
// components/footer.php

// Ensure session is started if not already done by the main page
// session_start(); 
?>

<div class="top-footer">
    <h2><i class="bx bx-envelope"></i> Sign Up For Newsletter</h2>
    <form action="process_newsletter_signup.php" method="POST" class="input-field">
        <input type="email" name="newsletter_email" placeholder="Your email address..." required>
        <button type="submit" name="subscribe_newsletter" class="btn">Subscribe</button>
    </form>
</div>

<footer class="footer">
    <div class="overlay"></div>
    <div class="footer-content">
        <div class="img-box">
            <img src="img/logo2.png" alt="Company Logo">
        </div>
        <div class="inner-footer">
            <div class="card">
                <h3>About Us</h3>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Our Difference</a></li>
                    <li><a href="#">Community Matters</a></li>
                    <li><a href="#">Press</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Bouqs Video</a></li>
                </ul>
            </div>
            <div class="card">
                <h3>Services</h3>
                <ul>
                    <li><a href="#">Order</a></li>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Shipping</a></li>
                    <li><a href="#">Terms of Use</a></li>
                    <li><a href="#">Account Detail</a></li>
                    <li><a href="#">My Account</a></li>
                </ul>
            </div>
            <div class="card">
                <h3>Local</h3>
                <ul>
                    <li><a href="#">Addis Ababa</a></li>
                    <li><a href="#">Hawassa</a></li>
                    <li><a href="#">Dire Dawa</a></li>
                    <li><a href="#">Adama</a></li>
                    <li><a href="#">Gonder</a></li>
                    <li><a href="#">Mekelle</a></li>
                </ul>
            </div>
            <div class="card">
                <h3>Connect With Us</h3> <!-- Changed "newsletter" to "Connect With Us" -->
                <p>Follow us on social media for updates.</p>
                <div class="social-links">
                    <a href="#" target="_blank"><i class="bx bxl-instagram"></i></a>
                    <a href="#" target="_blank"><i class="bx bxl-twitter"></i></a>
                    <a href="#" target="_blank"><i class="bx bxl-linkedin"></i></a>
                    <a href="#" target="_blank"><i class="bx bxl-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="bottom-footer">
            <p>© <?= date('Y'); ?> All Rights Reserved by <span>Jimma Coffee Express</span></p>
        </div>
    </div>
</footer>