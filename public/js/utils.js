
document.addEventListener('DOMContentLoaded',()=>{
    eventsUtils();
})

function eventsUtils(){

    document.addEventListener('input',(e)=>{
        if(e.target.classList.contains('inputDecimalPositivo')){

            const input = e.target;

            // Reemplaza cualquier carácter que no sea un dígito o un punto decimal
            let value = input.value.replace(/[^0-9.]/g, '');

            // Asegúrate de que el punto decimal no esté al inicio
            if (value.startsWith('.')) {
                value = value.slice(1);
            }

            // Permite solo un punto decimal y limita a dos decimales
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            if (parts.length === 2) {
                parts[1] = parts[1].slice(0, 2); // Limita a dos decimales
                value = parts.join('.');
            }

            // Actualiza el valor del input
            input.value = value;
        }

        if (e.target.classList.contains('inputDecimalPositivoLibre')) {
            const input = e.target;

            // Reemplaza cualquier carácter que no sea un dígito o un punto decimal
            let value = input.value.replace(/[^0-9.]/g, '');

            // Asegúrate de que el punto decimal no esté al inicio
            if (value.startsWith('.')) {
                value = value.slice(1);
            }

            // Permite solo un punto decimal
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Actualiza el valor del input
            input.value = value;
        }

        if (e.target.classList.contains('inputDecimal')) {
            const input = e.target;

            // Reemplaza cualquier carácter que no sea un dígito, un punto decimal o un signo negativo al inicio
            let value = input.value.replace(/[^0-9.-]/g, '');

            // Asegúrate de que el signo negativo esté al inicio si existe
            if (value.includes('-')) {
                value = '-' + value.replace(/-/g, ''); // Mueve el signo negativo al inicio y remueve los demás
            }

            // Asegúrate de que el punto decimal no esté al inicio, a menos que sea después del signo negativo
            if (value.startsWith('.') || value.startsWith('-.')) {
                value = value.slice(1);
            }

            // Permite solo un punto decimal y limita a dos decimales
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            if (parts.length === 2) {
                parts[1] = parts[1].slice(0, 2); // Limita a dos decimales
                value = parts.join('.');
            }

            // Actualiza el valor del input
            input.value = value;
        }

        if (e.target.classList.contains('inputEnteroPositivo')) {
            const input = e.target;

            // Reemplaza cualquier carácter que no sea un dígito
            let value = input.value.replace(/[^0-9]/g, '');

            // Asegúrate de que no empiece con ceros
            if (value.startsWith('0')) {
                value = value.replace(/^0+/, ''); // Elimina los ceros iniciales
            }

            // Si el campo se vacía por completo, mantenerlo vacío
            if (value === '') {
                value = '';
            }

            // Actualiza el valor del input
            input.value = value;
        }

    })

}


//======= LIMPIAR ERRORES DE VALIDACIÓN ========
function limpiarErroresValidacion(error_class){
    const lstTagErrors    =   document.querySelectorAll(`.${error_class}`);
    lstTagErrors.forEach((tag)=>{
        tag.textContent    =   '';
    })
}


function pintarErroresValidacion(objValidationErrors,suffix){

    for (let clave in objValidationErrors) {
        const pError        =   document.querySelector(`.${clave}_${suffix}`);
        pError.textContent  =   objValidationErrors[clave][0];
    }

}

//========= LIMPIAR UNA TABLA =======
function limpiarTabla(idTabla) {
    const tbody =   document.querySelector(`#${idTabla} tbody`);
    while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild);
    }
}

function destruirDataTable(dtTable){
    if(dtTable){
        dtTable.destroy();
        dtTable =   null;
    }
}

function iniciarDataTable(idTabla) {
    dtGenerico = new DataTable(`#${idTabla}`, {
        language: {
            processing:     "Procesando...",
            search:         "Buscar: ",
            lengthMenu:     "Mostrar _MENU_ elementos",
            info:           "Mostrando _START_ a _END_ de _TOTAL_ elementos",
            infoEmpty:      "Mostrando 0 elementos",
            infoFiltered:   "(filtrado de _MAX_ elementos)",
            infoPostFix:    "",
            loadingRecords: "Cargando...",
            zeroRecords:    "No se encontraron registros",
            emptyTable:     "No hay datos disponibles",
            paginate: {
                first:      "Primero",
                previous:   "Anterior",
                next:       "Siguiente",
                last:       "Último"
            },
            aria: {
                sortAscending:  ": activar para ordenar la columna de manera ascendente",
                sortDescending: ": activar para ordenar la columna de manera descendente"
            }
        }
    });

    return dtGenerico;
}

//=========== OBTENER FILA POR EL ID DE UN DATATABLE =========
function getRowById(dtTabla,registro_id) {
    let data    = dtTabla.rows().data();
    console.log("Data:", data); // Para depuración
    let rowData = null;

    for (let i = 0; i < data.length; i++) {
        console.log("Registro ID:", registro_id, "Data ID:", data[i].id); // Para depuración
        if (data[i].id == registro_id) {
            rowData = data[i];
            break;
        }
    }

    return rowData;
}

//======== OBTENER FILA POR EL INDEX DEL DATATABLE ========
function getRowByIndex(dtTabla, index) {

    if (index < 0 || index >= dtTabla.rows().count()) {
        return null;
    }

    let data = dtTabla.rows().data();

    return data[index];
}

function formatoMoneda(valor) {
    return parseFloat(valor).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
