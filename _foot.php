<?php
// Pre-fill the WhatsApp chat with a starter message
$_wa_number  = '60123456789';
$_wa_message = urlencode("Hi, I'm interested in your flower arrangements. Could you share more details?");

// Store address used for the Google Maps search link
$_store_address = urlencode('BloomNest Florist, Jalan Genting Kelang, 53300 Kuala Lumpur, Federal Territory of Kuala Lumpur');
?>
    </main>

    <footer>
        <div class="footer-top">

            <div class="footer-col">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="https://facebook.com/bloomnestflorist" target="_blank" rel="noopener" aria-label="Facebook">
                        <img src="/images/facebook.png" alt="Facebook">
                    </a>
                    <a href="https://instagram.com/bloomnestflorist" target="_blank" rel="noopener" aria-label="Instagram">
                        <img src="/images/instagram.png" alt="Instagram">
                    </a>
                    <a href="https://xiaohongshu.com/user/profile/bloomnestflorist" target="_blank" rel="noopener" aria-label="Xiaohongshu">
                        <img src="/images/xiaohongshu.png" alt="Xiaohongshu">
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h3>We Accept</h3>
                <div class="payment-icons">
                    <img src="/images/visa.png" alt="Visa">
                    <img src="/images/mastercard.png" alt="Mastercard">
                    <!-- <img src="/images/online-banking.png" alt="Online Banking">
                    <img src="/images/tng.png" alt="Touch 'n Go eWallet">
                    <img src="/images/cod.png" alt="Cash on Delivery"> -->
                </div>
            </div>

            <div class="footer-col">
                <h3>Quick Links</h3>
                <a href="https://www.google.com/maps/search/?api=1&query=<?= $_store_address ?>" target="_blank" rel="noopener">Store Locator</a>
                <a href="https://wa.me/<?= $_wa_number ?>?text=<?= $_wa_message ?>" target="_blank" rel="noopener">Contact BloomNest</a>
                <a href="/about.php">About Us</a>
                <a href="/care_guide.php">Flower Care Tips</a>
            </div>

            <div class="footer-col footer-mission">
                <h3>Our Mission</h3>
                <p>
                    At BloomNest, our mission is to spread love and warmth through flowers that
                    never fade in memory. We believe every bouquet carries a story and every
                    bloom is a small way of saying what words can't. With freshness, care and
                    a personal touch, we hope every arrangement becomes a lasting moment for
                    the people you cherish.
                </p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>Copyright &copy; <?= date('Y') ?> BloomNest Florist &middot; Developed by <b>XIAO TING & JOEL</b></p>
            <span>Privacy Policy</span>
        </div>
    </footer>
</body>
</html>