<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=], initial-scale=1.0">
     <title>WRI Education Consultancy</title>
    <link rel="icon" type="image/png" href="{{ asset('img/wri.png') }}">
    <style>
        /* Mobile First Approach */

/* Base styles for mobile devices */
@media screen and (min-width: 320px) {
    .container {
        width: 100%;
        padding: 0 15px;
        box-sizing: border-box;
    }
    
    .content {
        font-size: 16px;
        line-height: 1.4;
    }
    
    .heading {
        font-size: 24px;
        margin-bottom: 15px;
    }
}

/* Small tablets and large phones (landscape) */
@media screen and (min-width: 576px) {
    .container {
        max-width: 540px;
        margin: 0 auto;
    }
    
    .content {
        font-size: 16px;
        line-height: 1.5;
    }
    
    .heading {
        font-size: 28px;
    }
}

/* Tablets and small desktops */
@media screen and (min-width: 768px) {
    .container {
        max-width: 720px;
    }
    
    .content {
        font-size: 16px;
        line-height: 1.6;
    }
    
    .heading {
        font-size: 32px;
    }
    
    .nav-menu {
        display: flex;
        justify-content: space-between;
    }
}

/* Large tablets and desktops */
@media screen and (min-width: 992px) {
    .container {
        max-width: 960px;
    }
    
    .content {
        font-size: 17px;
        line-height: 1.7;
    }
    
    .grid-layout {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
}

/* Extra large desktops */
@media screen and (min-width: 1200px) {
    .container {
        max-width: 1140px;
    }
    
    .content {
        font-size: 18px;
    }
    
    .grid-layout {
        grid-template-columns: repeat(4, 1fr);
    }
}

/* Extra extra large desktops */
@media screen and (min-width: 1400px) {
    .container {
        max-width: 1320px;
    }
    
    .content {
        font-size: 18px;
    }
    
    .grid-layout {
        gap: 40px;
    }
}

/* Device-specific media queries */

/* For iPhone SE and smaller phones */
@media screen and (max-width: 375px) {
    .container {
        padding: 0 10px;
    }
    
    .heading {
        font-size: 22px;
    }
    
    .button {
        width: 100%;
        padding: 12px;
    }
}

/* For tablets in portrait mode */
@media screen and (min-width: 768px) and (max-width: 991px) and (orientation: portrait) {
    .sidebar {
        width: 30%;
        float: left;
    }
    
    .main-content {
        width: 70%;
        float: right;
    }
}

/* For tablets in landscape mode */
@media screen and (min-width: 768px) and (max-width: 1024px) and (orientation: landscape) {
    .sidebar {
        width: 25%;
        float: left;
    }
    
    .main-content {
        width: 75%;
        float: right;
    }
}

/* High-resolution displays (Retina) */
@media (-webkit-min-device-pixel-ratio: 2), 
       (min-resolution: 192dpi) {
    .logo {
        background-image: url('logo@2x.png');
        background-size: contain;
    }
    
    .images img {
        transform: scale(0.5);
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none;
    }
    
    body {
        font-size: 12pt;
        line-height: 1.5;
    }
    
    a[href]:after {
        content: " (" attr(href) ")";
    }
}

/* Dark mode */
@media (prefers-color-scheme: dark) {
    body {
        background-color: #121212;
        color: #ffffff;
    }
    
    .card {
        background-color: #1e1e1e;
        border: 1px solid #333;
    }
    
    .button {
        background-color: #2c2c2c;
        color: #ffffff;
    }
}

/* For devices with hover capability */
@media (hover: hover) {
    .button:hover {
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
}

/* Reduced motion preference */
@media (prefers-reduced-motion: reduce) {
    * {
        animation: none !important;
        transition: none !important;
        scroll-behavior: auto !important;
    }
    
    .animated-element {
        transform: none !important;
    }
}

/* Viewport height adjustments for mobile browsers */
@media screen and (max-height: 450px) {
    .modal {
        max-height: 80vh;
        overflow-y: auto;
    }
    
    .nav-menu {
        position: static;
    }
}
    </style>
</head>
<body>
    
</body>
</html>