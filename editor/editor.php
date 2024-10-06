<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CKEditor with AJAX</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.ckeditor.com/4.20.2/standard/ckeditor.js"></script> <!-- CKEditor CDN -->
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #E0F2F1; /* Light teal background */
            color: #004D40; /* Dark teal text */
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #00796B; /* Teal color */
            text-align: center;
        }

        h2 {
            color: #004D40; /* Dark teal color */
            margin-top: 40px;
        }

        #editor {
            width: 100%;
            max-width: 800px; /* Limit the editor width */
            margin: 20px auto;
            padding: 10px;
            border: 2px solid #00796B; /* Teal border */
            border-radius: 8px;
            background-color: #FFFFFF; /* White background for the editor */
        }

        button {
            background-color: #00796B; /* Teal button */
            color: #FFFFFF; /* White text */
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block; /* Center the button */
            margin: 20px auto;
        }

        button:hover {
            background-color: #004D40; /* Darker teal on hover */
        }

        #response {
            margin: 20px 0;
            font-weight: bold;
        }

        #content-area {
            max-width: 800px; /* Limit the content area width */
            margin: 20px auto;
            padding: 10px;
            background-color: #FFFFFF; /* White background for content area */
            border: 1px solid #00796B; /* Teal border */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .content-item {
            border-bottom: 1px solid #B2DFDB; /* Light teal line between items */
            padding: 10px 0;
        }

        .content-item:last-child {
            border-bottom: none; /* Remove border for the last item */
        }
    </style>
</head>
<body>

<h1>Content Editor</h1>
<textarea id="editor" rows="10" cols="50" placeholder="Write your content here..."></textarea><br>
<button id="submit">Submit</button>

<div id="response"></div>

<h2>Submitted Content</h2>
<div id="content-area">
    <!-- The fetched content will be loaded here -->
</div>

<script>
$(document).ready(function() {
    // Initialize CKEditor
    CKEDITOR.replace('editor');

    // Load initial content
    $('#content-area').load('fetch_data.php');

    // Handle submit button click
    $('#submit').click(function() {
        // Get data from CKEditor
        var content = CKEDITOR.instances.editor.getData();

        // Submit the data via AJAX
        $.ajax({
            url: 'submit_content.php',
            type: 'POST',
            data: { content: content },
            success: function(response) {
                $('#response').html(response);  // Show the response message
                CKEDITOR.instances.editor.setData('');  // Clear CKEditor after submission
                // Refresh the content area
                $('#content-area').load('fetch_data.php');
            },
            error: function() {
                $('#response').html('Error occurred while submitting.');
            }
        });
    });
});
</script>

</body>
</html>
