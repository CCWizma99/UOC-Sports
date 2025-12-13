<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UOC Sports E-Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Bebas+Neue&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: #1a1a1a;
            overflow-x: hidden;
            background: #fff;
        }
        
        /* Header */
        header {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.2rem 5%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.3rem;
            color: #1a1a1a;
            text-decoration: none;
        }
        
        .logo img {
            height: 40px;
        }
        
        .nav-links {
            display: flex;
            gap: 2.5rem;
            align-items: center;
        }
        
        .nav-links a {
            color: #4a4a4a;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            position: relative;
        }
        
        .nav-links a:hover {
            color: #1a1a1a;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #5e2d91;
            transition: width 0.3s;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .btn-primary {
            background: #1a1a1a;
            color: white;
            padding: 0.7rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: #5e2d91;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: transparent;
            color: #4a4a4a;
            padding: 0.7rem 1.8rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s;
            border: 1px solid #e0e0e0;
        }
        
        .btn-secondary:hover {
            border-color: #5e2d91;
            color: #5e2d91;
        }
        
        /* Hero Section */
        .hero {
            margin-top: 80px;
            padding: 8rem 5% 6rem;
            background: linear-gradient(135deg, 
                #ff6b9d 0%, 
                #c471ed 20%, 
                #12c2e9 40%, 
                #c471ed 60%, 
                #f093fb 80%, 
                #ffd89b 100%);
            background-size: 200% 200%;
            animation: gradient 15s ease infinite;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(255,255,255,0.1) 0%, transparent 50%);
        }
        
        .hero-content {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: 4.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: #1a1a1a;
            letter-spacing: -2px;
        }
        
        .hero p {
            font-size: 1.3rem;
            color: #2a2a2a;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .input-container {
            background: white;
            padding: 0.8rem;
            border-radius: 12px;
            display: flex;
            gap: 1rem;
            max-width: 600px;
            margin: 0 auto 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .input-container input {
            flex: 1;
            border: none;
            outline: none;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
        }
        
        .input-container input::placeholder {
            color: #999;
        }
        
        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .trusted-by {
            margin-top: 4rem;
            color: #4a4a4a;
            font-size: 0.9rem;
            font-weight: 500;
            overflow: hidden;
        }
        
        .sports-marquee {
            margin-top: 2rem;
            overflow: hidden;
            position: relative;
            height: 60px;
        }
        
        .marquee-content {
            display: flex;
            gap: 3rem;
            animation: marquee 30s linear infinite;
            width: fit-content;
        }
        
        .marquee-content span {
            font-weight: 600;
            font-size: 1.1rem;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .marquee-content span i {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.8);
        }
        
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        /* Feature Section */
        .features {
            padding: 6rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-label {
            color: #5e2d91;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .section-title {
            font-size: 3rem;
            font-weight: 800;
            margin-top: 0.5rem;
            letter-spacing: -1px;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: #fff;
            padding: 0;
            border-radius: 16px;
            transition: all 0.3s;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        
        .feature-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .feature-content {
            padding: 2rem;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .feature-icon i {
            font-size: 1.5rem;
            color: white;
        }
        
        .feature-card h3 {
            font-size: 1.4rem;
            margin-bottom: 0.8rem;
            font-weight: 700;
        }
        
        .feature-card p {
            color: #666;
            line-height: 1.7;
        }
        
        /* Showcase Section */
        .showcase {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            padding: 6rem 5%;
            color: white;
        }
        
        .showcase-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .showcase-text h2 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }
        
        .showcase-text p {
            font-size: 1.1rem;
            line-height: 1.7;
            color: #b0b0b0;
            margin-bottom: 1rem;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .stat-item h3 {
            font-size: 3rem;
            font-weight: 800;
            color: #5e2d91;
            margin-bottom: 0.5rem;
        }
        
        .stat-item p {
            font-size: 1rem;
            color: #888;
        }
        
        .showcase-image {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 400px;
        }
        
        .showcase-image i {
            font-size: 8rem;
            opacity: 0.9;
        }
        
        /* Stories Section */
        .stories {
            padding: 6rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .stories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .story-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        
        .story-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        .story-image {
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .story-image i {
            font-size: 4rem;
            color: rgba(255,255,255,0.9);
        }
        
        .story-tag {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: rgba(255,255,255,0.95);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #5e2d91;
        }
        
        .story-content {
            padding: 1.5rem;
        }
        
        .story-content h4 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
            font-weight: 600;
            line-height: 1.4;
        }
        
        /* Footer */
        footer {
            background: #1a1a1a;
            color: white;
            padding: 4rem 5% 2rem;
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .footer-brand h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .footer-brand p {
            color: #999;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        
        .footer-section h4 {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        
        .footer-section a {
            display: block;
            color: #ccc;
            text-decoration: none;
            margin-bottom: 0.8rem;
            transition: color 0.3s;
            font-size: 0.95rem;
        }
        
        .footer-section a:hover {
            color: #5e2d91;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            background: #2a2a2a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: #5e2d91;
            transform: translateY(-3px);
        }
        
        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .footer-bottom p {
            color: #888;
            font-size: 0.9rem;
        }
        
        .footer-links {
            display: flex;
            gap: 2rem;
        }
        
        .footer-links a {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: #5e2d91;
        }
        
        /* Contact Section */
        .contact {
            padding: 6rem 5%;
            background: #f8f9fa;
        }
        
        .contact-content {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-top: 3rem;
        }
        
        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .contact-form input,
        .contact-form textarea {
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: #5e2d91;
        }
        
        .contact-form textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        
        .contact-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .contact-item i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
        }
        
        .contact-item div h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .contact-item div p {
            color: #666;
            line-height: 1.6;
        }
        
        .contact-item div a {
            color: #5e2d91;
            text-decoration: none;
        }
        
        .contact-item div a:hover {
            text-decoration: underline;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .showcase-content {
                grid-template-columns: 1fr;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .input-container {
                flex-direction: column;
            }
            
            .cta-buttons {
                flex-direction: column;
            }
        }
        /* Sporty Hero Enhancements */
.hero {
    position: relative;
    background: linear-gradient(135deg, #111 0%, #4b0082 100%);
    padding: 10rem 5% 7rem;
}

/* Action silhouettes (overlay) */
.hero::after {
    content: "";
    position: absolute;
    bottom: 0;
    right: 0;
    width: 600px;
    height: 500px;
    background-size: contain;
    background-repeat: no-repeat;
    opacity: 0.25;
    transform: translate(50px, 20px);
    pointer-events: none;
}

/* Speed lines effect */
.speed-lines {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image:
        linear-gradient(120deg, rgba(255,255,255,0.1) 0%, transparent 60%),
        linear-gradient(300deg, rgba(255,255,255,0.08) 0%, transparent 70%);
    background-size: 200% 200%;
    animation: speedMove 6s linear infinite;
    pointer-events: none;
}

@keyframes speedMove {
    0% { background-position: 0% 0%; }
    100% { background-position: 200% 200%; }
}

.hero h1 {
    font-size: 4.2rem;
    color: white;
    font-family: 'Bebas Neue', sans-serif;
    letter-spacing: 1px;
}

.hero p {
    color: #e0e0e0;
    font-size: 1.25rem;
    font-weight: 400;
    max-width: 700px;
    margin: 0 auto 2rem;
}

/* Make search box sporty */
.input-container {
    border: 2px solid rgba(255,255,255,0.15);
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(8px);
}

.input-container input {
    color: white;
}

.input-container input::placeholder {
    color: #ccc;
}

/* Button more athletic */
.btn-primary {
    background: #8a2be2;
    font-weight: 700;
    letter-spacing: 0.5px;
}

    </style>
</head>
<body>
    <header>
        <nav>
            <a href="#" class="logo">
                <i class="fas fa-trophy"></i>
                <span>UOC Sports E-Portal</span>
            </a>
            <div class="nav-links">
                <a href="#">News</a>
                <a href="#">Contact</a>
                <a href="#" class="btn-secondary">Facility Reservation</a>
                <a href="#" class="btn-primary">Sign In</a>
            </div>
        </nav>
    </header>

    <section class="hero">
        <div class="speed-lines">

        </div>
        <div class="hero-content">
            <h1>Elevate Your Athletic Journey</h1>
            <p>Manage sports activities, book facilities, and track your achievements all in one powerful platform at the University of Colombo</p>
            
            <div class="input-container">
                <input type="text" placeholder="Search for sports, facilities, or news...">
                <button class="btn-primary">Explore Now</button>
            </div>
            
            <div class="trusted-by">
                <p>Empowering athletes at Sri Lanka's premier university</p>
                <div class="sports-marquee">
                    <div class="marquee-content">
                        <span><i class="fas fa-cricket"></i> Cricket</span>
                        <span><i class="fas fa-football"></i> Rugby</span>
                        <span><i class="fas fa-volleyball"></i> Netball</span>
                        <span><i class="fas fa-running"></i> Athletics</span>
                        <span><i class="fas fa-hand-fist"></i> Karate</span>
                        <span><i class="fas fa-table-tennis"></i> Table Tennis</span>
                        <span><i class="fas fa-basketball"></i> Basketball</span>
                        <span><i class="fas fa-water"></i> Swimming</span>
                        <span><i class="fas fa-person-swimming"></i> Badminton</span>
                        <span><i class="fas fa-dumbbell"></i> Weightlifting</span>
                        <span><i class="fas fa-chess"></i> Chess</span>
                        <span><i class="fas fa-volleyball"></i> Volleyball</span>
                        <span><i class="fas fa-futbol"></i> Football</span>
                        <span><i class="fas fa-tennis-ball"></i> Tennis</span>
                        <span><i class="fas fa-baseball"></i> Baseball</span>
                        <!-- Duplicate for seamless loop -->
                        <span><i class="fas fa-football"></i> Rugby</span>
                        <span><i class="fas fa-volleyball"></i> Netball</span>
                        <span><i class="fas fa-running"></i> Athletics</span>
                        <span><i class="fas fa-hand-fist"></i> Karate</span>
                        <span><i class="fas fa-table-tennis"></i> Table Tennis</span>
                        <span><i class="fas fa-basketball"></i> Basketball</span>
                        <span><i class="fas fa-water"></i> Swimming</span>
                        <span><i class="fas fa-person-swimming"></i> Badminton</span>
                        <span><i class="fas fa-dumbbell"></i> Weightlifting</span>
                        <span><i class="fas fa-chess"></i> Chess</span>
                        <span><i class="fas fa-volleyball"></i> Volleyball</span>
                        <span><i class="fas fa-futbol"></i> Football</span>
                        <span><i class="fas fa-tennis-ball"></i> Tennis</span>
                        <span><i class="fas fa-baseball"></i> Baseball</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="section-header">
            <div class="section-label">Our Services</div>
            <h2 class="section-title">Everything You Need for Sports Excellence</h2>
        </div>
        
        <div class="feature-grid">
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1459865264687-595d652de67e?w=800&q=80" alt="Facility Booking" class="feature-image">
                <div class="feature-content">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Facility Booking</h3>
                    <p>Reserve sports facilities with ease. Book courts, fields, and training spaces for practice sessions and events.</p>
                </div>
            </div>
            
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=800&q=80" alt="Tournament Management" class="feature-image">
                <div class="feature-content">
                    <div class="feature-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3>Tournament Management</h3>
                    <p>Participate in inter-university championships, national tournaments, and track your competitive achievements.</p>
                </div>
            </div>
            
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=800&q=80" alt="Training Programs" class="feature-image">
                <div class="feature-content">
                    <div class="feature-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3>Training Programs</h3>
                    <p>Access structured training programs with professional coaching for various sports disciplines.</p>
                </div>
            </div>
            
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80" alt="Sports News" class="feature-image">
                <div class="feature-content">
                    <div class="feature-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3>Sports News</h3>
                    <p>Stay updated with the latest achievements, events, and announcements from the UOC sports community.</p>
                </div>
            </div>
            
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=800&q=80" alt="Team Collaboration" class="feature-image">
                <div class="feature-content">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Team Collaboration</h3>
                    <p>Connect with teammates, coordinate practices, and manage team activities efficiently.</p>
                </div>
            </div>
            
            <div class="feature-card">
                <img src="https://images.unsplash.com/photo-1551958219-acbc608c6377?w=800&q=80" alt="Performance Tracking" class="feature-image">
                <div class="feature-content">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Performance Tracking</h3>
                    <p>Monitor your athletic progress, view statistics, and set goals for continuous improvement.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="showcase">
        <div class="showcase-content">
            <div class="showcase-text">
                <h2>Trusted by Champions Across Multiple Disciplines</h2>
                <p>The University of Colombo Sports E-Portal has transformed how students engage with athletics, helping athletes achieve excellence at national and international levels.</p>
                <p>"Thanks to the streamlined facility booking and training management, our athletes save hours each week and focus on what matters most - performance and achievement."</p>
                
                <div class="stats">
                    <div class="stat-item">
                        <h3>15+</h3>
                        <p>Sports Disciplines</p>
                    </div>
                    <div class="stat-item">
                        <h3>500+</h3>
                        <p>Active Athletes</p>
                    </div>
                    <div class="stat-item">
                        <h3>20+</h3>
                        <p>Championships Won</p>
                    </div>
                    <div class="stat-item">
                        <h3>100%</h3>
                        <p>Digital Integration</p>
                    </div>
                </div>
            </div>
            
            <div class="showcase-image">
                
            </div>
        </div>
    </section>

    <section class="stories" id="stories">
        <div class="section-header">
            <div class="section-label">Top Stories</div>
            <h2 class="section-title">Latest Achievements & Updates</h2>
        </div>
        
        <div class="stories-grid">
            <a href="#" class="story-card">
                <div class="story-image" style="background-image: url('https://images.unsplash.com/photo-1587280501635-68a0e82cd5ff?w=800&q=80'); background-size: cover; background-position: center;">
                    <span class="story-tag">Achievement</span>
                </div>
                <div class="story-content">
                    <h4>University of Colombo students won medals in SAG 2019</h4>
                </div>
            </a>
            
            <a href="#" class="story-card">
                <div class="story-image" style="background-image: url('https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?w=800&q=80'); background-size: cover; background-position: center;">
                    <span class="story-tag">Facilities</span>
                </div>
                <div class="story-content">
                    <h4>Opening Ceremony of the Refurbished Pavilion</h4>
                </div>
            </a>
            
            <a href="#" class="story-card">
                <div class="story-image" style="background-image: url('https://images.unsplash.com/photo-1555597673-b21d5c935865?w=800&q=80'); background-size: cover; background-position: center;">
                    <span class="story-tag">Karate</span>
                </div>
                <div class="story-content">
                    <h4>University Karate (Kata) team shines at the National Karate Championship – 2021</h4>
                </div>
            </a>
            
            <a href="#" class="story-card">
                <div class="story-image" style="background-image: url('https://images.unsplash.com/photo-1571902943202-507ec2618e8f?w=800&q=80'); background-size: cover; background-position: center;">
                    <span class="story-tag">Well-being</span>
                </div>
                <div class="story-content">
                    <h4>Change the cycle, Break a sweat!</h4>
                </div>
            </a>
        </div>
    </section>

    <section class="contact" id="contact">
        <div class="contact-content">
            <div class="section-header">
                <div class="section-label">Get In Touch</div>
                <h2 class="section-title">Contact the Physical Education Department</h2>
            </div>
            
            <div class="contact-grid">
                <form class="contact-form">
                    <input type="email" placeholder="Your Email Address" required>
                    <input type="text" placeholder="Subject of Inquiry" required>
                    <textarea placeholder="Your Message" required></textarea>
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h4>Address</h4>
                            <p>Department of Physical Education,<br>
                            University of Colombo,<br>
                            94, Cumaratunga Munidasa Mw,<br>
                            Colombo 03, Sri Lanka</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h4>Email</h4>
                            <p><a href="mailto:info@ped.cmb.ac.lk">info@ped.cmb.ac.lk</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h4>Phone</h4>
                            <p>+94 112 502 405</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-brand">
                    <h3>UOC Sports E-Portal</h3>
                    <p>Empowering athletic excellence and teamwork at the University of Colombo through innovative digital solutions.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Platform</h4>
                    <a href="#">Home</a>
                    <a href="#">News & Events</a>
                    <a href="#">Facility Booking</a>
                    <a href="#">Training Programs</a>
                </div>
                
                <div class="footer-section">
                    <h4>Sports</h4>
                    <a href="#">Cricket</a>
                    <a href="#">Rugby</a>
                    <a href="#">Athletics</a>
                    <a href="#">Karate</a>
                    <a href="#">Netball</a>
                </div>
                
                <div class="footer-section">
                    <h4>Resources</h4>
                    <a href="#">Student Portal</a>
                    <a href="#">Coaching Staff</a>
                    <a href="#">Facilities</a>
                    <a href="#">Championships</a>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <a href="#">Help Center</a>
                    <a href="#">Contact Us</a>
                    <a href="#">FAQs</a>
                    <a href="#">Feedback</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2025 University of Colombo, Sri Lanka. All rights reserved.</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                    <a href="#">