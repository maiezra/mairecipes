<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <h2 class="section-title">About ChefMate</h2>
    <div class="about-section">
        <div class="about-card">
            <h3>Our Vision</h3>
            <p>At ChefMate, we envision a world where culinary creativity is accessible to everyone. We strive to foster connections and build a global community around food, making cooking an enjoyable and personalized experience for all.</p>
        </div>
        
        <div class="about-card">
            <h3>Our Mission</h3>
            <p>Our mission is to empower home cooks and food enthusiasts by providing a platform that offers tailored recipes, inspired by individual preferences and dietary needs. We aim to simplify the cooking process and inspire creativity in the kitchen.</p>
        </div>
        
        <div class="about-card">
            <h3>Our Goals</h3>
            <ul>
                <li><strong>Personalization:</strong> Offer a unique cooking experience by providing recipes that match users' dietary preferences, restrictions, and favorite cuisines.</li>
                <li><strong>Community:</strong> Build a supportive and interactive community where users can share their culinary creations and inspire others.</li>
                <li><strong>Innovation:</strong> Continuously improve our platform by integrating new technologies and features to enhance the user experience.</li>
            </ul>
        </div>
        
        <div class="about-card">
            <h3>Our Team</h3>
            <p>ChefMate is developed by a dedicated team of food lovers, tech enthusiasts, and design experts who are passionate about making cooking a delightful experience for everyone. We are committed to continuously improving our platform to better serve our users' needs.</p>
        </div>
        
        <div class="about-card">
            <h3>Contact Us</h3>
            <p>We love hearing from our users! If you have any questions, suggestions, or feedback, please don't hesitate to reach out to us at <a href="mailto:Feedback@chefmate.com">Feedback@chefmate.com</a>.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<style>
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
    padding: 20px;
    flex: 1 1 calc(33.333% - 20px);
    max-width: calc(33.333% - 20px);
    box-sizing: border-box;
    transition: transform 0.3s ease;
}

.about-card:hover {
    transform: translateY(-10px);
}

.about-card h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #375755;
}

.about-card p, .about-card ul {
    font-size: 1rem;
    line-height: 1.5;
    color: #555;
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
