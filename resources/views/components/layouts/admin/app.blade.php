<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="admin-current-route" content="{{ Route::currentRouteName() }}">
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

            window.openAbsensiPdf = function(url) {
                if (!url) {
                    return;
                }

                if (window.__absensiPdfTab && !window.__absensiPdfTab.closed) {
                    window.__absensiPdfTab.location.href = url;
                    window.__absensiPdfTab = null;

                    return;
                }

                const tab = window.open(url, '_blank');

                if (!tab) {
                    window.location.href = url;
                }
            };

            Livewire.on('download-attendance', (...params) => {
                const payload = params[0];
                const url = payload?.url
                    ?? (Array.isArray(payload) ? payload[0]?.url : null);

                window.openAbsensiPdf(url);
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

        window.addEventListener('show-paksa-akhiri-confirmation', data => {
            Swal.fire({
                title: 'Paksa akhiri ujian?',
                text: 'Skor akan dihitung otomatis dari jawaban yang tersimpan dan status ujian diubah menjadi selesai.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f0ad4e',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Akhiri ujian',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('paksaAkhiri');
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
            
            // Initialize tooltip saat pertama kali load
            initTooltips();
            
            // Re-initialize setiap kali Livewire selesai update
            Livewire.hook('morph.updated', ({ el, component }) => {
                feather.replace();
                initTooltips();
            });
        });
        
        // Function untuk initialize tooltips
        function initTooltips() {
            // Dispose semua tooltip yang sudah ada untuk menghindari duplikasi
            var existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            existingTooltips.forEach(function(element) {
                var tooltipInstance = bootstrap.Tooltip.getInstance(element);
                if (tooltipInstance) {
                    tooltipInstance.dispose();
                }
            });
            
            // Initialize tooltip baru
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    </script>
    <script>
        document.addEventListener('livewire:initialized', () => {

            const bindFlatpickrToggle = (input) => {
                const toggle = input.closest('.input-group')?.querySelector('[data-toggle]');
                if (!toggle || toggle._flatpickrBound) {
                    return;
                }

                toggle._flatpickrBound = true;
                toggle.addEventListener('click', () => input._flatpickr?.open());
            };

            const initFlatpickr = (input) => {
                input._flatpickr?.destroy();

                const model = input.dataset.model;

                requestAnimationFrame(() => {
                    const hiddenInput = input
                        .closest('.mb-4')
                        ?.querySelector(`input[type="hidden"][wire\\:model="${model}"]`);

                    const value = hiddenInput?.value || null;

                    input._flatpickr = flatpickr(input, {
                        dateFormat: 'd-m-Y',
                        allowInput: false,
                        defaultDate: value,

                        onChange: (_, dateStr) => {
                            if (!hiddenInput) return;
                            hiddenInput.value = dateStr;
                            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        },
                    });

                    bindFlatpickrToggle(input);
                });
            };

            const initTimeFlatpickr = (input) => {
                input._flatpickr?.destroy();

                const model = input.dataset.model;

                requestAnimationFrame(() => {
                    const hiddenInput = input
                        .closest('.mb-4')
                        ?.querySelector(`input[type="hidden"][wire\\:model="${model}"]`);

                    const value = hiddenInput?.value || null;

                    input._flatpickr = flatpickr(input, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: 'H.i',
                        time_24hr: true,
                        allowInput: false,
                        defaultDate: value || null,

                        onChange: (_, timeStr) => {
                            if (!hiddenInput) return;
                            hiddenInput.value = timeStr;
                            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                        },
                    });

                    bindFlatpickrToggle(input);
                });
            };

            const syncFilterDateToLivewire = (wrapper, dateStr) => {
                const property = wrapper.dataset.filterModel;
                const componentEl = wrapper.closest('[wire\\:id]');
                const component = componentEl
                    ? Livewire.find(componentEl.getAttribute('wire:id'))
                    : null;

                if (property && component) {
                    component.set(property, dateStr);
                    return;
                }

                const input = wrapper.querySelector('[data-input]');
                if (!input) return;

                input.value = dateStr;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            };

            const initPageDateFilterEl = (el) => {
                if (!el?.dataset?.filterModel) return;

                if (el._flatpickr) {
                    el._flatpickr.destroy();
                    el._flatpickr = null;
                }

                el._flatpickr = flatpickr(el, {
                    wrap: true,
                    dateFormat: 'd-m-Y',
                    allowInput: false,
                    onChange: (_, dateStr) => {
                        syncFilterDateToLivewire(el, dateStr);
                    },
                });
            };

            const initPageDateFilters = (root = document) => {
                if (root instanceof Element && root.dataset?.filterModel) {
                    initPageDateFilterEl(root);
                    return;
                }

                const scope = root instanceof Element ? root : document;
                scope.querySelectorAll('[data-filter-model]').forEach(initPageDateFilterEl);
            };

            initPageDateFilters();

            Livewire.hook('morph.added', ({ el }) => {
                initPageDateFilters(el);
                el.querySelectorAll('[data-flatpickr]').forEach(initFlatpickr);
                el.querySelectorAll('[data-flatpickr-time]').forEach(initTimeFlatpickr);
            });

            Livewire.on('reset-select2', () => {
                document.querySelectorAll('[data-filter-model]').forEach((el) => {
                    el._flatpickr?.clear();
                });
            });

            document.addEventListener('livewire:navigated', () => {
                setTimeout(() => initPageDateFilters(), 100);
            });

            Livewire.on('modalOpened', () => {
                setTimeout(() => {
                    document.querySelectorAll('[data-flatpickr]').forEach(initFlatpickr);
                    document.querySelectorAll('[data-flatpickr-time]').forEach(initTimeFlatpickr);
                }, 150);
            });

            Livewire.on('set-flatpickr', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                const input = document.querySelector(`[data-flatpickr][data-model="${data.model}"]`);
                if (input?._flatpickr && data.value) {
                    input._flatpickr.setDate(data.value, true, 'd-m-Y');
                }

                const hiddenInput = input
                    ?.closest('.mb-4')
                    ?.querySelector(`input[type="hidden"][wire\\:model="${data.model}"]`);

                if (hiddenInput && data.value) {
                    hiddenInput.value = data.value;
                    hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            Livewire.on('set-flatpickr-time', (payload) => {
                const data = Array.isArray(payload) ? payload[0] : payload;
                const input = document.querySelector(`[data-flatpickr-time][data-model="${data.model}"]`);
                if (input?._flatpickr && data.value) {
                    input._flatpickr.setDate(data.value, true, 'H.i');
                }
            });
        });
    </script>  
    <script>
        function syncAdminSidebar() {
            const sidebarNav = document.querySelector('#sidebarNav[data-server-managed]');
            if (!sidebarNav) return;

            const path = window.location.pathname.replace(/\/$/, '') || '/';

            sidebarNav.querySelectorAll('a.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            sidebarNav.querySelectorAll('.nav-item.active').forEach(item => {
                item.classList.remove('active');
            });
            sidebarNav.querySelectorAll('.collapse').forEach(el => {
                const instance = bootstrap.Collapse.getInstance(el);
                if (instance) instance.dispose();
                el.classList.remove('show');
            });
            sidebarNav.querySelectorAll('[data-bs-toggle="collapse"]').forEach(toggle => {
                toggle.classList.add('collapsed');
                toggle.setAttribute('aria-expanded', 'false');
            });

            let bestMatch = null;
            let bestLength = 0;

            sidebarNav.querySelectorAll('a.nav-link[href]').forEach(link => {
                if (link.hasAttribute('data-bs-toggle')) return;

                let linkPath;
                try {
                    linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/$/, '') || '/';
                } catch (e) {
                    return;
                }

                if (path === linkPath || path.startsWith(linkPath + '/')) {
                    if (linkPath.length > bestLength) {
                        bestLength = linkPath.length;
                        bestMatch = link;
                    }
                }
            });

            if (!bestMatch) return;

            bestMatch.classList.add('active');

            const navItem = bestMatch.closest('.nav-item');
            if (navItem) {
                navItem.classList.add('active');
            }

            const collapse = bestMatch.closest('.collapse');
            if (!collapse) return;

            collapse.classList.add('show');

            const toggle = sidebarNav.querySelector(`a[href="#${collapse.id}"][data-bs-toggle="collapse"]`);
            if (toggle) {
                toggle.classList.remove('collapsed');
                toggle.setAttribute('aria-expanded', 'true');
            }
        }

        if (!window.__adminNavigateInit) {
            window.__adminNavigateInit = true;

            document.addEventListener('DOMContentLoaded', syncAdminSidebar);

            document.addEventListener('livewire:navigated', () => {
                requestAnimationFrame(() => syncAdminSidebar());

                if (typeof feather !== 'undefined') feather.replace();

                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                    var instance = bootstrap.Tooltip.getInstance(el);
                    if (instance) instance.dispose();
                    new bootstrap.Tooltip(el);
                });

                initTooltips();
            });
        }
    </script>
    @stack('js')
</body>

</html>
