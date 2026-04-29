<!DOCTYPE html>
<html lang="en">
  <head>
    @php
        use App\Models\GeneralSetting;
        $generalSettings = GeneralSetting::first();
    @endphp
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $generalSettings->app_name ?? 'Edulife ' }}</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">

    @include('user.layouts.partials.__style')
    <!-- End layout styles -->
    @if($generalSettings && $generalSettings->favicon)
            <link rel="icon" type="image/png" href="{{ asset('storage/' . $generalSettings->favicon) }}">
            <link rel="apple-touch-icon" href="{{ asset('storage/' . $generalSettings->favicon) }}">
        @else
            <link rel="icon" type="image/png" href="{{ asset('default-favicon.png') }}">
            <link rel="apple-touch-icon" href="{{ asset('default-favicon.png') }}">
    @endif
    {{-- <link rel="shortcut icon" href="assets/images/favicon.png" /> --}}
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      @include('user.layouts.partials.__sidebar')
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_navbar.html -->
        @include('user.layouts.partials.__navbar')
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">

            @yield('userContent')

          <!-- content-wrapper ends -->
          @include('user.layouts.partials.__footer')
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
   @include('user.layouts.partials.__script')
    <!-- End custom js for this page -->


    <button id="installBtn" style="
        display:none;
        position:fixed;
        bottom:20px;
        right:20px;
        background:#0d6efd;
        color:#fff;
        padding:10px 15px;
        border:none;
        border-radius:8px;
    ">
        📲 Install App
    </button>
  </body>
</html>
