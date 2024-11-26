<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Portfolio</title>
    <style>
        /* Reset default styles */
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

        /* Form Container */
        .form-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            max-width: 450px;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .form-container:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        /* Title */
        .form-container h2 {
            text-align: center;
            color: #5C258D;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        /* Labels */
        label {
            font-weight: 600;
            color: #5C258D;
            display: block;
            margin-bottom: 6px;
        }

        /* Input and Textarea Styles */
        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus {
            border-color: #5C258D;
            outline: none;
        }

        /* File Input */
        input[type="file"] {
            padding: 5px;
        }

        /* Button */
        button {
            background: linear-gradient(135deg, #5C258D, #4389A2);
            color: #ffffff;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            background: linear-gradient(135deg, #4389A2, #5C258D);
            transform: scale(1.05);
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Create Your Portfolio</h2>
    <form action="submit_portfolio.php" method="POST" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Bio:</label>
        <textarea name="bio" rows="3" placeholder="Write a short bio about yourself"></textarea>

        <label>Skills:</label>
        <input type="text" name="skills" placeholder="e.g., Web Development, Data Analysis" required>

        <label>Interests:</label>
        <input type="text" name="interests" placeholder="e.g., Reading, Travelling" required>

        <label>Profile Picture:</label>
        <input type="file" name="profile_pic">

        <label>LinkedIn URL:</label>
        <input type="url" name="linkedin" placeholder="https://www.linkedin.com/in/yourprofile">

        <label>GitHub URL:</label>
        <input type="url" name="github" placeholder="https://github.com/yourprofile">

        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>
