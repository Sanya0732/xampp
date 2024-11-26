<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Portfolio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        label {
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
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
