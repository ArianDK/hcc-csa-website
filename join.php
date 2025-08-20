<?php
/**
 * CSA Website - Join Page
 */

session_start();

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Join CSA - Computer Science Association at HCC';
$pageDescription = 'Join the Computer Science Association at Houston Community College. Quick and easy registration for all STEM students.';

// Generate CSRF token
$csrfToken = Security::generateCSRFToken();

// Success/error messages
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This will be handled by the API endpoint, but we show messages here if redirected back
    if (isset($_SESSION['join_success'])) {
        $successMessage = $_SESSION['join_success'];
        unset($_SESSION['join_success']);
    }
    if (isset($_SESSION['join_error'])) {
        $errorMessage = $_SESSION['join_error'];
        unset($_SESSION['join_error']);
    }
}

include __DIR__ . '/partials/meta.php';
include __DIR__ . '/partials/header.php';
include __DIR__ . '/partials/captcha.php';
?>

<main id="main-content">
    <!-- Hero Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h1>Join the Computer Science Association</h1>
                <p class="text-lg text-secondary">Become part of HCC's most active tech student community</p>
            </div>
        </div>
    </section>

    <!-- Join Form Section -->
    <section class="section bg-secondary">
        <div class="container" style="max-width: 600px;">
            <?php if ($successMessage): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($errorMessage): ?>
                <div class="alert alert-error" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Membership Registration</h2>
                    <p class="text-muted">Join CSA • Completely free • For everyone</p>
                </div>
                
                <div class="card-body">
                    <form action="api/join_clean.php" method="POST" data-validate data-requires-captcha data-captcha-action="join_form">
                        <!-- CSRF Token -->
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        
                        <!-- Personal Information -->
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   class="form-control" 
                                   data-validate="required|min:2|max:80"
                                   required 
                                   autocomplete="given-name">
                            <div class="form-help">Your first name as you'd like it to appear on name tags</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   class="form-control" 
                                   data-validate="required|min:2|max:80"
                                   required 
                                   autocomplete="family-name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   data-validate="required|email|custom:hcc_email_preferred"
                                   required 
                                   autocomplete="email">
                            <div class="form-help">We'll send a verification email here. HCC students: use your @hccs.edu email for best experience.</div>
                        </div>
                        
                        <!-- Academic Information -->
                        <div class="form-group">
                            <label for="year_level" class="form-label">Year Level</label>
                            <select id="year_level" name="year_level" class="form-control">
                                <option value="">Select your year (optional)</option>
                                <option value="Freshman">Freshman</option>
                                <option value="Sophomore">Sophomore</option>
                                <option value="Junior">Junior</option>
                                <option value="Senior">Senior</option>
                            </select>
                            <div class="form-help">This helps us plan appropriate events and networking opportunities</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="major" class="form-label">Major/Field of Study</label>
                            <select id="major" name="major" class="form-control">
                                <option value="">Select your major (optional)</option>
                                <option value="Computer Science">Computer Science</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Artificial Intelligence">Artificial Intelligence</option>
                                <option value="Data Science">Data Science</option>
                                <option value="Engineering - General">Engineering - General</option>
                                <option value="Engineering - Electrical">Engineering - Electrical</option>
                                <option value="Engineering - Mechanical">Engineering - Mechanical</option>
                                <option value="Engineering - Civil">Engineering - Civil</option>
                                <option value="Engineering - Chemical">Engineering - Chemical</option>
                                <option value="Mathematics">Mathematics</option>
                                <option value="Statistics">Statistics</option>
                                <option value="Physics">Physics</option>
                                <option value="Chemistry">Chemistry</option>
                                <option value="Biology">Biology</option>
                                <option value="Environmental Science">Environmental Science</option>
                                <option value="Health Sciences">Health Sciences</option>
                                <option value="Pre-Med">Pre-Med</option>
                                <option value="Pre-Engineering">Pre-Engineering</option>
                                <option value="Other STEM">Other STEM</option>
                                <option value="Undeclared">Undeclared</option>
                            </select>
                            <div class="form-help">This helps us tailor events to member interests</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="campus" class="form-label">Primary Campus</label>
                            <select id="campus" name="campus" class="form-control">
                                <option value="">Select your campus (optional)</option>
                                <option value="Central">Central Campus</option>
                                <option value="Northeast">Northeast Campus</option>
                                <option value="Northwest">Northwest Campus</option>
                                <option value="Southeast">Southeast Campus</option>
                                <option value="Southwest">Southwest Campus</option>
                                <option value="Online">Online Student</option>
                                <option value="Multiple">Multiple Campuses</option>
                            </select>
                            <div class="form-help">We may organize campus-specific events</div>
                        </div>
                        
                        <!-- Agreements -->
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="accepted_code" 
                                       name="accepted_code" 
                                       class="form-check-input" 
                                       data-validate="required"
                                       required>
                                <label for="accepted_code" class="form-check-label">
                                    I agree to follow the <a href="assets/docs/csa-code-of-conduct.pdf" target="_blank">CSA Code of Conduct</a> *
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="consent_privacy" 
                                       name="consent_privacy" 
                                       class="form-check-input" 
                                       data-validate="required"
                                       required>
                                <label for="consent_privacy" class="form-check-label">
                                    I have read and agree to the <a href="privacy.php" target="_blank">Privacy Policy</a> *
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" 
                                       id="consent_comms" 
                                       name="consent_comms" 
                                       class="form-check-input">
                                <label for="consent_comms" class="form-check-label">
                                    Yes, I want to receive email updates about CSA events and opportunities
                                </label>
                            </div>
                            <div class="form-help">You can unsubscribe at any time</div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" id="join-submit-btn">
                                Join CSA Now - It's Free!
                            </button>
                            
                            <!-- Inline Success Message -->
                            <div id="join-success-message" class="alert alert-success mt-3" style="display: none;" role="alert">
                                <strong>Registration Successful!</strong><br>
                                Welcome to CSA! Your membership is now pending admin approval. You'll hear from us soon!
                            </div>
                            
                            <!-- Inline Error Message -->
                            <div id="join-error-message" class="alert alert-error mt-3" style="display: none;" role="alert">
                                <strong>Registration Failed</strong><br>
                                <span id="join-error-text">Please try again.</span>
                            </div>
                        </div>
                        
                        <div class="text-center text-muted">
                            <small>
                                By joining, you agree to our terms and your membership will be reviewed by an admin. 
                                You'll be notified once your membership is approved!
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- What Happens Next Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>What Happens Next?</h2>
                <p class="text-secondary">Your journey with CSA starts here</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3>1. Check Your Email</h3>
                        <p>We'll send a verification email within minutes. Click the link to confirm your membership.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>2. Join Discord</h3>
                        <p>Get an invite to our Discord server where members chat, share resources, and coordinate study sessions.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>3. Attend Events</h3>
                        <p>Start attending workshops, hackathons, and social events. Your first event is always the hardest - we'll make sure you feel welcome!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Frequently Asked Questions</h2>
                <p class="text-secondary">Everything you need to know about joining CSA</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Is there a membership fee?</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>No!</strong> CSA membership is completely free. No dues, no hidden costs, no required purchases. Ever.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Do I need to be a Computer Science major?</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Not at all!</strong> We welcome all STEM majors: Engineering, Math, Biology, Physics, Chemistry, Health Sciences, and more.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">How much time commitment is required?</h3>
                    </div>
                    <div class="card-body">
                        <p>As little or as much as you want! Attend one event per semester to stay active, or come to everything. It's your choice.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">I'm a beginner. Will I fit in?</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Absolutely!</strong> We have members at all skill levels, from complete beginners to industry professionals. Everyone is welcome.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Can online students participate?</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Yes!</strong> Many of our events are virtual or hybrid. You can participate fully as an online student.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">What if I don't get the verification email?</h3>
                    </div>
                    <div class="card-body">
                        <p>Check your spam folder first. If still nothing, email us at president@hccs.edu and we'll help you out!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section">
        <div class="container text-center">
            <h2>Questions?</h2>
            <p class="text-lg text-secondary mb-6">We're here to help!</p>
            
            <div class="flex justify-center flex-wrap" style="gap: 2rem;">
                <a href="mailto:president@hccs.edu" class="btn btn-secondary">Email Us</a>
                <a href="https://discord.gg/hcc-csa" class="btn btn-secondary" target="_blank">Join Discord</a>
                <a href="about.php" class="btn btn-secondary">Learn More</a>
            </div>
        </div>
    </section>
</main>

<script>
// Enhanced form handling for join page
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[data-validate]');
    const submitBtn = document.getElementById('join-submit-btn');
    const successMessage = document.getElementById('join-success-message');
    const errorMessage = document.getElementById('join-error-message');
    const errorText = document.getElementById('join-error-text');
    
    if (form) {
        // Show encouraging messages as user fills out form
        const firstName = document.getElementById('first_name');
        const email = document.getElementById('email');
        
        firstName.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                const help = this.parentNode.querySelector('.form-help');
                help.innerHTML = `Hi ${this.value}! Welcome to CSA!`;
                help.style.color = 'var(--success-color)';
            }
        });
        
        // HCC email detection
        email.addEventListener('input', function() {
            const help = this.parentNode.querySelector('.form-help');
            if (this.value.includes('@hccs.edu')) {
                help.innerHTML = '🎓 Great! HCC students get priority for some events.';
                help.style.color = 'var(--success-color)';
            } else if (this.value.includes('@')) {
                help.innerHTML = 'Any email works! HCC students: consider using your @hccs.edu address.';
                help.style.color = 'var(--info-color)';
            } else {
                help.innerHTML = 'We\'ll send a verification email here. HCC students: use your @hccs.edu email for best experience.';
                help.style.color = 'var(--text-muted)';
            }
        });
        
        // Handle form submission with AJAX
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Hide previous messages
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';
            
            // Disable button but keep original text
            submitBtn.disabled = true;
            
            // Prepare form data
            const formData = new FormData(form);
            
            // Submit via AJAX
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        try {
                            const data = JSON.parse(text);
                            throw new Error(data.message || 'Server error');
                        } catch (e) {
                            throw new Error('Server returned: ' + response.status + ' - ' + text.substring(0, 100));
                        }
                    });
                }
                return response.json();
            })
            .then(data => {
                // Re-enable button
                submitBtn.disabled = false;
                
                if (data.success) {
                    // Show success message
                    successMessage.style.display = 'block';
                    successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Reset form
                    form.reset();
                    
                    // Reset help texts
                    const helpTexts = form.querySelectorAll('.form-help');
                    helpTexts.forEach(help => {
                        help.style.color = 'var(--text-muted)';
                    });
                    
                    // Update first name help text
                    const firstNameHelp = firstName.parentNode.querySelector('.form-help');
                    firstNameHelp.innerHTML = 'Your first name as you\'d like it to appear on name tags';
                    
                    // Update email help text
                    const emailHelp = email.parentNode.querySelector('.form-help');
                    emailHelp.innerHTML = 'We\'ll send a verification email here. HCC students: use your @hccs.edu email for best experience.';
                    
                } else {
                    // Show error message
                    errorText.innerHTML = data.message || 'Please try again.';
                    errorMessage.style.display = 'block';
                    errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            })
            .catch(error => {
                console.error('Registration error:', error);
                
                // Re-enable button
                submitBtn.disabled = false;
                
                // Show specific error message
                errorText.innerHTML = error.message || 'Something went wrong. Please try again.';
                errorMessage.style.display = 'block';
                errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    }
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
