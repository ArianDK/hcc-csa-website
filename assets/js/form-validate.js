/**
 * CSA Website Form Validation
 * Client-side validation with accessibility and UX enhancements
 */

(function() {
    'use strict';
    
    const FormValidator = {
        init: function() {
            this.setupFormValidation();
            this.setupRealTimeValidation();
        },
        
        setupFormValidation: function() {
            const forms = document.querySelectorAll('form[data-validate]');
            
            forms.forEach(form => {
                form.addEventListener('submit', this.handleFormSubmit.bind(this));
                
                // Setup CAPTCHA if available
                if (window.csaCaptcha && form.querySelector('[data-requires-captcha]')) {
                    this.setupCaptcha(form);
                }
            });
        },
        
        setupRealTimeValidation: function() {
            const inputs = document.querySelectorAll('input[data-validate], textarea[data-validate], select[data-validate]');
            
            inputs.forEach(input => {
                // Validate on blur
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                // Clear errors on input
                input.addEventListener('input', () => {
                    this.clearFieldError(input);
                });
            });
        },
        
        handleFormSubmit: function(e) {
            const form = e.target;
            const isValid = this.validateForm(form);
            
            if (!isValid) {
                e.preventDefault();
                
                // Focus first error field
                const firstError = form.querySelector('.form-control:invalid, .has-error input');
                if (firstError) {
                    firstError.focus();
                }
                
                return false;
            }
            
            // Handle CAPTCHA
            if (window.csaCaptcha && form.querySelector('[data-requires-captcha]')) {
                e.preventDefault();
                this.handleCaptchaSubmit(form);
                return false;
            }
            
            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton && window.CSA) {
                const originalText = submitButton.innerHTML;
                window.CSA.LoadingManager.show(submitButton, 'Submitting...');
                
                // Re-enable after timeout (fallback)
                setTimeout(() => {
                    window.CSA.LoadingManager.hide(submitButton, originalText);
                }, 10000);
            }
            
            return true;
        },
        
        validateForm: function(form) {
            const fields = form.querySelectorAll('input[data-validate], textarea[data-validate], select[data-validate]');
            let isValid = true;
            
            fields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });
            
            return isValid;
        },
        
        validateField: function(field) {
            const rules = this.parseValidationRules(field.getAttribute('data-validate'));
            const value = field.value.trim();
            const fieldGroup = field.closest('.form-group');
            
            // Clear previous errors
            this.clearFieldError(field);
            
            // Required validation
            if (rules.required && !value) {
                this.showFieldError(field, 'This field is required.');
                return false;
            }
            
            if (!value && !rules.required) {
                return true; // Skip other validations for empty optional fields
            }
            
            // Email validation
            if (rules.email && !this.isValidEmail(value)) {
                this.showFieldError(field, 'Please enter a valid email address.');
                return false;
            }
            
            // Length validation
            if (rules.minLength && value.length < rules.minLength) {
                this.showFieldError(field, `Must be at least ${rules.minLength} characters long.`);
                return false;
            }
            
            if (rules.maxLength && value.length > rules.maxLength) {
                this.showFieldError(field, `Must be no more than ${rules.maxLength} characters long.`);
                return false;
            }
            
            // Pattern validation
            if (rules.pattern && !new RegExp(rules.pattern).test(value)) {
                this.showFieldError(field, rules.patternMessage || 'Please enter a valid value.');
                return false;
            }
            
            // Custom validation
            if (rules.custom) {
                const customResult = this.customValidations[rules.custom](value, field);
                if (customResult !== true) {
                    this.showFieldError(field, customResult);
                    return false;
                }
            }
            
            // Confirmation field validation
            if (rules.confirm) {
                const confirmField = document.querySelector(`[name="${rules.confirm}"]`);
                if (confirmField && value !== confirmField.value.trim()) {
                    this.showFieldError(field, 'Values do not match.');
                    return false;
                }
            }
            
            return true;
        },
        
        parseValidationRules: function(rulesString) {
            const rules = {};
            
            if (!rulesString) return rules;
            
            rulesString.split('|').forEach(rule => {
                const [name, value] = rule.split(':');
                
                switch (name.trim()) {
                    case 'required':
                        rules.required = true;
                        break;
                    case 'email':
                        rules.email = true;
                        break;
                    case 'min':
                        rules.minLength = parseInt(value);
                        break;
                    case 'max':
                        rules.maxLength = parseInt(value);
                        break;
                    case 'pattern':
                        rules.pattern = value;
                        break;
                    case 'pattern_message':
                        rules.patternMessage = value;
                        break;
                    case 'custom':
                        rules.custom = value;
                        break;
                    case 'confirm':
                        rules.confirm = value;
                        break;
                }
            });
            
            return rules;
        },
        
        customValidations: {
            hcc_email_preferred: function(value, field) {
                if (!value) return true;
                
                const isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
                if (!isValidEmail) {
                    return 'Please enter a valid email address.';
                }
                
                // Show hint for non-HCC emails
                if (!value.includes('@hccs.edu')) {
                    const hintElement = field.parentNode.querySelector('.form-help');
                    if (hintElement) {
                        hintElement.innerHTML = '💡 HCC students: Consider using your @hccs.edu email address';
                        hintElement.style.color = 'var(--info-color)';
                    }
                }
                
                return true;
            },
            
            strong_password: function(value, field) {
                if (!value) return true;
                
                if (value.length < 8) {
                    return 'Password must be at least 8 characters long.';
                }
                
                if (!/(?=.*[a-z])/.test(value)) {
                    return 'Password must contain at least one lowercase letter.';
                }
                
                if (!/(?=.*[A-Z])/.test(value)) {
                    return 'Password must contain at least one uppercase letter.';
                }
                
                if (!/(?=.*\d)/.test(value)) {
                    return 'Password must contain at least one number.';
                }
                
                return true;
            }
        },
        
        isValidEmail: function(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        
        showFieldError: function(field, message) {
            const fieldGroup = field.closest('.form-group');
            if (!fieldGroup) return;
            
            // Remove existing error
            this.clearFieldError(field);
            
            // Add error class
            fieldGroup.classList.add('has-error');
            field.setAttribute('aria-invalid', 'true');
            
            // Create error element
            const errorElement = document.createElement('div');
            errorElement.className = 'form-error';
            errorElement.textContent = message;
            errorElement.setAttribute('role', 'alert');
            
            // Insert error after the field
            field.parentNode.insertBefore(errorElement, field.nextSibling);
            
            // Set aria-describedby for accessibility
            const errorId = `error-${field.name || field.id || Date.now()}`;
            errorElement.id = errorId;
            field.setAttribute('aria-describedby', errorId);
        },
        
        clearFieldError: function(field) {
            const fieldGroup = field.closest('.form-group');
            if (!fieldGroup) return;
            
            fieldGroup.classList.remove('has-error');
            field.setAttribute('aria-invalid', 'false');
            field.removeAttribute('aria-describedby');
            
            const errorElement = fieldGroup.querySelector('.form-error');
            if (errorElement) {
                errorElement.remove();
            }
        },
        
        setupCaptcha: function(form) {
            if (!window.csaCaptcha) return;
            
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                // Validate form first
                if (!this.validateForm(form)) {
                    return;
                }
                
                this.handleCaptchaSubmit(form);
            });
        },
        
        handleCaptchaSubmit: function(form) {
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.innerHTML : '';
            
            if (submitButton && window.CSA) {
                window.CSA.LoadingManager.show(submitButton, 'Verifying...');
            }
            
            const action = form.getAttribute('data-captcha-action') || 'submit';
            
            window.csaCaptcha.execute(action, (token) => {
                // Add token to form
                window.csaCaptcha.addTokenToForm(form, action);
                
                // Submit the form
                this.submitFormWithAjax(form).then(() => {
                    if (submitButton && window.CSA) {
                        window.CSA.LoadingManager.hide(submitButton, originalText);
                    }
                }).catch((error) => {
                    if (submitButton && window.CSA) {
                        window.CSA.LoadingManager.hide(submitButton, originalText);
                    }
                    this.showFormError(form, error.message || 'An error occurred. Please try again.');
                });
            });
        },
        
        submitFormWithAjax: function(form) {
            return new Promise((resolve, reject) => {
                const formData = new FormData(form);
                const action = form.getAttribute('action') || window.location.pathname;
                
                fetch(action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showFormSuccess(form, data.message || 'Success!');
                        
                        // Redirect if specified
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 2000);
                        }
                        
                        // Reset form if specified
                        if (data.reset) {
                            form.reset();
                        }
                        
                        resolve(data);
                    } else {
                        reject(new Error(data.message || 'An error occurred.'));
                    }
                })
                .catch(error => {
                    reject(error);
                });
            });
        },
        
        showFormSuccess: function(form, message) {
            this.clearFormMessages(form);
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success';
            alertDiv.textContent = message;
            alertDiv.setAttribute('role', 'alert');
            
            form.insertBefore(alertDiv, form.firstChild);
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        },
        
        showFormError: function(form, message) {
            this.clearFormMessages(form);
            
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-error';
            alertDiv.textContent = message;
            alertDiv.setAttribute('role', 'alert');
            
            form.insertBefore(alertDiv, form.firstChild);
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        },
        
        clearFormMessages: function(form) {
            const alerts = form.querySelectorAll('.alert');
            alerts.forEach(alert => alert.remove());
        }
    };
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => FormValidator.init());
    } else {
        FormValidator.init();
    }
    
    // Expose for external use
    window.CSA = window.CSA || {};
    window.CSA.FormValidator = FormValidator;
    
})();
