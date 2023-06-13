<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ИС D-Жетекчи</title>
    <script src="{{ asset('jquery.js') }}"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body class="hold-transition sidebar-mini layout-navbar-fixed layout-fixed">

   <div class="content"> 
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content text-sm table-sm">
                @yield('content')
            </section>
            <!-- /.content -->
        </div>

   </div>

   
</body>

</html>