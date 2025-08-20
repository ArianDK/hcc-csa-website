-- Seed data for CSA website
-- Creates a default admin user and sample events

-- Insert default admin (password: "admin123" - CHANGE THIS!)
INSERT INTO admins (email, pass_hash, role) VALUES 
('admin@hccs.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'PRESIDENT');

-- Insert sample events
INSERT INTO events (title, summary, start_time, end_time, location, rsvp_url) VALUES 
(
  'Welcome Back Meeting', 
  'Start the semester with introductions, club overview, and pizza! Learn about upcoming events and how to get involved.',
  '2024-02-15 18:00:00',
  '2024-02-15 19:30:00',
  'HCC Main Campus - Room 205',
  NULL
),
(
  'Python Workshop: Data Science Basics',
  'Hands-on workshop covering pandas, matplotlib, and basic data analysis. Bring your laptop!',
  '2024-02-22 17:00:00',
  '2024-02-22 19:00:00',
  'HCC Northeast Campus - Computer Lab',
  NULL
),
(
  'Spring Hackathon Planning',
  'Help us plan our biggest event of the semester! We need volunteers for logistics, marketing, and judging.',
  '2024-03-01 18:00:00',
  '2024-03-01 19:00:00',
  'Virtual Meeting (Discord)',
  'https://discord.gg/hcc-csa'
);

-- Note: In production, change the admin password immediately
-- Password hash above is for "admin123" - use for initial setup only
