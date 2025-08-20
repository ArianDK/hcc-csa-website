<?php
/**
 * CSA Website - Events Page
 */

session_start();

require_once __DIR__ . '/config/database.php';

$pageTitle = 'Events - Computer Science Association at HCC';
$pageDescription = 'Upcoming events, workshops, and activities for CSA members at Houston Community College.';

// Get upcoming events
try {
    $stmt = Database::prepare("
        SELECT id, title, summary, description, start_time, end_time, location, rsvp_url 
        FROM events 
        WHERE start_time > NOW() 
        ORDER BY start_time ASC
        LIMIT 6
    ");
    $stmt->execute();
    $upcomingEvents = $stmt->fetchAll();
} catch (Exception $e) {
    $upcomingEvents = [];
    error_log('Failed to fetch events: ' . $e->getMessage());
}

// Get past events for reference
try {
    $stmt = Database::prepare("
        SELECT id, title, summary, description, start_time, end_time, location 
        FROM events 
        WHERE start_time <= NOW() 
        ORDER BY start_time DESC 
        LIMIT 2
    ");
    $stmt->execute();
    $pastEvents = $stmt->fetchAll();
} catch (Exception $e) {
    $pastEvents = [];
    error_log('Failed to fetch past events: ' . $e->getMessage());
}

include __DIR__ . '/partials/meta.php';
include __DIR__ . '/partials/header.php';
?>

<style>
/* Custom styling for event cards to allow dynamic heights */
.events-grid {
    display: grid;
    gap: var(--spacing-6);
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    align-items: start; /* This prevents equal heights */
}

.events-grid-past {
    display: grid;
    gap: var(--spacing-6);
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    align-items: start; /* This prevents equal heights */
}

/* Ensure event cards flex naturally with their content */
.events-grid .card,
.events-grid-past .card {
    height: auto;
    align-self: start;
}
</style>

<main id="main-content">
    <!-- Hero Section -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h1>CSA Events</h1>
                <p class="text-lg text-secondary">Workshops, hackathons, and networking opportunities for all STEM students</p>
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Upcoming Events</h2>
                <p class="text-secondary">Don't miss these exciting opportunities</p>
            </div>
            
            <?php if (!empty($upcomingEvents)): ?>
                <div class="events-grid">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <div class="text-muted">
                                    <?php echo date('l, F j, Y', strtotime($event['start_time'])); ?><br>
                                    <?php echo date('g:i A', strtotime($event['start_time'])); ?>
                                    <?php if ($event['end_time']): ?>
                                        - <?php echo date('g:i A', strtotime($event['end_time'])); ?>
                                    <?php endif; ?>
                                    <?php if ($event['location']): ?>
                                        <br><?php echo htmlspecialchars($event['location']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><?php echo htmlspecialchars($event['summary']); ?></p>
                                <?php if (!empty($event['description']) && $event['description'] !== $event['summary']): ?>
                                    <div class="event-description-container">
                                        <a href="#" class="show-more-link" data-event-id="<?php echo $event['id']; ?>" style="color: var(--primary-color); text-decoration: none; font-size: 0.9em; font-weight: 500;">Show more</a>
                                        <div class="event-full-description" data-description-id="<?php echo $event['id']; ?>" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                                            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <?php if ($event['rsvp_url']): ?>
                                    <a href="<?php echo htmlspecialchars($event['rsvp_url']); ?>" 
                                       class="btn btn-primary" target="_blank">RSVP</a>
                                <?php endif; ?>
                                <button class="btn btn-primary" onclick="addToCalendar(<?php echo $event['id']; ?>)">
                                    Add to Calendar
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <h3>No upcoming events scheduled</h3>
                    <p class="text-muted">We're planning some exciting events! Check back soon or join our Discord for updates.</p>
                    <div class="mt-4">
                        <a href="join.php" class="btn btn-primary">Join to Get Notified</a>
                        <a href="https://discord.gg/hcc-csa" class="btn btn-secondary" target="_blank">Join Discord</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Event Types -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Types of Events We Host</h2>
                <p class="text-secondary">Something for everyone in the STEM community</p>
            </div>
            
            <div class="grid grid-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Technical Workshops</h3>
                        <p>Hands-on coding sessions, programming tutorials, and skill-building workshops for all levels.</p>
                        <ul class="text-left mt-3">
                            <li>Python & Data Science</li>
                            <li>Web Development</li>
                            <li>Mobile App Development</li>
                            <li>Cybersecurity Basics</li>
                            <li>AI/ML Fundamentals</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Hackathons & Competitions</h3>
                        <p>Collaborative coding events where you build projects, solve problems, and compete for prizes.</p>
                        <ul class="text-left mt-3">
                            <li>24-hour hackathons</li>
                            <li>Theme-based challenges</li>
                            <li>Team building exercises</li>
                            <li>Industry partnerships</li>
                            <li>Cash & prize awards</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Career & Professional</h3>
                        <p>Industry insights, networking opportunities, and professional development sessions.</p>
                        <ul class="text-left mt-3">
                            <li>Industry guest speakers</li>
                            <li>Resume & interview prep</li>
                            <li>Company field trips</li>
                            <li>Networking mixers</li>
                            <li>Internship fairs</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Academic Support</h3>
                        <p>Study sessions, tutoring, and academic resources to help you succeed in your courses.</p>
                        <ul class="text-left mt-3">
                            <li>Exam prep sessions</li>
                            <li>Study groups</li>
                            <li>Peer tutoring</li>
                            <li>Project collaboration</li>
                            <li>Transfer guidance</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Social & Community</h3>
                        <p>Fun events to build friendships and strengthen our community bonds.</p>
                        <ul class="text-left mt-3">
                            <li>Welcome mixers</li>
                            <li>Game nights</li>
                            <li>Tech trivia</li>
                            <li>Movie nights</li>
                            <li>End-of-semester celebrations</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body text-center">
                        <h3>Community Service</h3>
                        <p>Technology-focused volunteer opportunities to give back to the Houston community.</p>
                        <ul class="text-left mt-3">
                            <li>Digital literacy training</li>
                            <li>Computer refurbishment</li>
                            <li>Coding for nonprofits</li>
                            <li>STEM outreach</li>
                            <li>Tech accessibility projects</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Past Events (if any) -->
    <?php if (!empty($pastEvents)): ?>
    <section class="section bg-secondary">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Recent Events</h2>
                <p class="text-secondary">See what we've been up to</p>
            </div>
            
            <div class="events-grid-past">
                <?php foreach ($pastEvents as $event): ?>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h4>
                            <div class="text-muted">
                                <?php echo date('M j, Y', strtotime($event['start_time'])); ?>
                                <?php if ($event['location']): ?>
                                    <br><?php echo htmlspecialchars($event['location']); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><?php echo htmlspecialchars($event['summary']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Event Guidelines -->
    <section class="section">
        <div class="container">
            <div class="text-center mb-8">
                <h2>Event Guidelines</h2>
                <p class="text-secondary">Making our events welcoming and productive for everyone</p>
            </div>
            
            <div class="grid grid-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">What to Expect</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>Inclusive Environment:</strong> Everyone is welcome regardless of skill level</li>
                            <li><strong>Learning Focus:</strong> Events prioritize education and skill development</li>
                            <li><strong>Collaboration:</strong> We encourage teamwork and peer learning</li>
                            <li><strong>Accessibility:</strong> All venues are wheelchair accessible</li>
                            <li><strong>Food:</strong> Light refreshments usually provided</li>
                        </ul>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">How to Participate</h3>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>RSVP:</strong> Please register in advance when possible</li>
                            <li><strong>Bring:</strong> Laptop, notebook, and enthusiasm</li>
                            <li><strong>Arrive Early:</strong> Setup and networking start 15 minutes before</li>
                            <li><strong>Stay Connected:</strong> Join our Discord for real-time updates</li>
                            <li><strong>Give Feedback:</strong> Help us improve future events</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section bg-secondary">
        <div class="container text-center">
            <h2>Ready to Join Our Events?</h2>
            <p class="text-lg text-secondary mb-6">Become a CSA member and never miss an opportunity</p>
            
            <div class="flex justify-center flex-wrap" style="gap: 2rem;">
                <a href="join.php" class="btn btn-secondary btn-lg">Join CSA</a>
                <a href="about.php" class="btn btn-secondary btn-lg">Learn More</a>
            </div>
        </div>
    </section>
</main>

<script>
// Add to calendar functionality
function addToCalendar(eventId) {
    // This would generate an iCal file for the specific event
    alert('Calendar functionality coming soon! For now, please note the date manually or check our Discord for calendar links.');
    
    // In a full implementation, you would:
    // 1. Make an AJAX call to get event details
    // 2. Generate iCal data
    // 3. Trigger download or open calendar app
}

// Event delegation for show more links
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('show-more-link')) {
            e.preventDefault();
            
            const eventId = e.target.getAttribute('data-event-id');
            const descriptionDiv = document.querySelector(`[data-description-id="${eventId}"]`);
            
            if (descriptionDiv) {
                if (descriptionDiv.style.display === 'none' || descriptionDiv.style.display === '') {
                    descriptionDiv.style.display = 'block';
                    e.target.textContent = 'Show less';
                } else {
                    descriptionDiv.style.display = 'none';
                    e.target.textContent = 'Show more';
                }
            }
        }
    });
});

// Track event interactions
document.addEventListener('click', function(e) {
    if (e.target.matches('a[href*="rsvp"]')) {
        if (window.CSA && window.CSA.Analytics) {
            window.CSA.Analytics.trackCustomEvent('events', 'rsvp_click', {
                event_title: e.target.closest('.card').querySelector('.card-title').textContent
            });
        }
    }
});
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
