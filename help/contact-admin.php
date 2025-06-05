<!-- help/contact-admin.php -->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $message = htmlspecialchars($_POST["message"]);

    // You can store this message in a database or send via email (optional enhancement)
    $feedback = "Thank you, $name. Your message has been sent!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Admin - Student Market Hub</title>
    <link rel="stylesheet" href="../css/help.css">
</head>
<body>


<div class="container">
    <h2>Contact Admin</h2>
    <?php if (!empty($feedback)) echo "<p style='color: green;'>$feedback</p>"; ?>
    <form method="POST" action="">
        <label for="name">Your Name:</label>
        <input type="text" name="name" required>

        <label for="email">Your Email:</label>
        <input type="email" name="email" required>

        <label for="message">Message:</label>
        <textarea name="message" rows="5" required></textarea>

        <button type="submit">Send Message</button>
    </form>
</div>


</body>
</html>
