<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>ConPacTor | HealthCardGo</title>

  <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}" />
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.4.0/fonts/remixicon.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

  <style>
    .map-container {
      width: 200%;
      max-width: 1000px;
      margin: 30px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      animation: transitionIn-Y-over 0.5s;
    }

    .map {
      width: 100%;
      height: 900px;
      border-radius: 12px;
      overflow: hidden;
    }

    /* Styles for the dropdown */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: #f9f9f9;
      min-width: 160px;
      box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
      z-index: 1;
      border-radius: 5px;
    }

    .dropdown-content button {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      width: 100%;
      /* Make buttons fill the dropdown width */
      text-align: left;
      border: none;
      background: none;
      cursor: pointer;
      font-size: 1rem;
      display: flex;
      /* For icon and text alignment */
      align-items: center;
    }

    .dropdown-content button img {
      margin-right: 8px;
      /* Space between flag and text */
      width: 20px;
      /* Adjust flag size */
      height: auto;
    }

    .dropdown-content button:hover {
      background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }
  </style>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @fluxAppearance
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">

  {{ $slot }}

  @fluxScripts
  @stack('scripts')
</body>

</html>
