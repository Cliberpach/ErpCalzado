<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Comprobante Electrónico</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        /* ========== COLORES COMANDAPRO ========== */
        :root {
            --cp-primary: #3B82F6;
            --cp-secondary: #60A5FA;
            --cp-light: #E0F2FE;
            --cp-dark: #1E3A8A;
            --cp-accent: #2563EB;
        }

        /* ========== ESTILOS GENERALES ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 15px;
            position: relative;
            overflow-x: hidden;
        }

        /* Fondo animado sutil */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 900px;
        }

        /* ========== LOGO/HEADER ========== */
        .brand-header {
            text-align: center;
            margin-bottom: 30px;
            animation: fadeInDown 0.6s ease-out;
        }

        .brand-logo {
            background: white;
            padding: 6px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: inline-block;
            margin-bottom: 15px;
        }

        .brand-title {
            color: white;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            margin-bottom: 8px;
        }

        .brand-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            font-weight: 300;
            letter-spacing: 1px;
        }

        /* ========== CARD PRINCIPAL ========== */
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
            background: white;
        }

        .card-header {
            background: linear-gradient(135deg, var(--cp-primary) 0%, var(--cp-accent) 100%);
            color: white;
            padding: 25px 30px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }

        .card-header h5 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card-header h5::before {
            content: '🔍';
            font-size: 28px;
        }

        .card-body {
            padding: 35px 30px;
            background: white;
        }

        /* ========== SECCIÓN DE BÚSQUEDA ========== */
        .search-section {
            background: linear-gradient(135deg, var(--cp-light) 0%, #ffffff 100%);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 2px solid var(--cp-secondary);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
        }

        .search-section h6 {
            color: var(--cp-dark);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ========== FORMULARIO ========== */
        .form-label {
            color: var(--cp-dark);
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 2px solid #E0F2FE;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--cp-primary);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
            background-color: #F0F9FF;
        }

        .form-control::placeholder {
            color: #9CA3AF;
        }

        /* ========== BOTONES ========== */
        .btn-primary {
            background: linear-gradient(135deg, var(--cp-primary) 0%, var(--cp-accent) 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            background: linear-gradient(135deg, var(--cp-accent) 0%, var(--cp-primary) 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-secondary {
            background: #F3F4F6;
            color: #4B5563;
            border: 2px solid #E5E7EB;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #E5E7EB;
            border-color: #D1D5DB;
            color: #1F2937;
            transform: translateY(-2px);
        }

        /* ========== TABLA DE RESULTADOS ========== */
        .results-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            border: 2px solid var(--cp-light);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.05);
        }

        .results-section h6 {
            color: var(--cp-dark);
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .results-section h6::before {
            content: '📄';
            font-size: 20px;
        }

        /* Tabla mejorada */
        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(135deg, var(--cp-primary) 0%, var(--cp-accent) 100%);
        }

        .table thead th {
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
            padding: 18px 15px;
            border: none;
            white-space: nowrap;
            background-color: transparent;
        }

        .table thead th:first-child {
            border-top-left-radius: 10px;
        }

        .table thead th:last-child {
            border-top-right-radius: 10px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
            background-color: white;
        }

        .table tbody tr:nth-child(even) {
            background-color: #F9FAFB;
        }

        .table tbody tr:hover {
            background-color: var(--cp-light) !important;
            transform: translateX(5px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .table tbody td {
            padding: 18px 15px;
            vertical-align: middle;
            font-size: 14px;
            border-bottom: 1px solid #E5E7EB;
            color: #1F2937;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        /* Estilos para las celdas específicas */
        .table tbody td:nth-child(1) {
            font-weight: 600;
            color: var(--cp-dark);
        }

        .table tbody td:nth-child(2) {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--cp-primary);
        }

        .table tbody td:nth-child(3) {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #059669;
            font-size: 15px;
        }

        /* Columna de descargas */
        .table tbody td:nth-child(4) {
            text-align: center;
        }

        /* Botones de descarga */
        .btn-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            margin: 0 4px;
            text-decoration: none;
            color: white;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-download:active {
            transform: translateY(0);
        }

        .btn-xml {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        }

        .btn-xml:hover {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
        }

        .btn-pdf {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }

        .btn-pdf:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .btn-cdr {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        }

        .btn-cdr:hover {
            background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);
        }

        /* Estado vacío mejorado */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #6B7280;
            background: linear-gradient(135deg, #F9FAFB 0%, #F3F4F6 100%);
            border-radius: 10px;
            margin-top: 20px;
        }

        .empty-state-icon {
            font-size: 72px;
            margin-bottom: 20px;
            opacity: 0.4;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .empty-state h6 {
            color: #374151;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .empty-state p {
            color: #6B7280;
            font-size: 14px;
            max-width: 400px;
            margin: 0 auto;
        }

        /* ========== BADGES ========== */
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success {
            background-color: #10B981;
            color: white;
        }

        .badge-warning {
            background-color: #F59E0B;
            color: white;
        }

        .badge-danger {
            background-color: #EF4444;
            color: white;
        }

        .badge-info {
            background-color: var(--cp-secondary);
            color: white;
        }

        /* ========== MENSAJE VACÍO ========== */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6B7280;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h6 {
            color: #4B5563;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #9CA3AF;
            font-size: 14px;
        }

        /* ========== LOADING SPINNER ========== */
        .spinner-border-sm {
            width: 18px;
            height: 18px;
            border-width: 2px;
        }

        /* ========== FOOTER ========== */
        .footer {
            text-align: center;
            margin-top: 40px;
            color: white;
            font-size: 14px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .footer a:hover {
            border-bottom-color: white;
        }

        /* ========== ANIMACIONES ========== */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1) rotate(0deg);
            }

            50% {
                transform: scale(1.1) rotate(5deg);
            }
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .brand-title {
                font-size: 24px;
            }

            .card-header h5 {
                font-size: 20px;
            }

            .card-body {
                padding: 25px 20px;
            }

            .search-section,
            .results-section {
                padding: 20px 15px;
            }

            /* Tabla responsive */
            .table {
                font-size: 12px;
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .table thead th,
            .table tbody td {
                padding: 12px 10px;
            }

            /* Botones de descarga más pequeños en móvil */
            .btn-download {
                padding: 6px 12px;
                font-size: 10px;
                margin: 2px;
            }

            .table tbody td:nth-child(3) {
                font-size: 13px;
            }

            /* Ajustar estado vacío */
            .empty-state {
                padding: 50px 15px;
            }

            .empty-state-icon {
                font-size: 48px;
            }

            .empty-state h6 {
                font-size: 16px;
            }
        }

        /* Ajuste para pantallas muy pequeñas */
        @media (max-width: 576px) {
            body {
                padding: 15px 10px;
            }

            .brand-logo {
                padding: 10px;
            }

            .card-header h5 {
                font-size: 18px;
                flex-direction: column;
                gap: 5px;
            }

            .btn-download {
                display: block;
                width: 100%;
                margin: 4px 0;
            }
        }

        .brand-logo-img {
            height: 70px;
            user-select: none;
            -webkit-user-drag: none;
        }
    </style>

    <link rel="preload" href="{{ asset('assets/images/logo-full.svg') }}" as="image">

</head>

<body style="height: 100vh !important;">
    <!-- Header de marca -->
    <div class="brand-header">

        <div class="brand-logo">
            <img src="{{ asset('assets/images/logo-full.svg') }}" alt="ErpCalzado" class="brand-logo-img"
                draggable="false" loading="eager">
        </div>

        <h1 class="brand-title">Consulta de Comprobantes</h1>
    </div>

    <!-- Container principal -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Buscar Comprobante Electrónico</h5>
            </div>
            <div class="card-body">

                <!-- Sección de búsqueda -->
                <div class="search-section">
                    <h6>🔎 Ingrese los datos del comprobante</h6>
                    @include('ventas.documentos.consultar.forms.form_consultar')
                </div>

                <!-- Sección de resultados -->
                <div class="results-section">
                    <h6>Resultados de la búsqueda</h6>
                    @include('ventas.documentos.consultar.tables.tbl_documento')
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; {{ date('Y') }} <a href="#">Merris</a> - ErpCalzado</p>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="{{ asset('js/utils.js') }}?v={{ time() }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadSelect2();
        events();
    })

    function events() {
        document.querySelector('#formConsultarComprobante').addEventListener('submit', (e) => {
            e.preventDefault();
            consultarComprobante(e.target);
        })
    }

    async function consultarComprobante(formConsultar) {
        try {

            toastr.clear();
            ocultarTablaDocumento();

            Swal.fire({
                title: 'Consultando...',
                text: 'Por favor, espere.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    const swalContainer = document.querySelector('.swal2-container');
                    if (swalContainer) {
                        swalContainer.style.height = '100vh';
                    }
                }
            });


            const formData = new FormData(formConsultar);
            const params = new URLSearchParams(formData).toString();


            const url = `{{ route('consultarComprobante.buscar') }}`;
            const res = await axios.post(url, formData);

            if (res.data.success) {
                const documento = res.data.documento;

                if (!documento) {

                    Swal.fire({
                        title: '¡No se encontró su comprobante!',
                        text: 'Consulta completada.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });

                    return;

                }

                pintarDocumento(res.data.documento);
                mostrarTablaDocumento();
                toastr.success(res.data.message, 'OPERACIÓN COMPLETA');

                Swal.close();

            } else {
                toastr.error('ERROR INTENTA DE NUEVO');
                Swal.close();
            }
        } catch (error) {
            toastr.error(error, 'ERROR AL CONSULTAR');
            Swal.close();
        }
    }

    function pintarDocumento(documento) {

        const d = new Date(documento.created_at);
        const fecha_emision =
            `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        const totalPagar = parseFloat(documento.total_pagar).toFixed(2);
        const urlPdf =
            `{{ route('consultarComprobante.pdf') }}?tipo_doc=${documento.tipo_venta_codigo}&fecha_emision=${fecha_emision}&serie=${documento.serie}&correlativo=${documento.correlativo}&doc_cliente=${documento.cliente_numero_documento}&monto_total=${totalPagar}`;

        const urlXml =
            `{{ route('consultarComprobante.xml') }}?tipo_doc=${documento.tipo_venta_codigo}&fecha_emision=${fecha_emision}&serie=${documento.serie}&correlativo=${documento.correlativo}&doc_cliente=${documento.cliente_numero_documento}&monto_total=${totalPagar}`;
        const urlCdr =
            `{{ route('consultarComprobante.cdr') }}?tipo_doc=${documento.tipo_venta_codigo}&fecha_emision=${fecha_emision}&serie=${documento.serie}&correlativo=${documento.correlativo}&doc_cliente=${documento.cliente_numero_documento}&monto_total=${totalPagar}`;

        let row = `<tr>
                        <td style="vertical-align:middle;">${documento.cliente_numero_documento}</td>
                        <td style="vertical-align:middle;">${documento.serie}-${documento.correlativo}</td>
                        <td style="vertical-align:middle;">${parseFloat(documento.total_pagar).toFixed(2)}</td>
                        <td>
                            <div class="container my-4">
                                <div class="d-flex justify-content-center">
                                    <a href="${urlXml}" class="btn btn-primary mx-2" id="btnXML">XML</a>
                                    <a target=”_blank” href="${urlPdf}" class="btn btn-success mx-2" id="btnPDF">PDF</a>
                                    <a href="${urlCdr}" class="btn btn-danger mx-2" id="btnCDR">CDR</a>
                                </div>
                            </div>
                        </td>
                    </tr>`;

        const tbody = document.querySelector('#tblDocumento tbody');
        tbody.innerHTML = row;
    }

    function loadSelect2() {
        $('#tipoDocumento').select2({
            placeholder: "Seleccionar",
            allowClear: true,
            width: '100%'
        });
    }

    function mostrarTablaDocumento() {
        const table = document.getElementById('tblDocumento');
        table.classList.add('show');
    }

    function ocultarTablaDocumento() {
        const table = document.getElementById('tblDocumento');
        table.classList.remove('show');
    }
</script>

</html>
