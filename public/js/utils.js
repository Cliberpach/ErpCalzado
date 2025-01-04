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