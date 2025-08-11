<html>
<body>
<div class="pagination-container" style="display: flex; justify-content: center; margin-top: 20px;">
        {{ $documents->links() }}
    </div>

    <head>
    <style>
        .pagination-container nav {
            display: flex;
            justify-content: center;
        }
        
        .pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .pagination li {
            margin: 0 5px;
        }
        
        .pagination li a, .pagination li span {
            display: block;
            padding: 8px 12px;
            border: 1px solid #ddd;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .pagination li.active span {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .pagination li a:hover {
            background-color: #f0f0f0;
        }
    </style>
    </head>
    </body>
    </html>