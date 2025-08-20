<?php
/**
 * HTML Meta tags and head section
 */

$config = require __DIR__ . '/../config/config.php';
$pageTitle = $pageTitle ?? 'Computer Science Association - HCC';
$pageDescription = $pageDescription ?? 'Join the Computer Science Association at Houston Community College. Open to all STEM majors - build skills, projects, and community.';
$pageKeywords = $pageKeywords ?? 'HCC, Houston Community College, Computer Science, Programming, STEM, Student Organization';
$canonicalUrl = $canonicalUrl ?? $config['app']['base_url'] . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($pageKeywords); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($config['app']['name']); ?>">
    
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="og:image" content="<?php echo $config['app']['base_url']; ?>/assets/img/og-image.jpg">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($pageTitle); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($pageDescription); ?>">
    <meta property="twitter:image" content="<?php echo $config['app']['base_url']; ?>/assets/img/og-image.jpg">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $config['app']['base_url']; ?>/assets/img/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $config['app']['base_url']; ?>/assets/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $config['app']['base_url']; ?>/assets/img/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $config['app']['base_url']; ?>/assets/img/favicon-16x16.png">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo $config['app']['base_url']; ?>/assets/css/global.css">
    
    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#1a365d">
    <meta name="msapplication-TileColor" content="#1a365d">
    
    <!-- Skip to content link (for accessibility) -->
    <style>
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--primary-color);
            color: white;
            padding: 8px;
            text-decoration: none;
            z-index: 100;
            border-radius: 4px;
        }
        .skip-link:focus {
            top: 6px;
        }
    </style>
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($additionalMeta)): ?>
        <?php echo $additionalMeta; ?>
    <?php endif; ?>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
