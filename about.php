<?php
/**
 * CSA Website - About Page
 */

session_start();

$pageTitle = 'About CSA - Computer Science Association at HCC';
$pageDescription = 'Learn about the Computer Science Association at Houston Community College. Our mission, leadership, and how we support STEM students.';

include __DIR__ . '/partials/meta.php';
include __DIR__ . '/partials/header.php';
?>

<main id="main-content">
    <!-- Hero Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h1>About CSA</h1>
                <p class="text-lg text-secondary">Building the next generation of tech leaders at Houston Community College</p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="grid grid-2 items-center">
                <div>
                    <h2>Our Mission</h2>
                    <p class="text-lg">
                        <strong>Train, prepare, and unite students for the ever-changing technological frontiers so our members become pioneers of new technologies.</strong>
                    </p>
                    <p>
                        The Computer Science Association at Houston Community College is dedicated to fostering a community of passionate STEM students who are committed to learning, growing, and innovating together.
                    </p>
                    <p>
                        We believe that technology has the power to solve the world's greatest challenges, and our members are the future leaders who will make that happen.
                    </p>
                </div>
                <div class="text-center">
                    <h3>Innovation Through Community</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Do Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>What We Do</h2>
                <p class="text-secondary">Comprehensive support for your academic and professional journey</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Academic Support</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Study groups for challenging courses</li>
                            <li>Peer tutoring and mentorship</li>
                            <li>Project collaboration opportunities</li>
                            <li>Academic resources and guidance</li>
                            <li>Transfer preparation assistance</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Professional Development</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Resume and LinkedIn optimization</li>
                            <li>Interview preparation workshops</li>
                            <li>Industry networking events</li>
                            <li>Internship and job search support</li>
                            <li>Professional skills development</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Technical Skills</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Programming workshops (Python, Java, C++, JavaScript, etc.)</li>
                            <li>Web development bootcamps</li>
                            <li>Data science and analytics training</li>
                            <li>Cybersecurity fundamentals</li>
                            <li>Emerging technology exploration</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Competitions & Events</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Hackathons and coding competitions</li>
                            <li>Tech talks and guest speakers</li>
                            <li>Industry field trips</li>
                            <li>Social and networking events</li>
                            <li>Community service projects</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Membership Section -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Membership & Participation</h2>
                <p class="text-secondary">Open to all STEM students at Houston Community College</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Who Can Join</h3>
                        <p>Any currently enrolled HCC student in a STEM program or with interest in technology.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Membership Fees</h3>
                        <p>Absolutely free! No dues, no hidden costs. Just your enthusiasm and commitment to learning.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Requirements</h3>
                        <p>Attend at least one event per semester and maintain good academic standing.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Leadership Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Leadership Team</h2>
                <p class="text-secondary">Dedicated students leading CSA into the future</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3>President</h3>
                        <p><strong>Elected annually by members</strong></p>
                        <p class="text-muted">
                            Leads the organization, represents CSA to the college administration, and coordinates with other student organizations.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Vice President</h3>
                        <p><strong>Elected annually by members</strong></p>
                        <p class="text-muted">
                            Assists the president, coordinates events and workshops, and leads special projects and initiatives.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Secretary/Treasurer</h3>
                        <p><strong>Elected annually by members</strong></p>
                        <p class="text-muted">
                            Manages membership records, meeting minutes, and coordinates with HCC for room bookings and resources.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Faculty Advisor</h3>
                        <p><strong>HCC Computer Science Faculty</strong></p>
                        <p class="text-muted">
                            Provides guidance, ensures alignment with college policies, and supports student leadership development.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Communications Lead</h3>
                        <p><strong>Appointed by officers</strong></p>
                        <p class="text-muted">
                            Manages social media, website updates, and ensures effective communication with members.
                        </p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Event Coordinators</h3>
                        <p><strong>Volunteer positions</strong></p>
                        <p class="text-muted">
                            Help plan and execute workshops, hackathons, social events, and networking opportunities.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Elections & Governance -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Elections & Governance</h2>
                <p class="text-secondary">Democratic leadership for student empowerment</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Election Process</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>When:</strong> Held each spring semester for the following academic year</li>
                            <li><strong>Eligibility:</strong> Any active member in good standing can run for office</li>
                            <li><strong>Voting:</strong> All board members can vote via secure online ballot</li>
                            <li><strong>Terms:</strong> One academic year (fall and spring semesters)</li>
                            <li><strong>Transition:</strong> New officers take office at the start of fall semester</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Constitution & Bylaws</h3>
                    </div>
                    <div class="card-body">
                        <p>CSA operates under a constitution that outlines our mission, membership requirements, leadership structure, and operational procedures.</p>
                        <ul>
                            <li>Approved by HCC Student Activities Office</li>
                            <li>Updated annually as needed</li>
                            <li>Available for all members to review</li>
                            <li>Ensures fair and transparent governance</li>
                        </ul>
                        <div class="mt-4">
                            <a href="/assets/docs/csa-constitution.pdf" class="btn btn-primary" target="_blank">
                                Download Constitution (PDF)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Campus Locations -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>HCC Campus Locations</h2>
                <p class="text-secondary">CSA serves students across all Houston Community College campuses</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Main Campuses</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>Central Campus</strong> - Downtown Houston</li>
                            <li><strong>Northeast Campus</strong> - Northeast Houston</li>
                            <li><strong>Northwest Campus</strong> - Northwest Houston</li>
                            <li><strong>Southeast Campus</strong> - Southeast Houston</li>
                            <li><strong>Southwest Campus</strong> - Southwest Houston</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Meeting Locations</h3>
                    </div>
                    <div class="card-body">
                        <p>Most events are held at West Loop Campus, but we also organize activities at other locations:</p>
                        <ul>
                            <li>Campus computer labs for hands-on workshops</li>
                            <li>Auditoriums for large events and guest speakers</li>
                            <li>Virtual meetings via Discord for accessibility</li>
                            <li>Off-campus field trips to tech companies</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section">
        <div class="container text-center">
            <h2>Ready to Get Involved?</h2>
            <p class="text-lg text-secondary mb-6">Join our community of innovative STEM students</p>
            
            <div class="flex justify-center flex-wrap" style="gap: 2rem;">
                <a href="<?php echo $config['app']['base_url']; ?>/join.php" class="btn btn-secondary btn-lg">Join CSA</a>
                <a href="<?php echo $config['app']['base_url']; ?>/events.php" class="btn btn-secondary btn-lg">View Events</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
