<?php
/**
 * Site Header with Navigation
 */

$config = require __DIR__ . '/../config/config.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>

<header class="site-header" role="banner">
    <div class="container">
        <div class="header-content">
            <!-- Logo/Brand -->
            <div class="brand">
                <a href="<?php echo $config['app']['base_url']; ?>/" class="brand-link">
                    <img src="<?php echo $config['app']['base_url']; ?>/images/hcc-logo-white.png" alt="HCC Logo" class="brand-logo">
                </a>
            </div>
            
            <!-- Centered Navigation -->
            <nav class="main-nav main-nav-centered" role="navigation" aria-label="Main navigation">
                <ul class="nav-list nav-list-centered">
                    <li class="nav-item">
                        <a href="<?php echo $config['app']['base_url']; ?>/" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $config['app']['base_url']; ?>/about.php" class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $config['app']['base_url']; ?>/events.php" class="nav-link <?php echo $currentPage === 'events' ? 'active' : ''; ?>">Events</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $config['app']['base_url']; ?>/involved.php" class="nav-link <?php echo $currentPage === 'involved' ? 'active' : ''; ?>">Get Involved</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo $config['app']['base_url']; ?>/join.php" class="nav-link nav-cta <?php echo $currentPage === 'join' ? 'active' : ''; ?>">Join CSA</a>
                    </li>
                </ul>
            </nav>
            
            <!-- Mobile menu toggle -->
            <button class="mobile-menu-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>
    </div>
</header>

<!-- Sticky Join Button for Mobile -->
<div class="sticky-join-mobile">
    <a href="<?php echo $config['app']['base_url']; ?>/join.php" class="btn btn-primary btn-block">Join CSA</a>
</div>
