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
        <!-- End plugin css for this page -->

        <!-- inject:css -->
        <link rel="stylesheet" href="{{ asset('assets/fonts/feather-font/css/iconfont.css') }}">

        <!-- Layout styles -->  
        <link rel="stylesheet" href="{{ asset('assets/css/demo2/style.css') }}">
        <!-- End layout styles -->

        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        @stack('css')
    </head>
    <body>
        <div class="main-wrapper">
            {{-- <x-layouts.peserta.sidebar /> --}}
            <x-layouts.peserta.navbar />
		    <div class="page-wrapper">
                {{-- <x-layouts.peserta.navbar /> --}}

			    <div class="page-content">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- core:js -->
        <script src="{{ asset('assets/vendors/core/core.js') }}"></script>
        <!-- endinject -->
        <script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>

        <!-- Plugin js for this page -->
        {{-- <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script> --}}
        <!-- End plugin js for this page -->

        <script src="{{ asset('assets/vendors/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
        <script src="{{ asset('assets/vendors/flatpickr/flatpickr.min.js') }}"></script>
        {{-- <script src="{{ asset('assets/js/flatpickr.js') }}"></script> --}}

            <!-- Plugin js for this page -->
        <script src="{{ asset('assets/vendors/ace-builds/src-min/ace.js') }}"></script>
        <script src="{{ asset('assets/vendors/ace-builds/src-min/theme-chaos.js') }}"></script>

        <!-- inject:js -->
        <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
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
            window.addEventListener('livewire:init', function () {
                Livewire.on('toast', data => {
                    const payload = Array.isArray(data) ? data[0] : data;
                    if (!payload || typeof toastr === 'undefined' || typeof toastr[payload.type] !== 'function') {
                        return;
                    }
                    toastr[payload.type](payload.message, null, { positionClass: 'toast-top-center' });
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
        </script>
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
        <script>
            function initFlatpickrs(container) {
                (container || document).querySelectorAll('[data-flatpickr]').forEach(input => {
                    if (input._flatpickr) return;
                    flatpickr(input, {
                        dateFormat: 'd-m-Y',
                        allowInput: false,
                        defaultDate: input.value || null,
                        onChange: (_, dateStr) => {
                            input.value = dateStr;
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                        }
                    });
                });
            }

            document.addEventListener('livewire:initialized', () => {
                initFlatpickrs();
                Livewire.hook('morph.added', ({ el }) => {
                    if (el.querySelectorAll) initFlatpickrs(el);
                });
            });

            document.addEventListener('livewire:navigated', () => {
                initFlatpickrs();
            });
        </script>
        <script>
            function initPesertaTooltips(container) {
                (container || document).querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                    var instance = bootstrap.Tooltip.getInstance(el);
                    if (instance) instance.dispose();
                    new bootstrap.Tooltip(el);
                });
            }

            document.addEventListener('livewire:initialized', function () {
                initPesertaTooltips();
                Livewire.hook('morph.updated', function () {
                    initPesertaTooltips();
                });
            });

            if (!window.__pesertaNavigateInit) {
                window.__pesertaNavigateInit = true;
                document.addEventListener('livewire:navigated', function () {
                    if (typeof feather !== 'undefined') feather.replace();
                    initPesertaTooltips();
                });
            }
        </script>
        <script>
            if (!window.ujianAwait) {
                window.ujianAwait = function (request, method, timeoutMs) {
                    const ms = typeof timeoutMs === 'number' ? timeoutMs : 20000;
                    let timer = null;
                    let stopHook = function () {};
                    let cleaned = false;

                    function cleanup() {
                        if (cleaned) return;
                        cleaned = true;
                        if (timer !== null) clearTimeout(timer);
                        try { stopHook(); } catch (e) {}
                    }

                    const timeout = new Promise((resolve) => {
                        timer = setTimeout(() => resolve({ __ujianTimeout: true }), ms);
                    });

                    const failed = new Promise((resolve) => {
                        if (typeof Livewire === 'undefined' || typeof Livewire.hook !== 'function') {
                            return;
                        }
                        const unsubscribe = Livewire.hook('commit', ({ commit, fail }) => {
                            const calls = (commit && commit.calls) || [];
                            if (!calls.some((call) => call.method === method) || typeof fail !== 'function') {
                                return;
                            }
                            fail(() => resolve({ __ujianTimeout: true }));
                        });
                        if (typeof unsubscribe === 'function') stopHook = unsubscribe;
                    });

                    const work = Promise.resolve().then(() => request());
                    work.catch(() => {});

                    const result = Promise.race([work, timeout, failed]);
                    result.then(cleanup, cleanup);
                    return result;
                };
            }
        </script>
        @stack('js')
    </body>
</html>
