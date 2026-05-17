<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="referrer" content="no-referrer">
    <title>Lampiran analisa kasus</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            background: #eceef3;
            overscroll-behavior-y: contain;
            -webkit-user-select: none;
            user-select: none;
        }
        body {
            overflow-y: auto;
            overflow-x: auto;
            padding-top: 48px;
        }
        #zoom-bar {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.97);
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 8px rgba(31, 34, 38, 0.08);
            font-family: system-ui, -apple-system, sans-serif;
        }
        #zoom-bar.visible { display: flex; }
        #zoom-bar button {
            min-width: 40px;
            height: 36px;
            padding: 0 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            background: #fff;
            color: #495057;
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1;
            cursor: pointer;
        }
        #zoom-bar button:hover:not(:disabled) {
            background: #f8f9fa;
            border-color: #6f42c1;
            color: #6f42c1;
        }
        #zoom-bar button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }
        #zoom-bar #zoom-reset-btn {
            font-size: 0.8125rem;
            font-weight: 600;
            min-width: auto;
            padding: 0 14px;
        }
        #zoom-pct {
            min-width: 48px;
            text-align: center;
            font-size: 0.8125rem;
            font-variant-numeric: tabular-nums;
            color: #6f42c1;
            font-weight: 600;
        }
        #scroller {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 0 28px;
            min-height: calc(100% - 48px);
        }
        #pages {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        #pages canvas {
            display: block;
            margin: 0 auto 14px auto;
            height: auto;
            border-radius: 6px;
            box-shadow: 0 4px 16px rgba(31, 34, 38, 0.12);
        }
        #err {
            padding: 20px 16px;
            color: #842029;
            text-align: center;
            font-family: system-ui, sans-serif;
            font-size: 0.95rem;
            max-width: 420px;
            margin: 48px auto 0;
        }
        #loading {
            padding: 40px 16px;
            text-align: center;
            font-family: system-ui, sans-serif;
            color: #6c757d;
            font-size: 0.95rem;
        }
        @media print {
            body * { display: none !important; visibility: hidden !important; }
        }
    </style>
</head>
<body>
    <div id="zoom-bar" aria-label="Kontrol zoom PDF">
        <button type="button" id="zoom-out" title="Perkecil" aria-label="Perkecil">−</button>
        <span id="zoom-pct">100%</span>
        <button type="button" id="zoom-in" title="Perbesar" aria-label="Perbesar">+</button>
        <button type="button" id="zoom-reset-btn" title="Sesuai lebar" aria-label="Sesuai lebar">Lebar</button>
    </div>
    <div id="scroller">
        <div id="pages"></div>
        <p id="loading">Memuat lampiran…</p>
        <p id="err" style="display:none"></p>
    </div>
    <script type="module">
        const PDF_VER = '4.4.168';
        const FETCH_URL = @json($pdfFetchUrl);
        const MIN_ZOOM = 0.5;
        const MAX_ZOOM = 3;
        const ZOOM_STEP = 1.15;

        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
            return false;
        });

        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && ['p', 'P', 's', 'S'].includes(e.key)) {
                e.preventDefault();
                return false;
            }
        });

        window.addEventListener('beforeprint', function (e) {
            e.preventDefault?.();
            return false;
        }, true);

        const loadingEl = document.getElementById('loading');
        const errEl = document.getElementById('err');
        const pagesEl = document.getElementById('pages');
        const zoomBar = document.getElementById('zoom-bar');
        const zoomPct = document.getElementById('zoom-pct');
        const btnZoomIn = document.getElementById('zoom-in');
        const btnZoomOut = document.getElementById('zoom-out');
        const btnZoomReset = document.getElementById('zoom-reset-btn');

        let pdfDoc = null;
        let pdfjsLib = null;
        let zoomLevel = 1;
        let isRendering = false;

        async function fail(msg) {
            loadingEl.style.display = 'none';
            errEl.textContent = msg;
            errEl.style.display = 'block';
        }

        function updateZoomButtons() {
            btnZoomOut.disabled = zoomLevel <= MIN_ZOOM + 0.001;
            btnZoomIn.disabled = zoomLevel >= MAX_ZOOM - 0.001;
            btnZoomReset.disabled = Math.abs(zoomLevel - 1) < 0.001;
            zoomPct.textContent = Math.round(zoomLevel * 100) + '%';
        }

        function baseFitWidth() {
            return Math.max(Math.min(window.innerWidth - 24, 1200), 280);
        }

        async function renderAllPages() {
            if (!pdfDoc || !pdfjsLib || isRendering) return;
            isRendering = true;
            btnZoomIn.disabled = true;
            btnZoomOut.disabled = true;
            btnZoomReset.disabled = true;

            pagesEl.innerHTML = '';

            try {
                for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                    const page = await pdfDoc.getPage(pageNum);
                    const base = page.getViewport({ scale: 1 });
                    const scaleFit = baseFitWidth() / base.width;
                    const viewport = page.getViewport({ scale: scaleFit * zoomLevel });

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d', { alpha: false });
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    await page.render({ canvasContext: ctx, viewport }).promise;
                    pagesEl.appendChild(canvas);
                }
            } finally {
                isRendering = false;
                updateZoomButtons();
            }
        }

        btnZoomIn.addEventListener('click', function () {
            zoomLevel = Math.min(MAX_ZOOM, zoomLevel * ZOOM_STEP);
            renderAllPages();
        });
        btnZoomOut.addEventListener('click', function () {
            zoomLevel = Math.max(MIN_ZOOM, zoomLevel / ZOOM_STEP);
            renderAllPages();
        });
        btnZoomReset.addEventListener('click', function () {
            zoomLevel = 1;
            renderAllPages();
        });

        let resizeTimer = null;
        window.addEventListener('resize', function () {
            if (!pdfDoc) return;
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function () {
                renderAllPages();
            }, 200);
        });

        try {
            pdfjsLib = await import(`https://cdn.jsdelivr.net/npm/pdfjs-dist@${PDF_VER}/build/pdf.mjs`);
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                `https://cdn.jsdelivr.net/npm/pdfjs-dist@${PDF_VER}/build/pdf.worker.mjs`;

            const response = await fetch(FETCH_URL, {
                credentials: 'same-origin',
                headers: {
                    'X-Pspk-Inline-Pdf': '1',
                    'Accept': 'application/pdf',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                referrerPolicy: 'same-origin'
            });

            if (!response.ok) {
                await fail(response.status === 403 ? 'Akses lampiran tidak diizinkan.' : 'Gagal memuat dokumen.');
                throw new Error('pdf fetch failed');
            }

            const buffer = await response.arrayBuffer();

            try {
                pdfDoc = await pdfjsLib.getDocument({ data: buffer, verbosity: 0 }).promise;
            } catch (e) {
                await fail('Format dokumen tidak valid.');
                throw e;
            }

            loadingEl.style.display = 'none';
            zoomBar.classList.add('visible');
            updateZoomButtons();
            await renderAllPages();
        } catch (e) {
            if (loadingEl.style.display !== 'none') {
                loadingEl.style.display = 'none';
                if (!errEl.textContent) {
                    await fail('Tidak dapat menampilkan dokumen.');
                }
            }
        }
    </script>
</body>
</html>
