<section class="support-content">
    <div class="support-container">
        <!-- Hero Section -->
        <div class="support-hero">
            <h1>How can we help you today?</h1>
            <p>Find answers, learn more about our platform, or get in touch with our team.</p>
        </div>

        <!-- Quick Navigation -->
        <div class="support-nav-cards">
            <a href="#help-center" class="nav-card">
                <i class="fas fa-book-open"></i>
                <h3>Help Center</h3>
                <p>Guides and documentation</p>
            </a>
            <a href="#faqs" class="nav-card">
                <i class="fas fa-question-circle"></i>
                <h3>FAQs</h3>
                <p>Common questions answered</p>
            </a>
            <a href="#feedback" class="nav-card">
                <i class="fas fa-comment-dots"></i>
                <h3>Feedback</h3>
                <p>Share your thoughts with us</p>
            </a>
        </div>

        <!-- Help Center Section -->
        <div id="help-center" class="support-section">
            <div class="section-header">
                <div class="icon-wrap"><i class="fas fa-book-open"></i></div>
                <h2>Help Center</h2>
            </div>
            <div class="help-grid">
                <div class="help-item">
                    <h3>Getting Started</h3>
                    <ul>
                        <li><a href="#">Creating an account</a></li>
                        <li><a href="#">Student vs public profiles</a></li>
                        <li><a href="#">Navigating the dashboard</a></li>
                    </ul>
                </div>
                <div class="help-item">
                    <h3>Facility Bookings</h3>
                    <ul>
                        <li><a href="#">How to reserve a facility</a></li>
                        <li><a href="#">Understanding booking slots</a></li>
                        <li><a href="#">Payment and confirmation</a></li>
                    </ul>
                </div>
                <div class="help-item">
                    <h3>Sports & Equipment</h3>
                    <ul>
                        <li><a href="#">Enrolling in a sport</a></li>
                        <li><a href="#">Borrowing sports equipment</a></li>
                        <li><a href="#">Practice session schedules</a></li>
                    </ul>
                </div>
                <div class="help-item">
                    <h3>Account Security</h3>
                    <ul>
                        <li><a href="#">Changing your password</a></li>
                        <li><a href="#">Updating profile information</a></li>
                        <li><a href="#">Privacy settings</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- FAQs Section -->
        <div id="faqs" class="support-section">
            <div class="section-header">
                <div class="icon-wrap"><i class="fas fa-question-circle"></i></div>
                <h2>Frequently Asked Questions</h2>
            </div>
            <div class="faq-accordion">
                <div class="faq-card">
                    <div class="faq-question">
                        <span>How do I pay for a facility booking?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>After selecting your facility and slot, you will be redirected to the payment page. You can
                            pay securely using the PayHere gateway with credit/debit cards or mobile wallets. Once
                            payment is successful, your booking is automatically confirmed.</p>
                    </div>
                </div>
                <div class="faq-card">
                    <div class="faq-question">
                        <span>Can I cancel a reservation?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, you can cancel your reservation through your Student Portal. Note that cancellations
                            must be made at least 24 hours before the scheduled time for a potential refund or
                            rescheduling.</p>
                    </div>
                </div>
                <div class="faq-card">
                    <div class="faq-question">
                        <span>How do I join a university sports team?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Go to the Student Portal, navigate to the "Sports" section, and browse available teams. Click
                            'Enroll' to join. Some teams might require physical trials; keep an eye on the "News &
                            Events" section for trial dates.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Section -->
        <div id="feedback" class="support-section">
            <div class="section-header">
                <div class="icon-wrap"><i class="fas fa-comment-dots"></i></div>
                <h2>Give Us Your Feedback</h2>
            </div>
            <div class="feedback-container">
                <div class="feedback-info">
                    <h3>We value your opinion</h3>
                    <p>Your feedback helps us improve the UOC Sports E-Portal. Whether it's a suggestion, a bug report,
                        or a compliment, we'd love to hear from you!</p>
                    <div class="contact-mini">
                        <p><i class="fas fa-envelope"></i> sports@uoc.lk</p>
                        <p><i class="fas fa-phone"></i> +94 11 250 1234</p>
                    </div>
                </div>
                <form action="/uoc-sports/public/submit-feedback" method="POST" class="feedback-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" id="name" name="name" placeholder="John Doe" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="john@example.com" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feedback_type">Feedback Type</label>
                        <select id="feedback_type" name="feedback_type">
                            <option value="Suggestion">Suggestion</option>
                            <option value="Bug Report">Bug Report</option>
                            <option value="General">General Feedback</option>
                            <option value="Compliment">Compliment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Tell us what's on your mind..."
                            required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">
                        <span>Submit Feedback</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
    .support-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .support-hero {
        text-align: center;
        margin-bottom: 60px;
    }

    .support-hero h1 {
        font-size: 3rem;
        color: #5e2d91;
        margin-bottom: 15px;
        font-weight: 800;
    }

    .support-hero p {
        font-size: 1.2rem;
        color: #666;
    }

    .support-nav-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 80px;
    }

    .nav-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .nav-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(94, 45, 145, 0.1);
        border-color: #5e2d91;
    }

    .nav-card i {
        font-size: 3rem;
        color: #5e2d91;
        margin-bottom: 20px;
    }

    .nav-card h3 {
        color: #333;
        margin-bottom: 10px;
        font-size: 1.4rem;
    }

    .nav-card p {
        color: #777;
    }

    .support-section {
        background: white;
        border-radius: 30px;
        padding: 60px;
        margin-bottom: 60px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.03);
        scroll-margin-top: 100px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 40px;
    }

    .icon-wrap {
        width: 60px;
        height: 60px;
        background: rgba(94, 45, 145, 0.1);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5e2d91;
        font-size: 1.5rem;
    }

    .section-header h2 {
        font-size: 2rem;
        color: #333;
    }

    /* Help Grid */
    .help-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 40px;
    }

    .help-item h3 {
        font-size: 1.3rem;
        color: #5e2d91;
        margin-bottom: 20px;
        border-bottom: 2px solid rgba(94, 45, 145, 0.1);
        padding-bottom: 10px;
    }

    .help-item ul {
        list-style: none;
        padding: 0;
    }

    .help-item ul li {
        margin-bottom: 12px;
    }

    .help-item ul li a {
        color: #555;
        text-decoration: none;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .help-item ul li a:before {
        content: "\f105";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #bfa6d9;
    }

    .help-item ul li a:hover {
        color: #5e2d91;
    }

    /* FAQ */
    .faq-accordion {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .faq-card {
        border: 1px solid #eee;
        border-radius: 15px;
        overflow: hidden;
    }

    .faq-question {
        padding: 20px 25px;
        background: #fcfaff;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #333;
        transition: background 0.3s;
    }

    .faq-question:hover {
        background: #f4f0f9;
    }

    .faq-answer {
        padding: 20px 25px;
        color: #666;
        line-height: 1.6;
        border-top: 1px solid #eee;
        display: none;
    }

    .faq-card.active .faq-answer {
        display: block;
    }

    .faq-card.active .faq-question i {
        transform: rotate(180deg);
    }

    .faq-question i {
        transition: transform 0.3s;
        color: #5e2d91;
    }

    /* Feedback */
    .feedback-container {
        display: grid;
        grid-template-columns: 1fr 1.5fr;
        gap: 60px;
    }

    .feedback-info h3 {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: #333;
    }

    .feedback-info p {
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .contact-mini p {
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 500;
        color: #5e2d91;
    }

    .feedback-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-weight: 600;
        color: #555;
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 12px 15px;
        border: 2px solid #eee;
        border-radius: 10px;
        font-family: inherit;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #5e2d91;
        outline: none;
    }

    .btn-submit {
        background: linear-gradient(135deg, #5e2d91 0%, #7b3fa0 100%);
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(94, 45, 145, 0.4);
    }

    @media (max-width: 992px) {
        .support-nav-cards {
            grid-template-columns: 1fr;
        }

        .help-grid {
            grid-template-columns: 1fr;
        }

        .feedback-container {
            grid-template-columns: 1fr;
        }

        .support-hero h1 {
            font-size: 2.2rem;
        }
    }
</style>

<script>
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const card = question.parentElement;
            card.classList.toggle('active');
        });
    });
</script>