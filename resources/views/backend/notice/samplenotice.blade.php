<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notice Period Form</title>

    @include('backend.script.session')

    @include('backend.script.alert')

    
    <style>
        body {
            background-color: #f0f0f0;
            font-family: 'Poppins', Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-direction: column;
            height: 100vh;
            padding-top: 30px;
        }
        .notice-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            width: 100%;
            max-width: 700px;
        }
        .container {
            flex: 1 1 calc(33.333% - 20px); /* Adjust this to control the size of the notices in a row */
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            min-width: 280px; /* Ensures the notice doesn't shrink too small */
        }
        h2 {
            font-size: 36px; 
            font-weight: 700; 
            color: #1e7e34; 
            margin-bottom: 20px; 
            text-align: center; 
            letter-spacing: 0.5px; 
            line-height: 1.4; 
            font-family: 'Arial', sans-serif; 
        }
        p {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
        }
        .notice-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .notice-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <h2>Notice Board</h2>

    <div class="notice-container">
        <!-- Loop through notices -->
        @foreach ($notices as $notice)
            <div class="container">
                <div class="notice-box">
                    <p><strong>Title:</strong> {{ $notice->title }}</p>
                    <p><strong>Description:</strong> {{ $notice->description }}</p>
                    <p><strong>Image:</strong></p>
                    <img src="{{ asset('storage/' . $notice->image) }}" class="notice-image" />
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
