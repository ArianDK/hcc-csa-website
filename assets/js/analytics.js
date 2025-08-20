/**
 * CSA Website Analytics
 * Simple, privacy-focused analytics without third-party trackers
 */

(function() {
    'use strict';
    
    const Analytics = {
        init: function() {
            this.setupPageTracking();
            this.setupEventTracking();
            this.setupPerformanceTracking();
        },
        
        setupPageTracking: function() {
            // Track page view
            this.trackPageView();
            
            // Track time on page
            this.startTimeTracking();
            
            // Track scroll depth
            this.setupScrollTracking();
        },
        
        setupEventTracking: function() {
            // Track CTA clicks
            document.addEventListener('click', (e) => {
                const target = e.target.closest('a, button');
                if (!target) return;
                
                // Track join button clicks
                if (target.textContent.toLowerCase().includes('join') && 
                    target.getAttribute('href') === '/join.php') {
                    this.trackEvent('cta_click', 'join_button', {
                        location: this.getElementLocation(target),
                        page: window.location.pathname
                    });
                }
                
                // Track external links
                if (target.tagName === 'A' && target.hostname !== window.location.hostname) {
                    this.trackEvent('external_link', 'click', {
                        url: target.href,
                        text: target.textContent.trim()
                    });
                }
                
                // Track email links
                if (target.href && target.href.startsWith('mailto:')) {
                    this.trackEvent('contact', 'email_click', {
                        email: target.href.replace('mailto:', '')
                    });
                }
                
                // Track form submissions
                if (target.type === 'submit') {
                    const form = target.closest('form');
                    if (form) {
                        const formId = form.id || form.getAttribute('data-form-name') || 'unknown';
                        this.trackEvent('form_submit', formId, {
                            page: window.location.pathname
                        });
                    }
                }
            });
            
            // Track form field interactions
            document.addEventListener('focus', (e) => {
                if (e.target.matches('input, textarea, select')) {
                    const form = e.target.closest('form');
                    if (form) {
                        const formId = form.id || form.getAttribute('data-form-name') || 'unknown';
                        this.trackEvent('form_interaction', 'field_focus', {
                            form: formId,
                            field: e.target.name || e.target.id || 'unknown'
                        });
                    }
                }
            }, true);
        },
        
        setupPerformanceTracking: function() {
            // Track page load time
            window.addEventListener('load', () => {
                setTimeout(() => {
                    const perfData = performance.getEntriesByType('navigation')[0];
                    if (perfData) {
                        this.trackPerformance('page_load', {
                            loadTime: Math.round(perfData.loadEventEnd - perfData.fetchStart),
                            domContentLoaded: Math.round(perfData.domContentLoadedEventEnd - perfData.fetchStart),
                            firstByte: Math.round(perfData.responseStart - perfData.fetchStart)
                        });
                    }
                }, 1000);
            });
        },
        
        setupScrollTracking: function() {
            let maxScroll = 0;
            let scrollMilestones = [25, 50, 75, 90, 100];
            let trackedMilestones = [];
            
            const trackScroll = this.throttle(() => {
                const scrollTop = window.pageYOffset;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercent = Math.round((scrollTop / docHeight) * 100);
                
                maxScroll = Math.max(maxScroll, scrollPercent);
                
                scrollMilestones.forEach(milestone => {
                    if (scrollPercent >= milestone && !trackedMilestones.includes(milestone)) {
                        trackedMilestones.push(milestone);
                        this.trackEvent('scroll', 'depth', {
                            percent: milestone,
                            page: window.location.pathname
                        });
                    }
                });
            }, 1000);
            
            window.addEventListener('scroll', trackScroll);
            
            // Track max scroll on page unload
            window.addEventListener('beforeunload', () => {
                this.trackEvent('scroll', 'max_depth', {
                    percent: maxScroll,
                    page: window.location.pathname
                });
            });
        },
        
        startTimeTracking: function() {
            const startTime = Date.now();
            
            // Track time on page when leaving
            const trackTimeOnPage = () => {
                const timeSpent = Math.round((Date.now() - startTime) / 1000);
                this.trackEvent('engagement', 'time_on_page', {
                    seconds: timeSpent,
                    page: window.location.pathname
                });
            };
            
            window.addEventListener('beforeunload', trackTimeOnPage);
            
            // Also track every 30 seconds for active users
            let activeTime = 0;
            const trackActiveTime = setInterval(() => {
                if (this.isUserActive()) {
                    activeTime += 30;
                    if (activeTime % 60 === 0) { // Every minute
                        this.trackEvent('engagement', 'active_time', {
                            seconds: activeTime,
                            page: window.location.pathname
                        });
                    }
                }
            }, 30000);
            
            window.addEventListener('beforeunload', () => {
                clearInterval(trackActiveTime);
            });
        },
        
        trackPageView: function() {
            const data = {
                url: window.location.href,
                path: window.location.pathname,
                title: document.title,
                referrer: document.referrer || 'direct',
                timestamp: new Date().toISOString(),
                userAgent: navigator.userAgent,
                language: navigator.language,
                screenResolution: `${screen.width}x${screen.height}`,
                viewportSize: `${window.innerWidth}x${window.innerHeight}`
            };
            
            this.sendAnalytics('pageview', data);
        },
        
        trackEvent: function(category, action, properties = {}) {
            const data = {
                category: category,
                action: action,
                properties: properties,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                path: window.location.pathname
            };
            
            this.sendAnalytics('event', data);
        },
        
        trackPerformance: function(metric, data) {
            const perfData = {
                metric: metric,
                data: data,
                timestamp: new Date().toISOString(),
                url: window.location.href,
                path: window.location.pathname
            };
            
            this.sendAnalytics('performance', perfData);
        },
        
        sendAnalytics: function(type, data) {
            // Store in localStorage for now (simple implementation)
            const storageKey = 'csa_analytics';
            let analytics = JSON.parse(localStorage.getItem(storageKey) || '[]');
            
            analytics.push({
                type: type,
                data: data
            });
            
            // Keep only last 100 entries to prevent storage bloat
            if (analytics.length > 100) {
                analytics = analytics.slice(-100);
            }
            
            localStorage.setItem(storageKey, JSON.stringify(analytics));
            
            // In a real implementation, you would send this to your analytics endpoint
            // fetch('/api/analytics', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ type, data })
            // });
        },
        
        getElementLocation: function(element) {
            // Determine where on the page the element is located
            const rect = element.getBoundingClientRect();
            const scrollTop = window.pageYOffset;
            const scrollLeft = window.pageXOffset;
            
            if (rect.top + scrollTop < window.innerHeight) {
                return 'above_fold';
            } else if (rect.top + scrollTop < window.innerHeight * 2) {
                return 'below_fold';
            } else {
                return 'far_below';
            }
        },
        
        isUserActive: function() {
            // Simple activity detection
            return document.hasFocus() && !document.hidden;
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
            }
        },
        
        // Public methods for manual tracking
        trackCustomEvent: function(category, action, properties) {
            this.trackEvent(category, action, properties);
        },
        
        trackFormSubmission: function(formName, success = true) {
            this.trackEvent('form', success ? 'submit_success' : 'submit_error', {
                form: formName
            });
        },
        
        getAnalyticsData: function() {
            // For admin dashboard to display analytics
            const storageKey = 'csa_analytics';
            return JSON.parse(localStorage.getItem(storageKey) || '[]');
        },
        
        clearAnalyticsData: function() {
            localStorage.removeItem('csa_analytics');
        }
    };
    
    // Initialize analytics when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => Analytics.init());
    } else {
        Analytics.init();
    }
    
    // Expose for external use
    window.CSA = window.CSA || {};
    window.CSA.Analytics = Analytics;
    
})();
