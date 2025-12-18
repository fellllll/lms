<x-app-layout>
    <x-slot name="header">
        <div class="relative">
            <a href="{{ route('reserve.view') }}" class="absolute left-0 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700">
                ← Back to Reservations
            </a>
            <h1 class="text-2xl font-bold text-center">PDF Viewer</h1>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-md shadow-md">
                <div id="pdfContainer" class="w-full border mb-4 overflow-auto" style="min-height: 400px;">
                    <canvas id="pdfCanvas" class="max-w-full h-auto"></canvas>
                </div>
                <div class="flex justify-between items-center">
                    <button id="prevPage" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Previous</button>
                    <span id="pageInfo">Page: <span id="currentPage">1</span> / <span id="totalPages">1</span></span>
                    <button id="nextPage" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Next</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        let pdfDoc = null;
        let currentPage = 1;
        let totalPages = 1;

        async function loadPdf(url) {
            try {
                const loadingTask = pdfjsLib.getDocument(url);
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;
                document.getElementById('totalPages').textContent = totalPages;
                renderPage(currentPage);
            } catch (error) {
                console.error('Error loading PDF:', error);
                alert('Error loading PDF');
            }
        }

        async function renderPage(pageNum) {
            try {
                const page = await pdfDoc.getPage(pageNum);
                const container = document.getElementById('pdfContainer');
                const containerWidth = container.clientWidth - 40; // Padding
                const viewport = page.getViewport({ scale: 1 });
                const scale = Math.min(containerWidth / viewport.width, 2.0); // Max scale 2.0 to prevent too large
                
                console.log('Container width:', containerWidth, 'Viewport width:', viewport.width, 'Scale:', scale);

                const scaledViewport = page.getViewport({ scale });

                const canvas = document.getElementById('pdfCanvas');
                const context = canvas.getContext('2d');
                canvas.height = scaledViewport.height;
                canvas.width = scaledViewport.width;

                const renderContext = {
                    canvasContext: context,
                    viewport: scaledViewport
                };

                await page.render(renderContext).promise;
                document.getElementById('currentPage').textContent = pageNum;

                // Adjust container height to fit canvas
                container.style.height = (canvas.height + 20) + 'px';
                console.log('Canvas rendered, height:', canvas.height);
            } catch (error) {
                console.error('Error rendering page:', error);
            }
        }

        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage(currentPage);
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage(currentPage);
            }
        });

        // Proteksi copy/right-click
        document.addEventListener('contextmenu', function(e) {
            if (e.target.id === 'pdfCanvas') {
                e.preventDefault();
            }
        });

        document.addEventListener('copy', function(e) {
            if (document.activeElement.id === 'pdfCanvas') {
                e.preventDefault();
            }
        });

        // Load PDF saat halaman load
        window.onload = function() {
            loadPdf('{{ $pdfUrl }}');
        };
    </script>
</x-app-layout>