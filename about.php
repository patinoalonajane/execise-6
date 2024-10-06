<?php
$inquiryFile = 'inquiries.txt';
$feedbackFile = 'feedback.txt';

function sendJsonResponse($status, $message) {
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitInquiry'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $contact = htmlspecialchars(trim($_POST['contact']));

    if (empty($name) || empty($address) || empty($contact)) {
        sendJsonResponse('error', 'All fields are required in the inquiry form.');
    }

    $inquiryData = "Name: $name, Address: $address, Contact: $contact\n";
    file_put_contents($inquiryFile, $inquiryData, FILE_APPEND);
    sendJsonResponse('success', "Thank you, $name! We have received your inquiry.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitFeedback'])) {
    $feedback = htmlspecialchars(trim($_POST['feedback']));

    if (empty($feedback)) {
        sendJsonResponse('error', 'Feedback cannot be empty.');
    }

    $feedbackEntry = "Feedback: $feedback, Submitted on: " . date('Y-m-d H:i:s') . "\n";
    file_put_contents($feedbackFile, $feedbackEntry, FILE_APPEND);
    sendJsonResponse('success', 'Feedback submitted successfully!');
}

$inquiries = [];
$feedbackEntries = [];

if (isset($_GET['view']) && $_GET['view'] == 'inquiries') {
    if (file_exists($inquiryFile)) {
        $inquiries = file($inquiryFile, FILE_IGNORE_NEW_LINES);
    }
} elseif (isset($_GET['view']) && $_GET['view'] == 'feedback') {
    if (file_exists($feedbackFile)) {
        $feedbackEntries = file($feedbackFile, FILE_IGNORE_NEW_LINES);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>About Us</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="aboutstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body background="Bg.jpg">

    <div class="navbar">
        <h1>Group Nine</h1>
        <div>
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
        </div>
    </div>

    <div class="container">
        <h1 style="text-align: center;">About Us</h1>

        <div class="section">
            <div class="description">
                <h2>Our Mission</h2>
                <p>
                    Our mission is to empower individuals through innovative solutions and exceptional service. We are dedicated to fostering growth 
                    and success in our community by providing quality products, inspiring creativity, and maintaining a commitment to excellence. 
                    We believe in building lasting relationships with our clients and partners, ensuring their needs are met with integrity and professionalism.
                </p>
            </div>
            <img src="Aboutimg.png" alt="Mission Image">
        </div>

        <div class="section">
            <img src="Aboutimg.png" alt="Vision Image">
            <div class="description">
                <h2>Our Vision</h2>
                <p>
                    Our vision is to be a leading force in our industry, recognized for our commitment to innovation and sustainability.
                    We aspire to create a world where individuals are empowered to achieve their dreams and contribute positively to society.
                    Through our dedication to excellence and collaboration, we aim to inspire change and foster a brighter future for all.
                </p>
            </div>
        </div>

        <div class="services-section">
            <h2 style="text-align: center;">Our Services</h2>
            <div class="services-container">
                <div class="service">
                    <h3>Web Design</h3>
                    <p>Creating stunning and responsive websites tailored to your needs.</p>
                </div>
                <div class="service">
                    <h3>Computer Related Services</h3>
                    <p>Offering expert technical support and maintenance for all your devices.</p>
                </div>
                <div class="service">
                    <h3>Digital Marketing</h3>
                    <p>Helping you reach your audience effectively through online strategies.</p>
                </div>
                <div class="service">
                    <h3>Graphic Design</h3>
                    <p>Designing visuals that communicate your brand message powerfully.</p>
                </div>
                <div class="service">
                    <h3>Technical Support</h3>
                    <p>Providing assistance and solutions for your technical issues.</p>
                </div>
            </div>
        </div>

        <div class="forms-section">
            <div class="form-wrapper">
                <h2>Submit Inquiry</h2>
                <form id="inquiryForm" method="POST">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>

                    <label for="contact">Contact Number:</label>
                    <input type="tel" id="contact" name="contact" required>

                    <button type="submit" name="submitInquiry">Submit Inquiry</button>
                </form>
            </div>

            <div class="form-wrapper">
                <h2>Submit Feedback</h2>
                <form id="feedbackForm" method="POST">
                    <label for="feedback">Your Feedback:</label>
                    <textarea id="feedback" name="feedback" required></textarea>

                    <button type="submit" name="submitFeedback">Submit Feedback</button>
                </form>
            </div>

            <p id="formMessage" style="display:none;"></p>
        </div>

        <div class="list-section">
            <div class="view-options">
                <a href="about.php?view=inquiries" class="view-button">View Past Inquiries</a>
                <a href="about.php?view=feedback" class="view-button">View Past Feedback</a>
            </div>

            <?php if (!empty($inquiries)): ?>
                <div class="list-wrapper">
                    <h2>Past Inquiries</h2>
                    <ul>
                        <?php foreach ($inquiries as $inquiry): ?>
                            <li><?= htmlspecialchars($inquiry) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($feedbackEntries)): ?>
                <div class="list-wrapper">
                    <h2>Past Feedback</h2>
                    <ul>
                        <?php foreach ($feedbackEntries as $entry): ?>
                            <li><?= htmlspecialchars($entry) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#inquiryForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'about.php',
                    data: $(this).serialize() + '&submitInquiry=true',
                    dataType: 'json',
                    success: function(response) {
                        $('#formMessage').html(response.message).show().css('color', response.status === 'success' ? 'green' : 'red');
                        if (response.status === 'success') {
                            $('#inquiryForm')[0].reset();
                        }
                    }
                });
            });

            $('#feedbackForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: 'about.php',
                    data: $(this).serialize() + '&submitFeedback=true',
                    dataType: 'json',
                    success: function(response) {
                        $('#formMessage').html(response.message).show().css('color', response.status === 'success' ? 'green' : 'red');
                        if (response.status === 'success') {
                            $('#feedbackForm')[0].reset();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>