<?php
/**
 * CSA Website - Get Involved Page
 */

session_start();

$pageTitle = 'Get Involved - Computer Science Association at HCC';
$pageDescription = 'Ways to get involved with CSA at Houston Community College. Committees, volunteering, leadership, and more.';

include __DIR__ . '/partials/meta.php';
include __DIR__ . '/partials/header.php';
?>

<main id="main-content">
    <!-- Hero Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h1>Get Involved with CSA</h1>
                <p class="text-lg text-secondary">Make an impact, build skills, and lead your community</p>
            </div>
        </div>
    </section>

    <!-- Involvement Levels -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Choose Your Level of Involvement</h2>
                <p class="text-secondary">From casual participation to leadership roles</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Member</h3>
                        <p class="text-muted">Perfect for anyone</p>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Attend events that interest you</li>
                            <li>Participate in Discord discussions</li>
                            <li>Access exclusive resources</li>
                            <li>Minimum commitment: 1 event per semester</li>
                        </ul>
                        <div class="mt-4">
                            <a href="/join.php" class="btn btn-secondary">Join Now</a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Leadership</h3>
                        <p class="text-muted">Lead the organization</p>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Run for elected officer positions</li>
                            <li>Set organizational direction</li>
                            <li>Represent CSA to HCC administration</li>
                            <li>Build resume-worthy experience</li>
                        </ul>
                        <div class="mt-4">
                            <a href="#leadership" class="btn btn-secondary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Committees 
    <section id="committees" class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Join a Committee</h2>
                <p class="text-secondary">Make a direct impact in areas you're passionate about</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Events Committee</h3>
                        <p class="text-muted">Plan and execute amazing events</p>
                    </div>
                    <div class="card-body">
                        <p><strong>What you'll do:</strong></p>
                        <ul>
                            <li>Brainstorm and plan workshop topics</li>
                            <li>Coordinate with speakers and venues</li>
                            <li>Manage event logistics and setup</li>
                            <li>Gather feedback and improve future events</li>
                        </ul>
                        <p><strong>Skills you'll gain:</strong> Project management, vendor relations, event planning</p>
                        <p><strong>Time commitment:</strong> 3-5 hours per month</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Technology Committee</h3>
                        <p class="text-muted">Maintain and improve our digital presence</p>
                    </div>
                    <div class="card-body">
                        <p><strong>What you'll do:</strong></p>
                        <ul>
                            <li>Update and maintain the CSA website</li>
                            <li>Manage Discord server and integrations</li>
                            <li>Create digital resources and tools</li>
                            <li>Support virtual event technology</li>
                        </ul>
                        <p><strong>Skills you'll gain:</strong> Web development, server administration, digital marketing</p>
                        <p><strong>Time commitment:</strong> 2-4 hours per month</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Marketing & Outreach</h3>
                        <p class="text-muted">Spread the word about CSA</p>
                    </div>
                    <div class="card-body">
                        <p><strong>What you'll do:</strong></p>
                        <ul>
                            <li>Create social media content and campaigns</li>
                            <li>Design flyers and promotional materials</li>
                            <li>Coordinate with other student organizations</li>
                            <li>Recruit new members and promote events</li>
                        </ul>
                        <p><strong>Skills you'll gain:</strong> Digital marketing, graphic design, communications</p>
                        <p><strong>Time commitment:</strong> 3-4 hours per month</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Partnerships Committee</h3>
                        <p class="text-muted">Build relationships with industry and community</p>
                    </div>
                    <div class="card-body">
                        <p><strong>What you'll do:</strong></p>
                        <ul>
                            <li>Reach out to local tech companies</li>
                            <li>Coordinate guest speakers and field trips</li>
                            <li>Secure sponsorships for events</li>
                            <li>Build relationships with alumni</li>
                        </ul>
                        <p><strong>Skills you'll gain:</strong> Business development, networking, communication</p>
                        <p><strong>Time commitment:</strong> 2-5 hours per month</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Academic Support</h3>
                        <p class="text-muted">Help fellow students succeed</p>
                    </div>
                    <div class="card-body">
                        <p><strong>What you'll do:</strong></p>
                        <ul>
                            <li>Organize study groups and tutoring</li>
                            <li>Create and maintain resource libraries</li>
                            <li>Coordinate exam prep sessions</li>
                            <li>Mentor struggling students</li>
                        </ul>
                        <p><strong>Skills you'll gain:</strong> Teaching, mentoring, curriculum development</p>
                        <p><strong>Time commitment:</strong> 4-6 hours per month</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Community Service</h3>
                        <p class="text-muted">Use tech skills to help others</p>
                    </div>
                    <div class="card-body">
                        <p><strong>What you'll do:</strong></p>
                        <ul>
                            <li>Organize tech-focused volunteer projects</li>
                            <li>Teach digital literacy to community members</li>
                            <li>Refurbish computers for donation</li>
                            <li>Support nonprofit technology needs</li>
                        </ul>
                        <p><strong>Skills you'll gain:</strong> Project management, community engagement, technical training</p>
                        <p><strong>Time commitment:</strong> 3-6 hours per month</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
-->
    <!-- Leadership Opportunities -->
    <section id="leadership" class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Leadership Opportunities</h2>
                <p class="text-secondary">Take on responsibility and make lasting change</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Elected Positions</h3>
                    </div>
                    <div class="card-body">
                        <h4>President</h4>
                        <ul>
                            <li>Lead the organization and set strategic direction</li>
                            <li>Represent CSA to HCC administration</li>
                            <li>Coordinate with other student organizations</li>
                            <li>Run meetings and make executive decisions</li>
                        </ul>
                        
                        <h4>Vice President</h4>
                        <ul>
                            <li>Support the president and lead special initiatives</li>
                            <li>Coordinate events and activities</li>
                            <li>Manage committee chairs</li>
                            <li>Step in for president when needed</li>
                        </ul>
                        
                        <h4>Secretary/Treasurer</h4>
                        <ul>
                            <li>Maintain membership records and meeting minutes</li>
                            <li>Manage organizational finances and budgets</li>
                            <li>Handle administrative tasks and documentation</li>
                            <li>Coordinate with HCC for resources and space</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Appointed Roles</h3>
                    </div>
                    <div class="card-body">
                        <h4>Committee Chairs</h4>
                        <ul>
                            <li>Lead one of our six committees</li>
                            <li>Recruit and manage committee volunteers</li>
                            <li>Report to officers on committee activities</li>
                            <li>Coordinate with other committees</li>
                        </ul>
                        
                        <h4>Special Project Leaders</h4>
                        <ul>
                            <li>Lead major initiatives (hackathons, conferences)</li>
                            <li>Manage project timelines and deliverables</li>
                            <li>Work with sponsors and external partners</li>
                            <li>Build cross-functional teams</li>
                        </ul>
                        
                        <h4>Mentorship Coordinators</h4>
                        <ul>
                            <li>Match new members with experienced mentors</li>
                            <li>Organize mentorship training and resources</li>
                            <li>Track mentorship program effectiveness</li>
                            <li>Support both mentors and mentees</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-6">
                <h3>Ready to Lead?</h3>
                <p>Elections are held each spring for the following academic year.</p>
                <a href="/about.php#elections" class="btn btn-primary">Learn About Elections</a>
            </div>
        </div>
    </section>

    <!-- Special Projects -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Special Projects</h2>
                <p class="text-secondary">Major initiatives that make a big impact</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Annual Hackathon</h3>
                        <p>Our biggest event of the year - a 24-hour coding competition with prizes, workshops, and networking.</p>
                        <ul class="text-left">
                            <li>Recruiting sponsors and judges</li>
                            <li>Organizing workshops and activities</li>
                            <li>Managing logistics and catering</li>
                            <li>Coordinating with media and marketing</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Tech Conference</h3>
                        <p>Professional development conference featuring industry speakers and career resources.</p>
                        <ul class="text-left">
                            <li>Securing keynote speakers</li>
                            <li>Organizing workshops and panels</li>
                            <li>Managing registration and attendees</li>
                            <li>Coordinating vendor exhibition</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Community Tech Center</h3>
                        <p>Long-term project to establish a computer lab for underserved community members.</p>
                        <ul class="text-left">
                            <li>Fundraising and grant writing</li>
                            <li>Partnership development</li>
                            <li>Curriculum and training development</li>
                            <li>Volunteer coordination</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Get Started -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>How to Get Started</h2>
                <p class="text-secondary">Ready to get more involved? Here's how:</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Jump Right In</h3>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li><strong>Join CSA</strong> if you haven't already</li>
                            <li><strong>Attend a few events</strong> to get a feel for our community</li>
                            <li><strong>Join our Discord</strong> and introduce yourself</li>
                            <li><strong>Express interest</strong> in committees or roles that appeal to you</li>
                            <li><strong>Start volunteering</strong> at events or with projects</li>
                        </ol>
                        <div class="mt-4">
                            <a href="/join.php" class="btn btn-primary">Join CSA</a>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Talk to Us</h3>
                    </div>
                    <div class="card-body">
                        <p>Have questions about getting involved? Want to learn more about specific opportunities?</p>
                        
                        <div class="mt-4">
                            <h4>Contact Options:</h4>
                            <ul>
                                <li><strong>Discord:</strong> Join our server for real-time chat</li>
                                <li><strong>Email:</strong> president@hccs.edu</li>
                                <li><strong>In Person:</strong> Attend any CSA event</li>
                                <li><strong>Office Hours:</strong> Check Discord for current availability</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4">
                            <a href="https://discord.gg/hcc-csa" class="btn btn-secondary" target="_blank">Join Discord</a>
                            <a href="mailto:president@hccs.edu" class="btn btn-outline">Send Email</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits of Involvement -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Why Get More Involved?</h2>
                <p class="text-secondary">The benefits go far beyond your resume</p>
            </div>
            
            <div class="grid grid-4">
                <div class="text-center">
                    <h3>Leadership Skills</h3>
                    <p>Learn to manage teams, projects, and complex initiatives that employers value.</p>
                </div>
                
                <div class="text-center">
                    <h3>Professional Network</h3>
                    <p>Build relationships with peers, faculty, industry professionals, and alumni.</p>
                </div>
                
                <div class="text-center">
                    <h3>Resume Builder</h3>
                    <p>Gain concrete experience and achievements that set you apart from other candidates.</p>
                </div>
                
                <div class="text-center">
                    <h3>Skill Development</h3>
                    <p>Practice communication, project management, and technical skills in real scenarios.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section bg-secondary">
        <div class="container text-center">
            <h2>Ready to Make an Impact?</h2>
            <p class="text-lg text-secondary mb-6">Your journey as a leader in tech starts here</p>
            
            <div class="flex justify-center flex-wrap" style="gap: 2rem;">
                <a href="join.php" class="btn btn-secondary btn-lg">Join CSA</a>
                <a href="mailto:president@hccs.edu" class="btn btn-secondary btn-lg">Contact Leadership</a>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
