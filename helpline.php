<?php
include 'config.php';
session_start();

// Fetch helpline information
$helpline_query = "SELECT description, phone FROM helpline";
$helpline_result = $conn->query($helpline_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpline Information</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f5f7;
        }

        header {
            background-color: #007BFF;
            padding: 20px;
            color: #fff;
            text-align: center;
            font-size: 24px;
        }

        .content {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
            border-bottom: 2px solid #007BFF;
            display: inline-block;
            padding-bottom: 5px;
        }

        .helpline-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .helpline-card {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
            border: 1px solid #ddd;
        }

        .helpline-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .helpline-card i {
            font-size: 30px;
            color: #007BFF;
            margin-bottom: 10px;
        }

        .helpline-card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .helpline-card p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        footer {
            text-align: center;
            background: #007BFF;
            padding: 15px;
            color: #fff;
            position: relative;
            bottom: 0;
            width: 100%;
            margin-top: 40px;
        }

        footer a {
            color: #ffd700;
            text-decoration: none;
            font-weight: bold;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    

<header>
    Helpline Information
</header>

<div class="content">
    <h2>Available Helplines</h2>
    <div class="helpline-grid">
        <?php
        if ($helpline_result->num_rows > 0) {
            while ($row = $helpline_result->fetch_assoc()) {
                echo '
                <div class="helpline-card">
                    <i class="fas fa-phone-alt"></i>
                    <h3>Contact Us</h3>
                    <p><strong>Description:</strong> ' . htmlspecialchars($row['description']) . '</p>
                    <p><strong>Phone:</strong> ' . htmlspecialchars($row['phone']) . '</p>
                </div>';
            }
        } else {
            echo '<p>No helpline information available.</p>';
        }
        ?>
    </div>
</div>

<footer>
    © 2024 Helpline System | <a href="#">Privacy Policy</a>
</footer>

</body>
</html>
