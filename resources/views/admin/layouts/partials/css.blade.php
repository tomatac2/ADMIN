  <!-- Bootstrap css-->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/bootstrap.css') }}" onload="this.onload=null;this.rel='stylesheet'">

  <!-- Animated css-->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/animate.css') }}" onload="this.onload=null;this.rel='stylesheet'">

  <!-- Remixicon css-->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/remixicon.css') }}" onload="this.onload=null;this.rel='stylesheet'">

  <!-- Flag icon-->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/flag-icon.css') }}" media="print" onload="this.media='all'">

  <!-- Select2 css-->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/select2.css') }}" onload="this.onload=null;this.rel='stylesheet'">

  <!--Flatpickr css-->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/flatpickr.min.css') }}" media="print" onload="this.media='all'">

  <!-- Toastr css -->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/toastr.min.css') }}" media="print" onload="this.media='all'">

  <!-- Slick Slider css -->
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/slick.css') }}" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="stylesheet"  as="style" href="{{ asset('css/vendors/slick-theme.css') }}" onload="this.onload=null;this.rel='stylesheet'">

  <style>
    /* Fix profile dropdown scrollbar issue in RTL/Arabic */
    .profile-dropdown.onhover-show-div {
        overflow: visible !important;
        max-height: none !important;
        height: auto !important;
        min-width: 200px !important;
        width: auto !important;
    }
    .profile-dropdown.onhover-show-div ul {
        overflow: visible !important;
        max-height: none !important;
        height: auto !important;
        width: 100% !important;
    }
    .profile-dropdown.onhover-show-div ul li {
        white-space: nowrap !important;
    }
    .profile-dropdown.onhover-show-div ul li a {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 10px 15px !important;
        width: 100% !important;
    }
    .profile-dropdown.onhover-show-div ul li a span {
        overflow: visible !important;
        text-overflow: clip !important;
        white-space: nowrap !important;
    }
    /* Fix position for RTL/Arabic language */
    [dir="rtl"] .page-wrapper .page-main-header .main-header .nav-right .profile-dropdown {
        left: 0 !important;
        right: auto !important;
    }
</style>