# HCC Computer Science Association Website

<img src="assets/img/logo-gradient.jpg" alt="HCC CSA Logo" width="100" align="right"/>
A modern, responsive static website for the Computer Science Association at Houston City College. This website showcases the organization's mission, leadership, and provides information for prospective members.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Deployment](#deployment)
- [Customization](#customization)
- [Contributing](#contributing)
- [License](#license)

## Overview

The HCC CSA Website is a static website built with modern web technologies to serve the Computer Science Association at Houston City College. It provides an informative platform for student recruitment, leadership information, and community engagement.

### Key Highlights
<img src="assets/img/csa-dark.jpg" alt="HCC CSA Logo" width="200" align="right"/>
- **Modern Design**: Clean, responsive interface with professional styling
- **Static Website**: Fast loading, secure, and easy to deploy
- **GitHub Pages Ready**: Optimized for GitHub Pages hosting
- **Accessibility Compliant**: WCAG AA standards for inclusive access
- **STEM Inclusive**: Welcomes all Science, Technology, Engineering, and Mathematics majors



## Features

### Public Website

#### Homepage
- Hero section with organization introduction
- Why join CSA section with benefits
- STEM inclusivity messaging
- Our Leadership section with current board members
- Social media integration with dropdown navigation

#### About Page
- Mission statement and organizational values
- Campus locations and meeting details
- Contact information and social media links

#### Previous Board Members Page
- Historical leadership information
- Year-based navigation for different board terms
- Leadership team structure and roles

#### Privacy Policy
- Comprehensive privacy policy
- No data collection policy
- Contact information for privacy requests

## Technology Stack

### Frontend
- **HTML5**: Semantic markup with accessibility features
- **CSS3**: Modern styling with CSS Grid and Flexbox
- **JavaScript (ES6+)**: Interactive functionality including dropdown menus
- **Responsive Design**: Mobile-first approach with breakpoint optimization

### Hosting & Deployment
- **GitHub Pages**: Static website hosting
- **Custom Domain**: Support for custom domain configuration
- **CDN**: Global content delivery through GitHub's infrastructure

### Development Tools
- **Git**: Version control and collaboration
- **Markdown**: Documentation and content management

## Deployment

### GitHub Pages Setup

1. **Fork or Clone the Repository**
```bash
git clone https://github.com/ariandk/hcc-csa-website.git
cd hcc-csa-website
```

2. **Enable GitHub Pages**
   - Go to your repository on GitHub
   - Navigate to Settings → Pages
   - Select "Deploy from a branch"
   - Choose "main" branch and "/ (root)" folder
   - Click "Save"

3. **Custom Domain (Optional)**
   - Add a `CNAME` file to the root directory with your domain
   - Configure DNS settings with your domain provider
   - Update all internal links to use your custom domain

### File Structure

```
hcc-csa-website/
├── assets/
│   ├── css/
│   │   └── global.css          # Main stylesheet
│   ├── js/
│   │   └── main.js            # Main JavaScript functionality
│   └── img/                   # Image assets
├── index.html                 # Homepage
├── about.html                 # About page
├── previous-board.html        # Previous board members page
├── privacy.html               # Privacy policy
└── README.md                  # This file
```

## Customization

### Branding and Styling

#### Color Scheme
Edit CSS variables in `assets/css/global.css`:

```css
:root {
    --primary-color: #1a365d;      /* Dark blue */
    --secondary-color: #2d3748;    /* Gray */
    --accent-color: #3182ce;       /* Light blue */
    --success-color: #38a169;      /* Green */
    --warning-color: #d69e2e;      /* Yellow */
    --error-color: #e53e3e;        /* Red */
}
```

#### Typography
```css
:root {
    --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    --font-size-base: 16px;
    --line-height: 1.6;
}
```

### Content Customization

#### Leadership Information
Update leadership details in `index.html`:
- Current board member names and positions
- Leadership photos (place in `assets/img/leadership/` folder)
- Previous board member information in `previous-board.html`

#### Organization Information
Update organization details in `about.html`:
- Mission statement
- Campus locations
- Contact details

### Adding Leadership Photos

1. **Prepare Images**
   - Recommended size: 200x200px or larger
   - Format: JPG, PNG, or WebP
   - Should be square for best results
   - Place images in: `assets/img/leadership/` folder

2. **Update HTML**
   - Uncomment the image tags in leadership cards
   - Update the `src` attribute to point to your image
   - Comment out the initials fallback

## Contributing

### Development Setup

1. **Fork the Repository**
   - Click the "Fork" button on GitHub
   - Clone your forked repository

2. **Create a Branch**
```bash
git checkout -b feature/your-feature-name
```

3. **Make Changes**
   - Edit the HTML, CSS, or JavaScript files
   - Test your changes locally
   - Ensure all pages work correctly

4. **Commit Changes**
```bash
git add .
git commit -m "Add your feature description"
git push origin feature/your-feature-name
```

5. **Create Pull Request**
   - Go to your forked repository on GitHub
   - Click "New Pull Request"
   - Describe your changes
   - Submit the pull request

## License

### Usage Rights

This project is developed for the Computer Science Association at Houston City College.

**Permitted Uses:**
- Use by educational institutions
- Modification for organizational needs
- Deployment on institutional servers
- Academic research and learning

### Support and Contact

#### Technical Support
- **Documentation**: Check this README first
- **Issues**: Report bugs via GitHub issues
- **Community**: Join CSA Discord for peer support

#### Contact Information
- **Technical Issues**: CSA Technology Committee
- **General Questions**: Contact through social media links
- **Website Issues**: Create a GitHub issue

---

**Computer Science Association • Houston City College**

*Building the next generation of technology leaders*

---
