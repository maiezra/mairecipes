<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h2 class="section-title">About ChefMate</h2>
    <div class="about-section">
        <div class="about-card">
            <div class="icon-wrapper">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h3>Our Vision</h3>
            <p>At ChefMate, we envision a world where culinary creativity is accessible to everyone. We strive to foster connections and build a global community around food, making cooking an enjoyable and personalized experience for all.</p>
        </div>
        
        <div class="about-card">
            <div class="icon-wrapper">
                <i class="fas fa-bullseye"></i>
            </div>
            <h3>Our Mission</h3>
            <p>Our mission is to empower home cooks and food enthusiasts by providing a platform that offers tailored recipes, inspired by individual preferences and dietary needs. We aim to simplify the cooking process and inspire creativity in the kitchen.</p>
        </div>
        
        <div class="about-card">
            <div class="icon-wrapper">
                <i class="fas fa-trophy"></i>
            </div>
            <h3>Our Goals</h3>
            <ul>
                <li><strong>Personalization:</strong> Offer a unique cooking experience by providing recipes that match users' dietary preferences, restrictions, and favorite cuisines.</li>
                <li><strong>Community:</strong> Build a supportive and interactive community where users can share their culinary creations and inspire others.</li>
                <li><strong>Innovation:</strong> Continuously improve our platform by integrating new technologies and features to enhance the user experience.</li>
            </ul>
        </div>
        
        <div class="about-card">
            <div class="icon-wrapper">
                <i class="fas fa-users"></i>
            </div>
            <h3>Our Team</h3>
            <p>ChefMate is developed by a dedicated team of food lovers, tech enthusiasts, and design experts who are passionate about making cooking a delightful experience for everyone. We are committed to continuously improving our platform to better serve our users' needs.</p>
        </div>
        
        <div class="about-card">
            <div class="icon-wrapper">
                <i class="fas fa-envelope"></i>
            </div>
            <h3>Contact Us</h3>
            <p>We love hearing from our users! If you have any questions, suggestions, or feedback, please don't hesitate to reach out to us at <a href="mailto:Feedback@chefmate.com">Feedback@chefmate.com</a>.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');

body {
    margin-top: 9rem !important;
    font-family: 'Arial', sans-serif;
}

#mainNav a {
    color: #fff !important;
}

#mainNav {
    background-color: #375755 !important;
}

.container {
    max-width: 1200px;
    margin: auto;
    padding: 0 15px;
}

.section-title {
    font-size: 2.5rem;
    text-align: center;
    margin-bottom: 40px;
    color: #333;
}

.about-section {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
}

.about-card {
    background-color: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 40px 20px 20px; /* added padding top to create space for icon */
    flex: 1 1 calc(33.333% - 20px);
    max-width: calc(33.333% - 20px);
    box-sizing: border-box;
    transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    text-align: center; /* Center text alignment */
}

.about-card:hover {
    transform: translateY(-10px);
    background-color: #f9f9f9;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.icon-wrapper {
    position: absolute;
    top: -40px; /* moved icon to center top */
    left: 50%;
    transform: translateX(-50%);
    background-color: #ff6b6b;
    color: #fff;
    border-radius: 50%;
    padding: 20px; /* Increased padding to make the circle larger */
    font-size: 2rem; /* Increased font size */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.5s ease, background-color 0.5s ease; /* Made transition duration longer */
}

.about-card:hover .icon-wrapper {
    transform: translateX(-50%) rotate(360deg);
    background-color: #375755;
}

.about-card h3 {
    font-size: 1.5rem;
    margin-top: 20px;
    color: #375755;
}

.about-card p, .about-card ul {
    font-size: 1rem;
    line-height: 1.5;
    color: #555;
    margin-top: 15px; /* Added margin top for spacing */
}

.about-card ul {
    padding-left: 20px;
}

.about-card li {
    margin-bottom: 10px;
}

.about-card a {
    color: #375755;
    text-decoration: none;
    transition: color 0.2s;
}

.about-card a:hover {
    color: #ff6b6b;
}

@media (max-width: 992px) {
    .about-card {
        flex: 1 1 calc(50% - 20px);
        max-width: calc(50% - 20px);
    }
}

@media (max-width: 768px) {
    .about-card {
        flex: 1 1 100%;
        max-width: 100%;
    }
}
</style>
