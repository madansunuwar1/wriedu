<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="session-timeout" content="{{ config('session.timeout', 15) * 60000 }}">
  <meta name="auth-user-id" content="{{ Auth::id() }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="pusher-key" content="{{ config('broadcasting.connections.pusher.key') }}">
  <meta name="pusher-cluster" content="{{ config('broadcasting.connections.pusher.cluster') }}">

  <!-- Favicon icon-->
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png">

  <!-- Core Css -->
  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">

  <title>Wri Education Consultancy</title>
  <!-- jvectormap  -->
  <link rel="stylesheet" href="{{ asset('assets/libs/jvectormap/jquery-jvectormap.css') }}">
  <link defer rel="stylesheet" href="{{ asset('assets/css/tabler-icons.min.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body>
    <div id="app">
        <!-- Vue will render a pre-loader here initially -->
    </div>

    <!-- The only 3rd party JS you might need here -->
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Vite loads all your Vue app's JS and CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
<style>
 
</style>
  <script>
    setTimeout(() => {
      document.getElementById('loader').style.opacity = '0';
      setTimeout(() => {
        document.getElementById('loader').style.display = 'none';
      }, 100); // Wait for fade-out animation
    }, 600);
  </script>
  <script src="https://cdn.jsdelivr.net/npm/nepali-date-converter/dist/nepali-date-converter.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/nepali-date-converter@3.3.4/dist/nepali-date-converter.umd.min.js"></script>
  <!-- Vendor JS Files -->
  <script defer src="{{ asset('assets/js/iconify-icon.min.js') }}"></script>

  <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

  <!-- Bootstrap Bundle -->
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

  <!-- SimpleBar JS -->
  <script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>

  <!-- Theme JS Files -->
  <script src="{{ asset('assets/js/theme/app.init.js') }}"></script>
  <script src="{{ asset('assets/js/theme/theme.js') }}"></script>
  <script src="{{ asset('assets/js/theme/app.min.js') }}"></script>
  <script src="{{ asset('assets/js/theme/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/theme/feather.min.js') }}"></script>

  <!-- Highlight.js (Code View) -->
  <script src="{{ asset('assets/js/highlights/highlight.min.js') }}"></script>
  <script>
    hljs.initHighlightingOnLoad();

    document.querySelectorAll("pre.code-view > code").forEach((codeBlock) => {
      codeBlock.textContent = codeBlock.innerHTML;
    });
  </script>

  <!-- jVectorMap and ApexCharts -->


  <!-- Dashboard JS -->
  <script src="{{ asset('assets/js/dashboards/dashboard.js') }}"></script>
</html>