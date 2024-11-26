<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['name']); ?>'s Portfolio</title>
    <style>
        /* Reset and basic styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Background */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #5C258D, #4389A2);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Portfolio Container */
        .portfolio-container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .portfolio-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        /* Profile Picture */
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 4px solid #5C258D;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Heading and Text */
        h1 {
            color: #5C258D;
            font-size: 1.8rem;
            margin: 10px 0;
        }

        p {
            margin: 10px 0;
            line-height: 1.6;
            color: #333;
        }

        /* Strong Text */
        p strong {
            color: #5C258D;
            font-weight: 600;
        }

        /* Portfolio Links */
        .portfolio-links {
            margin-top: 15px;
        }

        .portfolio-links a {
            color: #4389A2;
            font-weight: bold;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .portfolio-links a:hover {
            color: #5C258D;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .portfolio-container {
                padding: 20px;
            }
            .profile-pic {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>

<div class="portfolio-container">
    <?php if ($row['profile_pic']): ?>
        <img src="<?php echo htmlspecialchars($row['profile_pic']); ?>" alt="Profile Picture" class="profile-pic">
    <?php endif; ?>
    
    <h1><?php echo htmlspecialchars($row['name']); ?></h1>
    <p><strong>Bio:</strong> <?php echo htmlspecialchars($row['bio']); ?></p>
    <p><strong>Skills:</strong> <?php echo htmlspecialchars($row['skills']); ?></p>
    <p><strong>Interests:</strong> <?php echo htmlspecialchars($row['interests']); ?></p>
    
    <div class="portfolio-links">
        <?php if ($row['linkedin']): ?>
            <a href="<?php echo htmlspecialchars($row['linkedin']); ?>" target="_blank">LinkedIn</a>
        <?php endif; ?>
        <?php if ($row['github']): ?>
            <a href="<?php echo htmlspecialchars($row['github']); ?>" target="_blank">GitHub</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
