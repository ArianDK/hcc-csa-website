/**
 * CSA Website Main JavaScript
 * Handles navigation, theme toggle, and general interactions
 */

(function() {
    'use strict';
    
    // Theme Management
    const ThemeManager = {
        init: function() {
            this.setupThemeToggle();
            this.initializeTheme();
        },
        
        setupThemeToggle: function() {
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', this.toggleTheme.bind(this));
            }
        },
        
        initializeTheme: function() {
            const savedTheme = localStorage.getItem('csa-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                this.setTheme('dark');
            } else {
                this.setTheme('light');
            }
        },
        
        toggleTheme: function() {
            const currentTheme = document.body.classList.contains('dark-theme') ? 'dark' : 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            this.setTheme(newTheme);
        },
        
        setTheme: function(theme) {
            if (theme === 'dark') {
                document.body.classList.add('dark-theme');
            } else {
                document.body.classList.remove('dark-theme');
            }
            localStorage.setItem('csa-theme', theme);
        }
    };
    
    // Mobile Navigation
    const MobileNav = {
        init: function() {
            this.setupMobileMenu();
        },
        
        setupMobileMenu: function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const nav = document.querySelector('.main-nav');
            
            if (toggle && nav) {
                toggle.addEventListener('click', this.toggleMobileMenu.bind(this));
                
                // Close menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!toggle.contains(e.target) && !nav.contains(e.target)) {
                        this.closeMobileMenu();
                    }
                });
                
                // Close menu on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.closeMobileMenu();
                    }
                });
            }
        },
        
        toggleMobileMenu: function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const nav = document.querySelector('.main-nav');
            const isOpen = toggle.getAttribute('aria-expanded') === 'true';
            
            if (isOpen) {
                this.closeMobileMenu();
            } else {
                this.openMobileMenu();
            }
        },
        
        openMobileMenu: function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const nav = document.querySelector('.main-nav');
            
            toggle.setAttribute('aria-expanded', 'true');
            nav.style.display = 'block';
            nav.style.position = 'absolute';
            nav.style.top = '100%';
            nav.style.left = '0';
            nav.style.right = '0';
            nav.style.background = 'var(--bg-primary)';
            nav.style.borderTop = '1px solid var(--border-color)';
            nav.style.padding = 'var(--spacing-4)';
            nav.style.boxShadow = 'var(--shadow-lg)';
            nav.style.zIndex = 'var(--z-dropdown)';
            
            // Stack nav items vertically on mobile
            const navList = nav.querySelector('.nav-list');
            if (navList) {
                navList.style.flexDirection = 'column';
                navList.style.gap = 'var(--spacing-4)';
            }
        },
        
        closeMobileMenu: function() {
            const toggle = document.querySelector('.mobile-menu-toggle');
            const nav = document.querySelector('.main-nav');
            
            if (toggle && nav) {
                toggle.setAttribute('aria-expanded', 'false');
                
                // Reset mobile styles
                if (window.innerWidth < 768) {
                    nav.style.display = 'none';
                } else {
                    nav.removeAttribute('style');
                    const navList = nav.querySelector('.nav-list');
                    if (navList) {
                        navList.removeAttribute('style');
                    }
                }
            }
        }
    };
    
    // Dropdown Menu Management
    const DropdownManager = {
        init: function() {
            this.setupDropdowns();
        },
        
        setupDropdowns: function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                
                if (toggle) {
                    toggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.toggleDropdown(dropdown);
                    });
                }
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.dropdown')) {
                    this.closeAllDropdowns();
                }
            });
            
            // Close dropdowns on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeAllDropdowns();
                }
            });
        },
        
        toggleDropdown: function(dropdown) {
            const isActive = dropdown.classList.contains('active');
            
            // Close all other dropdowns first
            this.closeAllDropdowns();
            
            if (!isActive) {
                dropdown.classList.add('active');
                const toggle = dropdown.querySelector('.dropdown-toggle');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'true');
                }
            }
        },
        
        closeAllDropdowns: function() {
            const activeDropdowns = document.querySelectorAll('.dropdown.active');
            
            activeDropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
                const toggle = dropdown.querySelector('.dropdown-toggle');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    };
    
    // Smooth Scrolling for Anchor Links
    const SmoothScroll = {
        init: function() {
            document.addEventListener('click', this.handleLinkClick.bind(this));
        },
        
        handleLinkClick: function(e) {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;
            
            const href = link.getAttribute('href');
            if (href === '#') return;
            
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Update focus for accessibility
                target.focus();
            }
        }
    };
    
    // Sticky Join Button Logic
    const StickyJoinButton = {
        init: function() {
            this.setupStickyButton();
        },
        
        setupStickyButton: function() {
            const stickyButton = document.querySelector('.sticky-join-mobile');
            const joinPage = window.location.pathname.includes('join.php');
            
            if (stickyButton && joinPage) {
                stickyButton.style.display = 'none';
            }
            
            // Hide/show based on scroll position
            if (stickyButton && !joinPage) {
                let lastScrollTop = 0;
                window.addEventListener('scroll', () => {
                    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                    
                    if (scrollTop > lastScrollTop && scrollTop > 200) {
                        // Scrolling down
                        stickyButton.style.transform = 'translateY(100%)';
                    } else {
                        // Scrolling up
                        stickyButton.style.transform = 'translateY(0)';
                    }
                    
                    lastScrollTop = scrollTop;
                });
            }
        }
    };
    
    // Modal Management
    const ModalManager = {
        init: function() {
            this.setupModalTriggers();
        },
        
        setupModalTriggers: function() {
            document.addEventListener('click', (e) => {
                if (e.target.matches('[data-modal-trigger]')) {
                    e.preventDefault();
                    const modalId = e.target.getAttribute('data-modal-trigger');
                    this.openModal(modalId);
                }
                
                if (e.target.matches('[data-modal-close]') || e.target.closest('[data-modal-close]')) {
                    e.preventDefault();
                    this.closeModal();
                }
            });
            
            // Close modal on escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeModal();
                }
            });
        },
        
        openModal: function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                modal.setAttribute('aria-hidden', 'false');
                
                // Focus trap
                const focusableElements = modal.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                if (focusableElements.length > 0) {
                    focusableElements[0].focus();
                }
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            }
        },
        
        closeModal: function() {
            const openModal = document.querySelector('.modal[aria-hidden="false"]');
            if (openModal) {
                openModal.style.display = 'none';
                openModal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        }
    };
    
    // Loading States
    const LoadingManager = {
        show: function(button, text = 'Loading...') {
            if (button) {
                button.disabled = true;
                button.innerHTML = `<span class="spinner"></span> ${text}`;
            }
        },
        
        hide: function(button, originalText) {
            if (button) {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
    };
    
    // Utility Functions
    const Utils = {
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction() {
                const context = this;
                const args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },
        
        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },
        
        formatDate: function(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        sanitizeHTML: function(str) {
            const temp = document.createElement('div');
            temp.textContent = str;
            return temp.innerHTML;
        }
    };
    
    // Initialize everything when DOM is ready
    function init() {
        ThemeManager.init();
        MobileNav.init();
        DropdownManager.init();
        SmoothScroll.init();
        StickyJoinButton.init();
        ModalManager.init();
        
        // Handle window resize
        window.addEventListener('resize', Utils.debounce(() => {
            MobileNav.closeMobileMenu();
        }, 250));
        
        // Add smooth transitions after page load
        document.body.classList.add('js-loaded');
    }
    
    // DOM ready check
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    // Expose utilities globally for other scripts
    window.CSA = {
        LoadingManager: LoadingManager,
        Utils: Utils,
        ModalManager: ModalManager
    };
    
})();
