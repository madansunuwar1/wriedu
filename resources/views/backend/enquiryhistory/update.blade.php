<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment Form</title>

    @include('backend.script.session')

    @include('backend.script.alert')

    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1; /* Light grey background for the page */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        form {
            background-color: #ffffff; /* White background for the form */
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 500px;
            max-width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center; /* Center content horizontally */
            border-radius:50px;
        }

        h1 {
            text-align: center;
            color: #333; /* Dark grey for the heading */
            font-size: 28px;
            margin-bottom: 20px;
            font-family: poppins;
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555; /* Slightly darker grey for labels */
            font-size: 20px;
            font-weight: bold;
            font-family: poppins;
        }

        textarea {
            width: 80%;
            height: 150px;
            padding: 12px;
            border: 1px solid #ccc; /* Light grey border for the textarea */
            border-radius: 6px;
            resize: vertical;
            font-size: 16px;
            background-color: #fafafa; /* Very light grey background for the textarea */
        }

        textarea:focus {
            outline: none;
            border-color: #007bff; /* Blue border when focused */
            background-color: #fff; /* White background on focus */
        }

        button {
            padding: 12px;
            background-color: #28a733; /* Blue button */
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            text-align: center;
            font-family:poppins;
        }

        button:hover {
            background-color: #24a52f; /* Darker blue on hover */
        }
    </style>
</head>
<body>
<form action="{{ route('backend.enquiryhistory.update', $comment_passes->id) }}" method="POST">

    @csrf
    @method('PUT')
        <h1>Comment</h1>
        <label for="comment">Leave a Comment</label>
        <textarea id="comment" name="comment" placeholder="Type your comment here...">{{ old('comment', $comment_passes->comment) }}</textarea>
        <button type="submit"> Update Comment</button>
    </form>
</body>
</html>
