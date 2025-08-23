<?php
/**
 * Site Footer
 */

$config = require __DIR__ . '/../config/config.php';
$currentYear = date('Y');
?>

<footer class="site-footer" role="contentinfo">
    <style>
    /* Improve footer section alignment and spacing */
    .footer-main {
        gap: var(--spacing-4) !important;
        align-items: start !important; /* Align all sections to top */
        justify-items: center !important; /* Center content within grid items */
    }
    .footer-section {
        margin-bottom: 0 !important;
        text-align: center !important; /* Center-align text for better visual balance */
        width: 100% !important;
    }
    .footer-section + .footer-section {
        margin-top: var(--spacing-3) !important;
    }
    /* Make all headings the same size for consistent alignment */
    .footer-title {
        font-size: var(--font-size-lg) !important;
        margin-bottom: var(--spacing-3) !important;
        font-weight: 600 !important;
    }
    /* Footer logo styling */
    .footer-logo {
        max-width: 120px !important;
        height: auto !important;
        margin-top: var(--spacing-3) !important;
        border-radius: var(--border-radius) !important;
    }
    </style>
    <div class="container">
        <div class="footer-content">
            
            <!-- Footer Main -->
            <div class="footer-main">
                <div class="footer-section">
                    <h4 class="footer-title">Computer Science Association</h4>
                    <p class="footer-description">
                        Building skills, projects, and community for all STEM majors at Houston Community College.
                    </p>
                                         <img src="<?php echo $config['app']['base_url']; ?>/images/csa-transparent.jpg" alt="CSA Logo" class="footer-logo">
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Social Media</h4>
                    <ul class="footer-links">
                        <li><a href="https://discord.gg/hcc-csa" aria-label="Join our Discord">Discord</a></li>
                        <li><a href="mailto:<?php echo htmlspecialchars($config['app']['admin_email']); ?>" aria-label="Email us">Email</a></li>
                        <li><a href="https://linkedin.com/company/hcc-csa" aria-label="Follow us on LinkedIn" target="_blank">LinkedIn</a></li>
                        <li><a href="https://instagram.com/hcc_csa" aria-label="Follow us on Instagram" target="_blank">Instagram</a></li>
                        <li><a href="https://facebook.com/hcc.csa" aria-label="Follow us on Facebook" target="_blank">Facebook</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $config['app']['base_url']; ?>/">Home</a></li>
                        <li><a href="<?php echo $config['app']['base_url']; ?>/about.php">About</a></li>
                        <li><a href="<?php echo $config['app']['base_url']; ?>/events.php">Events</a></li>
                        <li><a href="<?php echo $config['app']['base_url']; ?>/involved.php">Get Involved</a></li>
                        <li><a href="<?php echo $config['app']['base_url']; ?>/join.php">Join CSA</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4 class="footer-subtitle">Information</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo $config['app']['base_url']; ?>/privacy.php">Privacy Policy</a></li>
                        <li><a href="<?php echo $config['app']['base_url']; ?>/assets/docs/csa-constitution.pdf" target="_blank">Constitution (PDF)</a></li>
                        <li><a href="mailto:<?php echo htmlspecialchars($config['app']['admin_email']); ?>">Contact Us</a></li>
                        <li><a href="<?php echo $config['app']['base_url']; ?>/admin/">Admin Login</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p class="copyright">
                        &copy; <?php echo $currentYear; ?> Computer Science Association at Houston Community College. 
                        All rights reserved.
                    </p>
                    
                    <div class="footer-disclaimer">
                        <p>
                            <small>
                                HCC logo is a placeholder. Official branding will be used only with college approval. 
                                This is a student organization website.
                            </small>
                        </p>
                    </div>
                    
                    <div class="accessibility-statement">
                        <p>
                            <small>
                                Committed to digital accessibility. If you encounter barriers, please 
                                <a href="mailto:<?php echo htmlspecialchars($config['app']['admin_email']); ?>">contact us</a>.
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="<?php echo $config['app']['base_url']; ?>/assets/js/main.js"></script>
<script src="<?php echo $config['app']['base_url']; ?>/assets/js/form-validate.js"></script>
<script src="<?php echo $config['app']['base_url']; ?>/assets/js/analytics.js"></script>

<?php if (isset($additionalJS)): ?>
    <?php foreach ($additionalJS as $js): ?>
        <script src="<?php echo htmlspecialchars($js); ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

<?php if (isset($inlineJS)): ?>
    <script>
        <?php echo $inlineJS; ?>
    </script>
<?php endif; ?>

</body>
</html>
