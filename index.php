<?php
session_start();
require_once 'includes/db.php';

// Try to fetch classes dynamically from DB
$classes = [];
$trainers = [];
$db_connected = false;

try {
    $pdo = getDB();
    $db_connected = true;
    
    // Fetch classes with booking counts
    $stmt = $pdo->query("
        SELECT c.*, COUNT(b.id) as booked_count 
        FROM classes c 
        LEFT JOIN bookings b ON c.id = b.class_id AND b.status IN ('pending', 'accepted')
        GROUP BY c.id
    ");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fetch trainers from users table
    $stmtTrainers = $pdo->query("SELECT id, name, email FROM users WHERE role = 'trainer'");
    $trainers = $stmtTrainers->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $db_connected = false;
}

// Fallback to default mock classes if DB is empty or disconnected
if (empty($classes)) {
    $classes = [
        [
            'id' => 1,
            'class_name' => 'Yoga Flow',
            'instructor' => 'Emma Watson',
            'schedule' => 'Mon/Wed 8am',
            'capacity' => 15,
            'booked_count' => 8
        ],
        [
            'id' => 2,
            'class_name' => 'HIIT Blast',
            'instructor' => 'Mike Ross',
            'schedule' => 'Tue/Thu 6pm',
            'capacity' => 20,
            'booked_count' => 14
        ],
        [
            'id' => 3,
            'class_name' => 'Spin Cycle',
            'instructor' => 'Sarah Trainer',
            'schedule' => 'Fri 5pm',
            'capacity' => 12,
            'booked_count' => 5
        ]
    ];
}

// Fallback to default trainers if DB is empty or disconnected
if (empty($trainers)) {
    $trainers = [
        ['name' => 'Sarah Trainer', 'specialty' => 'Spin & HIIT Specialist', 'bio' => 'Passionate about high-intensity cardio and functional strength training.'],
        ['name' => 'Emma Watson', 'specialty' => 'Yoga & Mindfulness Coach', 'bio' => 'Focuses on alignment, flow, and connecting breath to movement.'],
        ['name' => 'Mike Ross', 'specialty' => 'Strength & Conditioning', 'bio' => 'Helps members build power, mobility, and progressive strength safely.']
    ];
} else {
    // Inject specialties and bios for database trainers
    foreach ($trainers as &$t) {
        if ($t['name'] === 'Sarah Trainer') {
            $t['specialty'] = 'Spin & HIIT Specialist';
            $t['bio'] = 'Passionate about high-intensity cardio and functional strength training.';
        } elseif ($t['name'] === 'Emma Watson') {
            $t['specialty'] = 'Yoga & Mindfulness Coach';
            $t['bio'] = 'Focuses on alignment, flow, and connecting breath to movement.';
        } elseif ($t['name'] === 'Mike Ross') {
            $t['specialty'] = 'Strength & Conditioning Coach';
            $t['bio'] = 'Helps members build power, mobility, and progressive strength safely.';
        } else {
            $t['specialty'] = 'Certified Fitness Coach';
            $t['bio'] = 'Dedicated to helping members achieve their personal fitness milestones.';
        }
    }
}

// Check logged in state
$isLoggedIn = isset($_SESSION['user_id']);
$dashboardLink = 'pages/dashboard.php';
$loginLink = 'login.php';
$registerLink = 'register.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitFlow AI - Smart Gym Class Booking System</title>
    <meta name="description" content="Elevate your workout routine with personalized AI-powered gym class matches, live schedule tracking, and elite trainer bookings.">
    <link rel="stylesheet" href="landing.css">
</head>
<body>

    <!-- Glow Blobs -->
    <div class="glow-blob glow-1"></div>
    <div class="glow-blob glow-2"></div>
    <div class="glow-blob glow-3"></div>

    <!-- Header & Navigation -->
    <header class="header">
        <div class="container navbar">
            <a href="#" class="logo">
                <span class="logo-dot"></span>FitFlow <span style="font-weight: 300;">AI</span>
            </a>
            <ul class="nav-menu">
                <li><a href="#" class="nav-link active">Home</a></li>
                <li><a href="#schedule" class="nav-link">Schedules</a></li>
                <li><a href="#matcher" class="nav-link">AI Goal Matcher</a></li>
                <li><a href="#trainers" class="nav-link">Our Trainers</a></li>
            </ul>
            <div class="nav-actions">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= $dashboardLink ?>" class="btn-accent">Go to Dashboard</a>
                <?php else: ?>
                    <a href="<?= $loginLink ?>" class="btn-outline">Sign In</a>
                    <a href="<?= $registerLink ?>" class="btn-accent">Join Now</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-content">
                <span class="hero-badge">⚡ AI-Powered Fitness Recommendation</span>
                <h1 class="hero-title">Precision Fitness.<br><span>Guided by Experts.</span></h1>
                <p class="hero-description">
                    Supercharge your training. FitFlow matches your biological goals with the perfect gym classes in seconds. Browse, book, and train with world-class instructors.
                </p>
                <div class="hero-buttons">
                    <a href="#schedule" class="btn-primary-glow">Explore Live Classes</a>
                    <a href="#matcher" class="btn-outline">Try AI Matcher</a>
                </div>
            </div>
            
            <!-- Interactive AI Matcher Panel (Hero Right) -->
            <div class="glass-card ai-matcher" id="matcher">
                <div class="matcher-header">
                    <h3>🎯 Smart Class Recommender</h3>
                    <p>Select your goal and intensity level to find your perfect session.</p>
                </div>
                <div class="matcher-form">
                    <div class="form-group">
                        <label>Select Fitness Goal</label>
                        <div class="option-grid" id="goal-options">
                            <div class="option-pill active" data-value="flexibility">Flexibility & Core</div>
                            <div class="option-pill" data-value="strength">Strength & Power</div>
                            <div class="option-pill" data-value="cardio">Cardio Endurance</div>
                            <div class="option-pill" data-value="weight_loss">Calorie Burn</div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Your Experience Level</label>
                        <div class="option-grid" id="level-options">
                            <div class="option-pill active" data-value="beginner">Beginner</div>
                            <div class="option-pill" data-value="intermediate">Intermediate</div>
                            <div class="option-pill" data-value="advanced">Advanced</div>
                        </div>
                    </div>

                    <!-- Recommended Class Results Area -->
                    <div class="matcher-result active" id="matcher-result-box">
                        <div class="result-tag">Recommended Class</div>
                        <div class="result-class-name" id="res-name">Yoga Flow</div>
                        <div class="result-meta">
                            <span>👤 Instructor: <strong id="res-instructor">Emma Watson</strong></span>
                            <span>📅 Schedule: <strong id="res-schedule">Mon/Wed 8am</strong></span>
                            <span>⚡ Suitability: <strong id="res-suitability">All Levels</strong></span>
                        </div>
                        <a href="<?= $isLoggedIn ? 'pages/book.php' : 'login.php' ?>" class="btn-accent" style="width:100%; justify-content:center; padding:10px 0;">Book Recommended Class</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="stats">
        <div class="container stats-grid">
            <div class="stat-item">
                <div class="stat-num">15<span>+</span></div>
                <div class="stat-label">Active Classes Weekly</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">350<span>+</span></div>
                <div class="stat-label">Happy Members</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">99.8<span>%</span></div>
                <div class="stat-label">Class Attendance</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">5<span>★</span></div>
                <div class="stat-label">Expert Trainer Rating</div>
            </div>
        </div>
    </section>

    <!-- Dynamic Schedules Section -->
    <section class="classes" id="schedule">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Weekly Schedule</span>
                <h2 class="section-title">Explore Active Gym Sessions</h2>
                <p class="section-subtitle">Real-time availability and dynamic capacity meters. Click book to claim your slot instantly.</p>
            </div>
            
            <div class="classes-grid">
                <?php foreach ($classes as $class): 
                    $booked = (int)($class['booked_count'] ?? 0);
                    $capacity = (int)($class['capacity'] ?? 20);
                    $percentage = $capacity > 0 ? min(100, round(($booked / $capacity) * 100)) : 0;
                    $remaining = max(0, $capacity - $booked);
                    
                    // Assign a nice icon based on class type
                    $icon = "🏋️";
                    $class_lower = strtolower($class['class_name']);
                    if (strpos($class_lower, 'yoga') !== false) {
                        $icon = "🧘";
                    } elseif (strpos($class_lower, 'spin') !== false || strpos($class_lower, 'cycle') !== false) {
                        $icon = "🚴";
                    } elseif (strpos($class_lower, 'hiit') !== false || strpos($class_lower, 'blast') !== false) {
                        $icon = "⚡";
                    }
                ?>
                    <div class="glass-card class-card">
                        <div>
                            <div class="class-card-header">
                                <div class="class-icon"><?= $icon ?></div>
                                <div class="class-status-badge">
                                    <?= $remaining > 0 ? "$remaining slots left" : "Fully Booked" ?>
                                </div>
                            </div>
                            <h3><?= htmlspecialchars($class['class_name']) ?></h3>
                            <div class="class-instructor">
                                <span class="instructor-avatar"><?= strtoupper(substr($class['instructor'], 0, 1)) ?></span>
                                <span>with <?= htmlspecialchars($class['instructor']) ?></span>
                            </div>
                            <div class="class-schedule">
                                📅 <?= htmlspecialchars($class['schedule']) ?>
                            </div>
                        </div>
                        <div>
                            <div class="capacity-container">
                                <div class="capacity-meta">
                                    <span>Class Fill Level</span>
                                    <span><?= $percentage ?>% (<?= $booked ?>/<?= $capacity ?>)</span>
                                </div>
                                <div class="capacity-bar">
                                    <div class="capacity-progress" style="width: <?= $percentage ?>%;"></div>
                                </div>
                            </div>
                            <?php if ($isLoggedIn): ?>
                                <a href="pages/book.php" class="btn-accent btn-book-class">Book Seat</a>
                            <?php else: ?>
                                <a href="login.php" class="btn-primary-glow btn-book-class">Claim Slot</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Elite Trainers Section -->
    <section class="trainers" id="trainers">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Elite Mentors</span>
                <h2 class="section-title">Train With The Best</h2>
                <p class="section-subtitle">Our certified trainers build high-impact classes structured around goal metrics and form precision.</p>
            </div>
            
            <div class="trainers-grid">
                <?php foreach ($trainers as $trainer): 
                    $initials = "";
                    $names = explode(" ", $trainer['name']);
                    foreach ($names as $n) {
                        $initials .= strtoupper(substr($n, 0, 1));
                    }
                    if (strlen($initials) > 2) {
                        $initials = substr($initials, 0, 2);
                    }
                ?>
                    <div class="glass-card trainer-card">
                        <div class="trainer-photo-container">
                            <div class="trainer-photo"><?= $initials ?></div>
                        </div>
                        <div class="trainer-specialty"><?= htmlspecialchars($trainer['specialty']) ?></div>
                        <h3><?= htmlspecialchars($trainer['name']) ?></h3>
                        <p class="trainer-bio"><?= htmlspecialchars($trainer['bio']) ?></p>
                        <div class="trainer-socials">
                            <a href="#" class="social-icon" aria-label="Instagram">📸</a>
                            <a href="#" class="social-icon" aria-label="LinkedIn">💼</a>
                            <a href="#" class="social-icon" aria-label="Twitter">🐦</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-box">
                <div class="glow-blob"></div>
                <h2 class="cta-title">Ready to Transform Your Fitness Routine?</h2>
                <p class="cta-description">
                    Take control of your schedule, reserve your class seats ahead of time, and get instant trainer approvals. Sign up today and get access to all trainer sessions.
                </p>
                <div class="cta-buttons">
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= $dashboardLink ?>" class="btn-accent">Go to Dashboard</a>
                    <?php else: ?>
                        <a href="<?= $registerLink ?>" class="btn-accent">Get Started Now</a>
                        <a href="<?= $loginLink ?>" class="btn-outline">Member Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="logo" style="margin-bottom: 12px;">
                        <span class="logo-dot"></span>FitFlow <span style="font-weight: 300;">AI</span>
                    </a>
                    <p>Intelligent scheduling and training alignment for modern gym members, trainers, and administrators.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#" class="footer-link">Home</a></li>
                        <li><a href="#schedule" class="footer-link">Schedules</a></li>
                        <li><a href="#matcher" class="footer-link">AI Goal Matcher</a></li>
                        <li><a href="#trainers" class="footer-link">Our Trainers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Portal Access</h4>
                    <ul class="footer-links">
                        <li><a href="login.php" class="footer-link">User Login</a></li>
                        <li><a href="register.php" class="footer-link">Register Account</a></li>
                        <li><a href="login.php" class="footer-link">Trainer Panel</a></li>
                        <li><a href="login.php" class="footer-link">Admin Portal</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact & Support</h4>
                    <ul class="footer-links">
                        <li><a href="#" class="footer-link">support@fitflow.ai</a></li>
                        <li><a href="#" class="footer-link">1-800-FIT-FLOW</a></li>
                        <li><a href="#" class="footer-link">Privacy Policy</a></li>
                        <li><a href="#" class="footer-link">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> FitFlow AI. All rights reserved. Built with PHP, PDO & HSL Design System.</p>
                <p>Designed for excellence.</p>
            </div>
        </div>
    </footer>

    <!-- Recommendation Engine JS Logic -->
    <script>
        // Export classes from PHP so the recommendation engine has access to actual dynamic database classes!
        const classes = <?= json_encode($classes) ?>;
        
        const goalPills = document.querySelectorAll('#goal-options .option-pill');
        const levelPills = document.querySelectorAll('#level-options .option-pill');
        
        let selectedGoal = 'flexibility';
        let selectedLevel = 'beginner';
        
        // Handle Goal Click
        goalPills.forEach(pill => {
            pill.addEventListener('click', () => {
                goalPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                selectedGoal = pill.getAttribute('data-value');
                runRecommendation();
            });
        });
        
        // Handle Level Click
        levelPills.forEach(pill => {
            pill.addEventListener('click', () => {
                levelPills.forEach(p => p.classList.remove('active'));
                pill.classList.add('active');
                selectedLevel = pill.getAttribute('data-value');
                runRecommendation();
            });
        });
        
        function runRecommendation() {
            const resultBox = document.getElementById('matcher-result-box');
            const resName = document.getElementById('res-name');
            const resInstructor = document.getElementById('res-instructor');
            const resSchedule = document.getElementById('res-schedule');
            const resSuitability = document.getElementById('res-suitability');
            
            // Recommender Logic
            let matchedClass = null;
            
            // 1. Try to find class by goal keyword matching
            if (selectedGoal === 'flexibility') {
                matchedClass = classes.find(c => {
                    const name = c.class_name.toLowerCase();
                    return name.includes('yoga') || name.includes('stretch') || name.includes('flex');
                });
            } else if (selectedGoal === 'strength') {
                matchedClass = classes.find(c => {
                    const name = c.class_name.toLowerCase();
                    return name.includes('hiit') || name.includes('blast') || name.includes('strength') || name.includes('pump') || name.includes('body');
                });
            } else if (selectedGoal === 'cardio') {
                matchedClass = classes.find(c => {
                    const name = c.class_name.toLowerCase();
                    return name.includes('spin') || name.includes('cycle') || name.includes('cardio') || name.includes('ride') || name.includes('run');
                });
            } else if (selectedGoal === 'weight_loss') {
                matchedClass = classes.find(c => {
                    const name = c.class_name.toLowerCase();
                    return name.includes('hiit') || name.includes('blast') || name.includes('spin') || name.includes('cycle');
                });
            }
            
            // 2. Fallback to first available class if no keyword match
            if (!matchedClass && classes.length > 0) {
                matchedClass = classes[0];
            }
            
            if (matchedClass) {
                // Determine suitability label
                let suitability = "All Levels";
                if (selectedLevel === 'beginner') {
                    suitability = "Perfect for Beginners";
                } else if (selectedLevel === 'intermediate') {
                    suitability = "Moderate Intensity";
                } else if (selectedLevel === 'advanced') {
                    suitability = "High Intensity Challenge";
                }
                
                // Show result box with animation
                resultBox.classList.remove('active');
                // trigger layout reflow to restart animation
                void resultBox.offsetWidth;
                
                resName.textContent = matchedClass.class_name;
                resInstructor.textContent = matchedClass.instructor;
                resSchedule.textContent = matchedClass.schedule;
                resSuitability.textContent = suitability;
                
                resultBox.classList.add('active');
            } else {
                resultBox.classList.remove('active');
            }
        }
        
        // Initial run on page load
        runRecommendation();
    </script>
</body>
</html>
