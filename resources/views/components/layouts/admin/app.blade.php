<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>
    <!-- color-modes:js -->
    <script src="{{ asset('assets/js/color-modes.js') }}"></script>
    <!-- endinject -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- End fonts -->

    <!-- core:css -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/core/core.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/flatpickr/flatpickr.min.css') }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">
    <!-- endinject -->

    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/select2/select2.min.css') }}">
    <!-- End plugin css for this page -->

    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">

    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/demo1/style.css') }}">
    <!-- End layout styles -->

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    @stack('styles')
</head>

<body>
    <div class="main-wrapper">
        <x-layouts.admin.sidebar />

        <div class="page-wrapper">
            <x-layouts.admin.navbar />

            <div class="page-content">
                {{ $slot }}
            </div>
        </div>
    </div>

    <audio id="alertSound" src="{{ asset('assets/sounds/alert.wav') }}" preload="auto"></audio>

    <!-- core:js -->
    <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
    <!-- endinject -->
    <script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>

    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <!-- End plugin js for this page -->

    <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/js/flatpickr.js') }}"></script>

    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.js') }}"></script>
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/vendors/ace-builds/src-min/ace.js') }}"></script>
    <script src="{{ asset('assets/vendors/ace-builds/src-min/theme-chaos.js') }}"></script>

    <!-- inject:js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <!-- endinject -->

    <!-- Custom js for this page -->
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <!-- End custom js for this page -->
    <!--Toastr-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @if (session()->has('toast'))
        <script>
            toastr.options = {
                positionClass: 'toast-top-center'
            };
            toastr.{{ session('toast.type') }}('{{ session('toast.message') }}');
        </script>
    @endif
    <script>
        window.addEventListener('livewire:init', function() {
            Livewire.on('toast', data => {
                toastr[data[0].type](data[0].message, null, {
                    positionClass: 'toast-top-center'
                });
            });
        });
    </script>
    <script>
        window.addEventListener('show-delete-confirmation', data => {
            Swal.fire({
                title: 'Apakah anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete');
                }
            });
        });

        window.addEventListener('change-status-portofolio-confirmation', data => {
            Swal.fire({
                title: 'Ubah Status Portofolio?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('changeStatusPortofolio');
                }
            });
        });

        window.addEventListener('change-status-event-confirmation', data => {
            Swal.fire({
                title: 'Ubah Status Event?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('changeStatusEvent');
                }
            });
        });
        
        window.addEventListener('change-status-timer-confirmation', data => {
            Swal.fire({
                title: 'Ubah Status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('changeStatusTimer');
                }
            });
        });

        window.addEventListener('change-status-peserta-confirmation', data => {
            Swal.fire({
                title: 'Ubah Status Peserta?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('changeStatusPeserta');
                }
            });
        });

        window.addEventListener('change-status-confirmation', data => {
            Swal.fire({
                title: 'Ubah Status?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('changeStatus');
                }
            });
        });
    </script>
    @vite(['resources/js/app.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Echo) {
                window.Echo.channel('admin.pelanggaran')
                .listen('.pelanggaran', (e) => {
                        toastr.options = {
                            positionClass: 'toast-top-center',
                            closeButton: true,
                        };
                        toastr.warning(`${e.user} meninggalkan tab! Peringatan ke-${e.peringatan}`);

                        // 🔊 Mainkan suara
                        const sound = document.getElementById('alertSound');
                        if (sound) {
                            sound.currentTime = 0;
                            sound.play().catch(err => console.warn('Audio play failed:', err));
                        }
                    });
            } else {
                console.error("Echo is not defined.");
            }
        });
    </script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Initialize feather icons saat pertama kali load
            feather.replace();
            
            // Re-initialize setiap kali Livewire selesai update
            Livewire.hook('morph.updated', ({ el, component }) => {
                feather.replace();
            });
        });
    </script>
    @stack('js')
</body>

</html>
