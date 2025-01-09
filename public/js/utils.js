
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