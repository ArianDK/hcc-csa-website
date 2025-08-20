<?php
/**
 * CSA Website - Home Page
 */

session_start();

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Computer Science Association - HCC';
$pageDescription = 'Join the Computer Science Association at Houston Community College. Build skills, projects, and community - open to all STEM majors.';

// Get upcoming events
try {
    $stmt = Database::prepare("
        SELECT title, summary, start_time, location, rsvp_url 
        FROM events 
        WHERE start_time > NOW() 
        ORDER BY start_time ASC 
        LIMIT 3
    ");
    $stmt->execute();
    $upcomingEvents = $stmt->fetchAll();
} catch (Exception $e) {
    $upcomingEvents = [];
    error_log('Failed to fetch events: ' . $e->getMessage());
}

// Get member count (for social proof)
try {
    $stmt = Database::prepare("SELECT COUNT(*) as count FROM members WHERE status = 'VERIFIED'");
    $stmt->execute();
    $memberCount = $stmt->fetch()['count'] ?? 0;
} catch (Exception $e) {
    $memberCount = 0;
    error_log('Failed to fetch member count: ' . $e->getMessage());
}

include __DIR__ . '/partials/meta.php';
include __DIR__ . '/partials/header.php';
?>

<main id="main-content">
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 class="hero-title">HCC's Computer Science Association (CSA)</h1>
            <p class="hero-subtitle">Build skills, projects, and community. Open to all STEM majors.</p>
        </div>
    </section>

    <!-- Why Join Section -->
    <section id="why-join" class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Why Join CSA?</h2>
                <p class="text-secondary">Everything you need to succeed in tech, all in one place.</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-body">
                        <div style="font-size: 2rem; margin-bottom: 1rem;"></div>
                        <h3 class="card-title">Skills Workshops</h3>
                        <p>Hands-on workshops covering programming languages, web development, data science, and emerging technologies.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div style="font-size: 2rem; margin-bottom: 1rem;"></div>
                        <h3 class="card-title">Hackathons</h3>
                        <p>Participate in exciting coding competitions and hackathons. Win prizes while building real projects.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div style="font-size: 2rem; margin-bottom: 1rem;"></div>
                        <h3 class="card-title">Career Prep</h3>
                        <p>Resume reviews, interview practice, job search strategies, and connections with industry professionals.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div style="font-size: 2rem; margin-bottom: 1rem;"></div>
                        <h3 class="card-title">Mentorship</h3>
                        <p>Connect with upperclassmen, alumni, and industry mentors who can guide your academic and career journey.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div style="font-size: 2rem; margin-bottom: 1rem;"></div>
                        <h3 class="card-title">Community</h3>
                        <p>Build lasting friendships with fellow STEM students who share your passion for technology and innovation.</p>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div style="font-size: 2rem; margin-bottom: 1rem;"></div>
                        <h3 class="card-title">Projects</h3>
                        <p>Collaborate on real-world projects that make a difference while building your portfolio and experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Upcoming Events Section -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Upcoming Events</h2>
                <p class="text-secondary">Don't miss out on these exciting opportunities</p>
            </div>
            
            <?php if (!empty($upcomingEvents)): ?>
                <div class="grid grid-2">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="text-muted">
                                    <?php echo date('F j, Y \a\t g:i A', strtotime($event['start_time'])); ?>
                                    <?php if ($event['location']): ?>
                                        <br><?php echo htmlspecialchars($event['location']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($event['summary']); ?></p>
                            </div>
                            <?php if ($event['rsvp_url']): ?>
                                <div class="card-footer">
                                    <a href="<?php echo htmlspecialchars($event['rsvp_url']); ?>" class="btn btn-primary" target="_blank">RSVP</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="text-center mt-6">
                    <a href="<?php echo $config['app']['base_url']; ?>/events.php" class="btn btn-outline">View All Events</a>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <h3>No upcoming events scheduled</h3>
                    <p class="text-muted">Check back soon for exciting new events and workshops!</p>
                    <a href="<?php echo $config['app']['base_url']; ?>/join.php" class="btn btn-primary">Join to Get Notified</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- STEM Welcome Section -->
    <section class="section">
        <div class="container">
            <div class="grid grid-2 items-center">
                <div>
                    <h2>Open to All STEM Majors</h2>
                    <p class="text-lg">Most of our members are Computer Science and AI majors, but CSA welcomes all STEM students at HCC:</p>
                    <ul class="mt-4">
                        <li>Computer Science & Information Technology</li>
                        <li>Artificial Intelligence & Machine Learning</li>
                        <li>Mathematics & Statistics</li>
                        <li>Engineering (All Disciplines)</li>
                        <li>Biology & Life Sciences</li>
                        <li>Chemistry & Physical Sciences</li>
                        <li>Environmental & Earth Sciences</li>
                        <li>Health & Medical Sciences</li>
                    </ul>
                    <p class="mt-4">
                        <strong>Our Mission:</strong> Train, prepare, and unite students for the ever-changing technological frontiers so our members become pioneers of new technologies.
                    </p>
                </div>
                <div class="text-center">
                    <h3>All STEM majors are welcome</h3>
                    <p class="text-muted">Diversity in disciplines strengthens our community</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Social Proof Section -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Join <?php echo $memberCount > 0 ? $memberCount . '+' : 'Our Growing'; ?> Community</h2>
                <p class="text-secondary">What our members say about CSA</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-body">
                        <p>"CSA helped me land my first internship! The career prep workshops and networking events were game-changers."</p>
                        <div class="mt-4">
                            <strong>Sarah M.</strong><br>
                            <small class="text-muted">Computer Science, Spring 2023</small>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <p>"As a Biology major, I wasn't sure if I'd fit in. But CSA welcomed me with open arms and taught me valuable coding skills for bioinformatics."</p>
                        <div class="mt-4">
                            <strong>Marcus L.</strong><br>
                            <small class="text-muted">Biology, Fall 2023</small>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <p>"The hackathons are amazing! I've built three projects that are now in my portfolio. Plus, the pizza is always great!"</p>
                        <div class="mt-4">
                            <strong>Alex T.</strong><br>
                            <small class="text-muted">Engineering, Current Member</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- HCC Partnership -->
            <div class="text-center mt-8">
                <img src="<?php echo $config['app']['base_url']; ?>/images/hcc-logo.png" alt="Houston Community College" style="height: 60px; opacity: 0.7;">
                <p class="text-muted mt-2">
                    <small>
                        <strong>Note:</strong> HCC logo is a placeholder. Official branding will be used only with college approval. 
                        CSA is a student organization at Houston Community College.
                    </small>
                </p>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="section">
        <div class="container text-center">
            <h2>Ready to Join?</h2>
            <p class="text-lg text-secondary mb-6">Become part of Houston's most active tech student community</p>
            
            <div class="flex justify-center flex-wrap" style="gap: 2rem;">
                <a href="<?php echo $config['app']['base_url']; ?>/join.php" class="btn btn-secondary btn-lg">Join CSA Now</a> <!-- btn-primary btn-lg dark blue button --> 
                <a href="<?php echo $config['app']['base_url']; ?>/about.php" class="btn btn-secondary btn-lg">Learn More</a> <!-- btn-outline btn-lg transparent button that thurns dark blue on hover --> 
            </div>
            
            <p class="mt-6 text-muted">
                <small>Free to join • Open to all HCC students • Inclusive community</small>
            </p>
        </div>
    </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
