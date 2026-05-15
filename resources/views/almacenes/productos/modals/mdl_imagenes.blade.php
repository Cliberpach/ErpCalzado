<div class="modal fade" id="mdl_imagenes_producto" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-images mr-2"></i>
                    Imágenes — <span id="mdl_imagenes_nombre" class="text-primary"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                {{-- Sin colores --}}
                <div id="mdl_imagenes_empty" class="text-center py-4 d-none">
                    <i class="fas fa-palette fa-2x text-muted mb-2 d-block"></i>
                    <p class="text-muted">Este producto no tiene colores registrados.</p>
                </div>

                {{-- Wrapper tabs --}}
                <div id="mdl_imagenes_tabs_wrapper" class="d-none">

                    {{-- Buscador de colores --}}
                    <div class="mb-2" style="max-width:280px;">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text"
                                   id="mdl_imagenes_search"
                                   class="form-control"
                                   placeholder="Buscar color..."
                                   oninput="filtrarColores()">
                        </div>
                        <small class="text-muted" id="mdl_imagenes_search_count"></small>
                    </div>

                    {{-- Tabs de colores (se construyen dinámicamente) --}}
                    <div style="overflow-x:auto;">
                        <ul class="nav nav-tabs flex-nowrap mb-3" id="mdl_imagenes_tabs" role="tablist"
                            style="min-width:max-content;"></ul>
                    </div>
                    <div class="tab-content" id="mdl_imagenes_tab_content"></div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times mr-1"></i> Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    const mdlImagenes = {
        productoId:  null,
        ponds:       {},    // colorId → FilePond instance
        initialized: {},    // colorId → bool (imágenes ya cargadas)
        totalColores: 0,
    };

    /* ── Abrir modal ──────────────────────────────────────────────── */
    async function openMdlImagenes(productoId, nombre) {
        mdlImagenes.productoId  = productoId;
        mdlImagenes.ponds       = {};
        mdlImagenes.initialized = {};

        document.getElementById('mdl_imagenes_nombre').textContent = nombre;
        document.getElementById('mdl_imagenes_empty').classList.add('d-none');
        document.getElementById('mdl_imagenes_tabs_wrapper').classList.add('d-none');
        document.getElementById('mdl_imagenes_tabs').innerHTML         = '';
        document.getElementById('mdl_imagenes_tab_content').innerHTML  = '';
        document.getElementById('mdl_imagenes_search').value           = '';
        document.getElementById('mdl_imagenes_search_count').textContent = '';

        $('#mdl_imagenes_producto').modal('show');
        mostrarAnimacion();

        try {
            const res = await axios.get(route('almacenes.producto.getColorsByProducto', productoId));

            if (!res.data.success || !res.data.data.length) {
                document.getElementById('mdl_imagenes_empty').classList.remove('d-none');
                return;
            }

            mdlImagenes.totalColores = res.data.data.length;
            buildTabs(res.data.data);
            document.getElementById('mdl_imagenes_tabs_wrapper').classList.remove('d-none');
            actualizarContador();

            // Cargar imágenes del primer tab activo
            const firstColorId = res.data.data[0].id;
            await loadImages(firstColorId);

        } catch (e) {
            toastr.error('No se pudieron cargar los colores.', 'ERROR');
        } finally {
            ocultarAnimacion();
        }
    }

    /* ── Construir tabs ───────────────────────────────────────────── */
    function buildTabs(colores) {
        const tabsEl    = document.getElementById('mdl_imagenes_tabs');
        const contentEl = document.getElementById('mdl_imagenes_tab_content');

        colores.forEach((color, idx) => {
            const active  = idx === 0 ? 'active show' : '';
            const swatch  = color.codigo
                ? `<span style="display:inline-block;width:11px;height:11px;border-radius:50%;
                               background:${color.codigo};border:1px solid #bbb;
                               margin-right:5px;vertical-align:middle;flex-shrink:0;"></span>`
                : '';

            tabsEl.innerHTML += `
                <li class="nav-item" data-color-name="${color.descripcion.toLowerCase()}">
                    <a class="nav-link ${active}"
                       id="tab-link-${color.id}"
                       data-toggle="tab"
                       href="#tab-pane-${color.id}"
                       role="tab"
                       style="white-space:nowrap;"
                       onclick="onTabClick(${color.id})">
                        ${swatch}${color.descripcion}
                    </a>
                </li>`;

            contentEl.innerHTML += `
                <div class="tab-pane fade ${active}"
                     id="tab-pane-${color.id}"
                     role="tabpanel">

                    {{-- Grid imágenes existentes --}}
                    <div id="img-grid-${color.id}"
                         class="d-flex flex-wrap mb-3"
                         style="gap:12px;min-height:100px;align-items:flex-start;">
                        <div class="text-muted small w-100 text-center py-4">
                            <i class="fas fa-spinner fa-spin"></i> Cargando...
                        </div>
                    </div>

                    <hr class="my-2">

                    {{-- Información de restricciones --}}
                    <div class="alert alert-light border mb-2 py-2 px-3" style="font-size:0.82rem;">
                        <i class="fas fa-info-circle text-primary mr-1"></i>
                        <strong>Formatos permitidos:</strong> JPG, JPEG, WEBP &nbsp;|&nbsp;
                        <strong>Peso máximo:</strong> 2 MB por imagen &nbsp;|&nbsp;
                        <strong>Máximo:</strong> 5 imágenes por color
                        <br>
                        <span class="text-muted">
                            <i class="fas fa-lightbulb text-warning mr-1"></i>
                            Usa WEBP para mejor calidad con menor peso. Los PNG no están permitidos.
                        </span>
                    </div>

                    {{-- FilePond --}}
                    <input type="file"
                           id="fp-input-${color.id}"
                           accept="image/jpeg,image/webp"
                           multiple>

                    <button class="btn btn-primary btn-sm mt-2"
                            onclick="uploadImages(${color.id})">
                        <i class="fas fa-upload mr-1"></i> Guardar imágenes seleccionadas
                    </button>

                    <span id="fp-count-msg-${color.id}" class="ml-3 text-muted" style="font-size:0.8rem;"></span>
                </div>`;
        });

        // Inicializar FilePond del primer tab
        initPond(colores[0].id);
    }

    /* ── Buscar / filtrar colores ─────────────────────────────────── */
    function filtrarColores() {
        const q     = document.getElementById('mdl_imagenes_search').value.toLowerCase().trim();
        const items = document.querySelectorAll('#mdl_imagenes_tabs .nav-item');
        let visible = 0;
        let firstVisible = null;

        items.forEach(item => {
            const nombre  = item.dataset.colorName || '';
            const matches = !q || nombre.includes(q);
            item.style.display = matches ? '' : 'none';
            if (matches) {
                visible++;
                if (!firstVisible) firstVisible = item.querySelector('.nav-link');
            }
        });

        // Si el tab activo quedó oculto, activar el primero visible
        const active = document.querySelector('#mdl_imagenes_tabs .nav-link.active');
        if (active && active.closest('.nav-item').style.display === 'none' && firstVisible) {
            firstVisible.click();
        }

        actualizarContador(q, visible);
    }

    function actualizarContador(q, visible) {
        const span = document.getElementById('mdl_imagenes_search_count');
        if (!span) return;
        if (!q) {
            span.textContent = mdlImagenes.totalColores + ' color(es)';
        } else {
            span.textContent = visible + ' de ' + mdlImagenes.totalColores + ' color(es)';
        }
    }

    /* ── Click en tab ─────────────────────────────────────────────── */
    async function onTabClick(colorId) {
        if (!mdlImagenes.initialized[colorId]) {
            await loadImages(colorId);
        }
        if (!mdlImagenes.ponds[colorId]) {
            initPond(colorId);
        }
    }

    /* ── Inicializar FilePond ─────────────────────────────────────── */
    function initPond(colorId) {
        const input = document.getElementById(`fp-input-${colorId}`);
        if (!input || mdlImagenes.ponds[colorId]) return;

        mdlImagenes.ponds[colorId] = FilePond.create(input, {
            allowMultiple: true,
            maxFiles: 5,
            storeAsFile: true,
            allowImagePreview: true,
            imagePreviewHeight: 100,

            allowFileTypeValidation: true,
            acceptedFileTypes: ['image/jpeg', 'image/webp'],
            labelFileTypeNotAllowed: 'Solo se permiten imágenes JPG, JPEG o WEBP',
            fileValidateTypeLabelExpectedTypes: 'Formatos válidos: JPG, JPEG, WEBP',

            allowFileSizeValidation: true,
            maxFileSize: '2MB',
            labelMaxFileSizeExceeded: 'El archivo supera el límite de 2 MB',
            labelMaxFileSize: 'Tamaño máximo: 2 MB',

            labelIdle: 'Arrastra imágenes aquí o <span class="filepond--label-action">Buscar</span>',
        });
    }

    /* ── Cargar imágenes existentes ───────────────────────────────── */
    async function loadImages(colorId) {
        const grid = document.getElementById(`img-grid-${colorId}`);
        grid.innerHTML = `<div class="text-muted small w-100 text-center py-4">
                            <i class="fas fa-spinner fa-spin"></i> Cargando imágenes...
                          </div>`;
        mostrarAnimacion();
        try {
            const res = await axios.get(
                route('almacenes.producto.imagenes.index', {
                    productoId: mdlImagenes.productoId,
                    colorId,
                })
            );

            if (res.data.success) {
                mdlImagenes.initialized[colorId] = true;
                renderImageGrid(colorId, res.data.data);
            } else {
                grid.innerHTML = `<p class="text-danger small">Error al cargar imágenes.</p>`;
            }
        } catch (e) {
            grid.innerHTML = `<p class="text-danger small">Error al cargar imágenes.</p>`;
        } finally {
            ocultarAnimacion();
        }
    }

    /* ── Renderizar grid de imágenes ──────────────────────────────── */
    function renderImageGrid(colorId, imagenes) {
        const grid = document.getElementById(`img-grid-${colorId}`);
        const msg  = document.getElementById(`fp-count-msg-${colorId}`);

        const remaining = 5 - imagenes.length;

        if (msg) {
            msg.textContent = imagenes.length > 0
                ? `${imagenes.length}/5 imagen(es) — ${remaining > 0 ? remaining + ' disponible(s)' : 'límite alcanzado'}`
                : '';
        }

        if (mdlImagenes.ponds[colorId]) {
            mdlImagenes.ponds[colorId].setOptions({ maxFiles: remaining > 0 ? remaining : 1 });
        }

        if (!imagenes.length) {
            grid.innerHTML = `
                <div class="text-center text-muted w-100 py-4" style="font-size:0.85rem;">
                    <i class="fas fa-image fa-2x mb-2 d-block" style="opacity:.25;"></i>
                    Sin imágenes aún. Agrega hasta 5 por este color.
                </div>`;
            return;
        }

        grid.innerHTML = imagenes.map(img => `
            <div class="card shadow-sm" id="img-card-${img.id}"
                 style="width:140px;border-radius:8px;overflow:hidden;">
                <img src="${img.url}"
                     alt="${img.img_name}"
                     style="width:100%;height:115px;object-fit:cover;display:block;">
                <div class="card-body p-1" style="font-size:0.7rem;">
                    <span class="badge badge-secondary mr-1">#${img.orden}</span>
                    <span class="text-truncate d-inline-block" style="max-width:90px;vertical-align:middle;"
                          title="${img.img_name}">${img.img_name}</span>
                </div>
                <button onclick="eliminarImagen(${img.id}, ${colorId})"
                        class="btn btn-danger btn-block btn-sm rounded-0"
                        style="font-size:0.72rem;padding:3px;">
                    <i class="fas fa-trash mr-1"></i> Eliminar
                </button>
            </div>`
        ).join('');
    }

    /* ── Subir imágenes ───────────────────────────────────────────── */
    async function uploadImages(colorId) {
        const pond = mdlImagenes.ponds[colorId];
        if (!pond) return;

        const files = pond.getFiles();
        if (!files.length) {
            toastr.warning('Selecciona al menos una imagen antes de guardar.', 'AVISO');
            return;
        }

        mostrarAnimacion();
        let uploaded = 0, errors = 0;

        for (const f of files) {
            try {
                const fd = new FormData();
                fd.append('imagen', f.file);

                const res = await axios.post(
                    route('almacenes.producto.imagenes.store', {
                        productoId: mdlImagenes.productoId,
                        colorId,
                    }),
                    fd
                );

                if (res.data.success) {
                    uploaded++;
                } else {
                    errors++;
                    toastr.error(res.data.message, 'ERROR');
                }
            } catch (e) {
                errors++;
                const msg = e.response?.data?.message
                    || (e.response?.data?.errors?.imagen?.[0])
                    || e.message;
                toastr.error(msg, 'ERROR AL SUBIR');
            }
        }

        ocultarAnimacion();

        if (uploaded > 0) {
            toastr.success(`${uploaded} imagen(es) guardada(s) correctamente.`, 'LISTO');
            pond.removeFiles();
            await loadImages(colorId);
            dtProductos.ajax.reload(null, false);
        }
        if (errors > 0) {
            toastr.warning(`${errors} imagen(es) no se pudieron guardar.`, 'ATENCIÓN');
        }
    }

    /* ── Eliminar imagen ──────────────────────────────────────────── */
    async function eliminarImagen(imagenId, colorId) {
        const result = await Swal.fire({
            title: '¿Eliminar imagen?',
            text:  'Esta acción no se puede deshacer.',
            icon:  'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText:  'Cancelar',
            confirmButtonColor: '#d33',
        });

        if (!result.isConfirmed) return;

        mostrarAnimacion();
        try {
            const res = await axios.delete(
                route('almacenes.producto.imagenes.destroy', {
                    productoId: mdlImagenes.productoId,
                    colorId,
                    id: imagenId,
                })
            );

            if (res.data.success) {
                toastr.success(res.data.message, 'LISTO');
                await loadImages(colorId);
                dtProductos.ajax.reload(null, false);
            } else {
                toastr.error(res.data.message, 'ERROR');
            }
        } catch (e) {
            toastr.error(e.message, 'ERROR');
        } finally {
            ocultarAnimacion();
        }
    }

    /* ── Limpiar al cerrar ────────────────────────────────────────── */
    $('#mdl_imagenes_producto').on('hidden.bs.modal', function () {
        Object.values(mdlImagenes.ponds).forEach(p => {
            try { p.destroy(); } catch (_) {}
        });
        mdlImagenes.ponds        = {};
        mdlImagenes.initialized  = {};
        mdlImagenes.productoId   = null;
        mdlImagenes.totalColores = 0;
    });
</script>
