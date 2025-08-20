<?php
/**
 * CSA Website - Privacy Policy
 */

session_start();

$pageTitle = 'Privacy Policy - Computer Science Association at HCC';
$pageDescription = 'Privacy policy for the Computer Science Association at Houston Community College. Learn how we protect your personal information.';

include __DIR__ . '/partials/meta.php';
include __DIR__ . '/partials/header.php';
?>

<main id="main-content">
    <section class="section">
        <div class="container" style="max-width: 800px;">
            <div class="text-center mb-8">
                <h1>Privacy Policy</h1>
                <p class="text-secondary">Last updated: <?php echo date('F j, Y'); ?></p>
            </div>
            
            <div class="card">
                <div class="card-body">
                    <h2>Introduction</h2>
                    <p>
                        The Computer Science Association (CSA) at Houston Community College is committed to protecting your privacy and personal information. This policy explains how we collect, use, and safeguard your data when you interact with our organization and website.
                    </p>
                    
                    <h2>Information We Collect</h2>
                    
                    <h3>Membership Information</h3>
                    <p>When you join CSA, we collect:</p>
                    <ul>
                        <li><strong>Personal Details:</strong> First name, last name, email address</li>
                        <li><strong>Academic Information:</strong> Major/field of study, campus (optional)</li>
                        <li><strong>Communication Preferences:</strong> Whether you consent to receive email updates</li>
                        <li><strong>Verification Data:</strong> Email verification status and timestamps</li>
                    </ul>
                    
                    <h3>Website Usage Information</h3>
                    <p>We automatically collect certain information when you use our website:</p>
                    <ul>
                        <li><strong>Technical Data:</strong> IP address, browser type, device information</li>
                        <li><strong>Usage Analytics:</strong> Pages visited, time spent, interactions (stored locally)</li>
                        <li><strong>Security Data:</strong> Rate limiting and fraud prevention information</li>
                    </ul>
                    
                    <h2>How We Use Your Information</h2>
                    
                    <h3>Membership Management</h3>
                    <ul>
                        <li>Maintain accurate membership records</li>
                        <li>Verify student eligibility and status</li>
                        <li>Provide member benefits and services</li>
                        <li>Generate membership reports for HCC administration</li>
                    </ul>
                    
                    <h3>Communication</h3>
                    <ul>
                        <li>Send important announcements about meetings and events</li>
                        <li>Share educational opportunities and workshops</li>
                        <li>Provide updates about CSA activities and achievements</li>
                        <li>Respond to your questions and support requests</li>
                    </ul>
                    
                    <h3>Event Management</h3>
                    <ul>
                        <li>Send event invitations and reminders</li>
                        <li>Manage RSVPs and attendance tracking</li>
                        <li>Share post-event resources and follow-ups</li>
                        <li>Plan future events based on member interests</li>
                    </ul>
                    
                    <h2>Information Sharing</h2>
                    
                    <h3>We Do Share</h3>
                    <ul>
                        <li><strong>HCC Administration:</strong> Membership numbers and general demographics for reporting requirements</li>
                        <li><strong>CSA Officers:</strong> Aggregate data for planning and decision-making (no individual details without consent)</li>
                        <li><strong>Event Partners:</strong> First names only for name tags and networking (with your consent)</li>
                    </ul>
                    
                    <h3>We Never Share</h3>
                    <ul>
                        <li>Your email address with third parties for marketing</li>
                        <li>Personal information with non-HCC entities</li>
                        <li>Individual academic or personal details without explicit consent</li>
                        <li>Any information for commercial purposes</li>
                    </ul>
                    
                    <h2>Data Security</h2>
                    
                    <h3>Protection Measures</h3>
                    <ul>
                        <li><strong>Encryption:</strong> All data transmission uses HTTPS/TLS encryption</li>
                        <li><strong>Access Control:</strong> Limited access to personal data (officers only, specific purposes)</li>
                        <li><strong>Secure Storage:</strong> Data stored on secure, regularly updated servers</li>
                        <li><strong>Regular Audits:</strong> Periodic security reviews and updates</li>
                    </ul>
                    
                    <h3>Breach Response</h3>
                    <p>
                        In the unlikely event of a data breach, we will:
                    </p>
                    <ul>
                        <li>Immediately secure the affected systems</li>
                        <li>Notify affected members within 72 hours</li>
                        <li>Report to HCC IT Security and relevant authorities</li>
                        <li>Provide guidance on protective steps you can take</li>
                    </ul>
                    
                    <h2>Your Rights</h2>
                    
                    <h3>Access and Control</h3>
                    <p>You have the right to:</p>
                    <ul>
                        <li><strong>Access:</strong> Request a copy of all personal data we have about you</li>
                        <li><strong>Correction:</strong> Ask us to correct any inaccurate information</li>
                        <li><strong>Deletion:</strong> Request removal of your data (subject to record-keeping requirements)</li>
                        <li><strong>Portability:</strong> Receive your data in a machine-readable format</li>
                        <li><strong>Objection:</strong> Opt out of certain uses of your information</li>
                    </ul>
                    
                    <h3>Communication Preferences</h3>
                    <ul>
                        <li>Unsubscribe from emails at any time using the link in any message</li>
                        <li>Update your preferences by contacting us</li>
                        <li>Choose which types of communications you receive</li>
                    </ul>
                    
                    <h2>Data Retention</h2>
                    
                    <h3>Active Members</h3>
                    <p>We retain your information while you are an active member and for:</p>
                    <ul>
                        <li><strong>Membership Records:</strong> 7 years after graduation or last activity</li>
                        <li><strong>Financial Records:</strong> 7 years for audit and tax purposes</li>
                        <li><strong>Communication Logs:</strong> 3 years for reference and continuity</li>
                    </ul>
                    
                    <h3>Former Members</h3>
                    <p>After you leave CSA or graduate:</p>
                    <ul>
                        <li>Marketing emails stop immediately</li>
                        <li>Personal details are archived and access is restricted</li>
                        <li>Alumni may opt into reunion and networking communications</li>
                        <li>Aggregate data may be retained indefinitely for historical records</li>
                    </ul>
                    
                    <h2>Cookies and Tracking</h2>
                    
                    <h3>Essential Cookies</h3>
                    <p>We use necessary cookies for:</p>
                    <ul>
                        <li>User authentication and session management</li>
                        <li>Security and fraud prevention</li>
                        <li>Form submission and error handling</li>
                        <li>Accessibility preferences</li>
                    </ul>
                    
                    <h3>Analytics</h3>
                    <p>We collect anonymous usage statistics to improve our website:</p>
                    <ul>
                        <li>Page views and popular content</li>
                        <li>User flow and navigation patterns</li>
                        <li>Device and browser compatibility</li>
                        <li>Performance and loading times</li>
                    </ul>
                    
                    <p>
                        <strong>Note:</strong> We do not use third-party analytics services like Google Analytics. All data is collected and stored locally for privacy.
                    </p>
                    
                    <h2>Third-Party Services</h2>
                    
                    <h3>Required Services</h3>
                    <ul>
                        <li><strong>CAPTCHA:</strong> reCAPTCHA or hCaptcha for spam prevention (Google/Intuition Machines privacy policies apply)</li>
                        <li><strong>Email Service:</strong> SMTP provider for sending emails (encrypted transmission)</li>
                        <li><strong>HCC Systems:</strong> Integration with college databases for student verification</li>
                    </ul>
                    
                    <h3>Optional Services</h3>
                    <ul>
                        <li><strong>Discord:</strong> Member communication platform (Discord privacy policy applies)</li>
                        <li><strong>Video Conferencing:</strong> Virtual events and meetings (provider privacy policies apply)</li>
                    </ul>
                    
                    <h2>Children's Privacy</h2>
                    <p>
                        CSA is intended for college students who are typically 18 or older. We do not knowingly collect information from children under 13. If we become aware that we have collected personal information from a child under 13, we will delete it immediately.
                    </p>
                    
                    <h2>International Users</h2>
                    <p>
                        Our services are provided from the United States and are subject to U.S. laws. If you are accessing our website from outside the U.S., please be aware that your information may be transferred to, stored, and processed in the United States.
                    </p>
                    
                    <h2>Changes to This Policy</h2>
                    <p>
                        We may update this privacy policy periodically to reflect changes in our practices or applicable laws. When we make significant changes:
                    </p>
                    <ul>
                        <li>We'll notify members via email</li>
                        <li>We'll post a notice on our website</li>
                        <li>We'll update the "Last updated" date</li>
                        <li>We'll maintain previous versions for reference</li>
                    </ul>
                    
                    <h2>Contact Us</h2>
                    <p>
                        If you have questions about this privacy policy or want to exercise your rights, please contact us:
                    </p>
                    
                    <div class="bg-tertiary p-4 border-radius-md mt-4">
                        <h3>CSA Privacy Officer</h3>
                        <p class="mb-2">
                            <strong>Email:</strong> <a href="mailto:president@hccs.edu">president@hccs.edu</a><br>
                            <strong>Subject Line:</strong> Privacy Request - [Your Name]
                        </p>
                        
                        <p class="mb-2">
                            <strong>Mail:</strong><br>
                            Computer Science Association<br>
                            Houston Community College<br>
                            3100 Main Street<br>
                            Houston, TX 77002
                        </p>
                        
                        <p class="mb-0">
                            <strong>Response Time:</strong> We'll respond to privacy requests within 30 days.
                        </p>
                    </div>
                    
                    <h2>Acknowledgment</h2>
                    <p>
                        By joining CSA or using our website, you acknowledge that you have read, understood, and agree to this privacy policy. If you do not agree with any part of this policy, please do not join CSA or use our services.
                    </p>
                    
                    <div class="text-center mt-6">
                        <a href="/join.php" class="btn btn-primary">Ready to Join CSA?</a>
                        <a href="/about.php" class="btn btn-outline">Learn More About CSA</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
