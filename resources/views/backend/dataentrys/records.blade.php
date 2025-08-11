<!Doctype html>
<html>
    <head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">

    @include('backend.script.session')

    @include('backend.script.alert')
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        /* Search and Actions Section */
        .search-actions-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .search-group {
            display: flex;
            align-items: center;
            flex-grow: 1;
        }

        .search-bar {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            flex-grow: 1;
            margin-left: 10px;
        }

        /* Tab Styles */
        .tab-container {
            display: flex;
            border-bottom: 2px solid #ddd;
            padding-top: 20px;
        }

        .tab {
            padding: 12px 15px;
            cursor: pointer;
            background: #036b0c;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            color: white;
            font-weight: bold;
            border: 1px solid #ddd;
            border-bottom: none;
        }

        .tab:hover {
            background-color: white;
            color: green;
        }

        .tab.active {
            color: green;
            background: #fff;
            border-color: #ccc;
            border-bottom: 2px solid #fff;
        }

        .section {
            display: none;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section.active {
            display: block;
        }

        /* Student Details Styles */
        .info-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .field-group {
            display: flex;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .field-group:nth-child(4n+1),
        .field-group:nth-child(4n+2) {
            background-color: #f5f5f5;
        }

        .field-group:nth-child(4n+3),
        .field-group:nth-child(4n+4) {
            background-color: #e8f5e9;
        }

        .field-group:hover {
            background-color: #90EE90;
            cursor: pointer;
        }

        .field-label {
            font-weight: bold;
            min-width: 150px;
            padding-right: 10px;
        }

        .field-value {
            flex: 1;
        }

        /* Action Button Styles */
        .action-btn {
            padding: 10px 16px;
            color: #fff;
            background-color: #28a745;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .action-btn:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }

        /* Comment Section Styles */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .popup-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>

    </head>

    <body>


    <div class="container">
        <!-- Tab Navigation -->
        <div class="tab-container">
            <div class="tab active" data-tab="tab1">Student Details</div>
            <div class="tab" data-tab="tab2">Comment</div>
            <div class="tab" data-tab="tab3">
                <a href="javascript:void(0);" id="addCommentBtn" class="text-decoration-none hover-effect" style="color: white;">
                    <i class="fa-solid fa-circle-plus"></i>&nbsp;&nbsp;Add Comment
                </a>
            </div>
        </div>

        <!-- Student Details Section -->
        <div id="tab1" class="section active">
            <h1>Student Details</h1>
            <div class="info-group">
                <div class="field-group">
                    <span class="field-label">University:</span>
                    <span class="field-value">{{ $data_entrie->newUniversity ?? 'N/A' }}</span>
                </div>
                <div class="field-group">
                    <span class="field-label">Location:</span>
                    <span class="field-value">{{ $data_entrie->newLocation ?? 'N/A' }}</span>
                </div>
                <div class="field-group">
                    <span class="field-label">Course:</span>
                    <span class="field-value">{{ $data_entrie->newCourse ?? 'N/A' }}</span>
                </div>
                <div class="field-group">
                    <span class="field-label">Intake:</span>
                    <span class="field-value">{{ $data_entrie->newIntake ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Comment Section -->
        <div id="tab2" class="section">
            <!-- Add your comment section content here -->
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs and sections
                document.querySelectorAll('.tab, .section').forEach(element => {
                    element.classList.remove('active');
                });
                
                // Add active class to clicked tab and corresponding section
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Add Comment button functionality
        document.getElementById('addCommentBtn').addEventListener('click', function() {
            // Add your comment functionality here
        });
    </script>
</body>
</html>