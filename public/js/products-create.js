const checkColores= document.querySelectorAll('.color');
const divsColorTallas= document.querySelectorAll('.color-tallas');

const formCrearCategoria        =   document.querySelector('#crear_categoria');
const formCrearMarca            =   document.querySelector('#crear_marca');
const formCrearModelo           =   document.querySelector('#crear_modelo');
const formRegProducto           =   document.querySelector('#form_registrar_producto');


const tokenValue                = document.querySelector('input[name="_token"]').value;

const selectCategorias          =   document.querySelector('#categoria');
const selectMarcas              =   document.querySelector('#marca');
const selectModelos              =   document.querySelector('#modelo');

const inputStocksJSON           =   document.querySelector('#stocksJSON');

const tallas    =   document.querySelectorAll('.talla');

const coloresSeleccionados = [];
const span_color_activo         = document.querySelector('.color_activo');

//solo marcar un color a la vez
document.addEventListener('DOMContentLoaded',()=>{
    events();
    tallas.forEach((t)=>{
        console.log(t);
    })
})
function events(){

    //============ MOSTRAR INPUTS TALLAS PARA COLOR SELECCIONADO ========================
    document.addEventListener('click',(e)=>{
        if(e.target.classList.contains('color')){
            const idColorCheckSelected  = e.target.getAttribute('id');
            const color_nombre          =   e.target.getAttribute('data-color-nombre');

            if(e.target.checked){
                addColor(idColorCheckSelected);
                showColorTallas(idColorCheckSelected);
                pintarBotonEditar(e.target,idColorCheckSelected,color_nombre);
                span_color_activo.textContent   =   color_nombre;

            }else{
                hiddenDivColorTallas(0);
                removeColor(idColorCheckSelected);
                despintarBotonEditar(e.target);
                span_color_activo.textContent   =   'SELECCIONAR COLOR';
            }     
        }

        if(e.target.classList.contains('btn-editar-stocks')){
            const idColor   =   e.target.getAttribute('data-color-id');
            const color_nombre  =   e.target.getAttribute('data-color-nombre');
            showColorTallas(idColor);
            span_color_activo.textContent   =   color_nombre;
        }


    })


    //========== FORM REG PRODUCTO ==============
    formRegProducto.addEventListener('submit',(e)=>{
        e.preventDefault();
        Swal.fire({
            title: 'Opción Guardar',
            text: "¿Seguro que desea guardar cambios?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: "#1ab394",
            confirmButtonText: 'Si, Confirmar',
            cancelButtonText: "No, Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                cargarStocks();
                e.target.submit();
              
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire(
                    'Cancelado',
                    'La Solicitud se ha cancelado.',
                    'error'
                )
            }
        })
    })


    //============ FETCH CREAR CATEGORIA ==========================
    formCrearCategoria.addEventListener('submit',(e)=>{
        e.preventDefault();
        const url           =   '/almacenes/categorias/store';
        const formData      =   new FormData(e.target);
        formData.append('fetch', 'SI');
        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenValue,
                },
                body:   formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.message=='success'){
                    updateSelectCategorias(data.data);
                    $('#modal_crear_categoria').modal('hide');
                    toastr.success('Categoría creada.', 'Éxito');
                    formCrearCategoria.reset();
                }else if(data.message=='error'){
                    toastr.error('El campo descripción es obligatorio.', 'Error');
                }
            })
            .catch(error => console.error('Error:', error));
    })

    //=================== FETCH CREAR MARCA =================================
    formCrearMarca.addEventListener('submit',(e)=>{
        e.preventDefault();
        const url           =   '/almacenes/marcas/store';
        const formData      =   new FormData(e.target);
        formData.append('fetch', 'SI');
        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenValue,
                },
                body:   formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.message=='success'){
                    updateSelectMarcas(data.data);
                    $('#modal_crear_marca').modal('hide');
                    toastr.success('Marca creada.', 'Éxito');
                    formCrearCategoria.reset();
                }else if(data.message=='error'){
                    toastr.error(pintarErroresMarca(data.data.marca_guardar), 'Error');
                }
            })
            .catch(error => console.error('Error:', error));
    })

    //==================== FETCH CREAR MODELO ==========================
    formCrearModelo.addEventListener('submit',(e)=>{
        e.preventDefault();
        const url           =   '/almacenes/modelos/store';
        const formData      =   new FormData(e.target);
        formData.append('fetch', 'SI');
        fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenValue,
                },
                body:   formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.message=='success'){
                    updateSelectModelos(data.data);
                    $('#modal_crear_modelo').modal('hide');
                    toastr.success('Modelo creado.', 'Éxito');
                    formCrearModelo.reset();
                }else if(data.message=='error'){
                    toastr.error(pintarErroresMarca(data.data.descripcion_guardar), 'Error');
                }
            })
            .catch(error => console.error('Error:', error));
    })
}


//============= PINTAR BOTÓN EDITAR STOCKS ===========
const pintarBotonEditar = (checkColorSeleccionado,idColor,color_nombre)=>{
   const form_check = checkColorSeleccionado.parentElement;
   const iconoEditar = document.createElement('i');
   iconoEditar.className = 'fas fa-edit btn btn-primary btn-editar-stocks'; 
   iconoEditar.style.margin = '0 0 0 10px';
   iconoEditar.setAttribute('data-color-id', idColor);
   iconoEditar.setAttribute('data-color-nombre', color_nombre);


   form_check.appendChild(iconoEditar);
}

//========= DESPINTAR BOTÓN EDITAR STOCKS ============
const despintarBotonEditar = (checkColorSeleccionado)=>{
    const form_check = checkColorSeleccionado.parentElement;
    
   form_check? form_check.children[2].remove():unset;
 }

const addColor = (idColorSeleccionado)=>{
    const indexColor = coloresSeleccionados.findIndex((c)=>{ return c  == idColorSeleccionado  });
    if(indexColor === -1){
        coloresSeleccionados.push(idColorSeleccionado);
    }
    console.log(coloresSeleccionados);
}

const removeColor = (idColorSeleccionado)=>{
    const indexColor = coloresSeleccionados.findIndex((c)=>{ return c  == idColorSeleccionado  });
    if(indexColor !== -1){
        coloresSeleccionados.splice(indexColor,1);
    }
    console.log(coloresSeleccionados);

}

//=========== cargar stocks ===========
const cargarStocks = ()=>{
    const arrayStocks   =   [];
    coloresSeleccionados.forEach((c)=>{
        const color={};
        const tallas    =   document.querySelectorAll(`input[data-color-id="${c}"]`);
        let arrayTallas =   [];

        color.color_id  =   c;
        tallas.forEach((t)=>{
            let talla = {};
            if(t.value !== ''){
                const idColor   =   t.getAttribute('data-color-id');
                const idTalla   =   t.getAttribute('data-talla-id');
                talla.color_id  =   idColor;
                talla.talla_id  =   idTalla;
                talla.cantidad  =   t.value;

                arrayTallas.push(talla);
            }
        })

        color.tallas    =   arrayTallas;
        arrayStocks.push(color);
    })

    inputStocksJSON.value   =   JSON.stringify(arrayStocks);
}


//================ MOSTRAR INPUTS TALLAS PARA LLENAR STOCKS ================
const showColorTallas=(idColorCheckSelected)=>{
    const idDivColorTallas=`#color_tallas_${idColorCheckSelected}`;
    //==== ocultar divs tallas de otros colores diferentes al seleccionado =======
    hiddenDivColorTallas(idDivColorTallas);
    //===== mostrar tallas del color elegido ===============
    const divColorTallas= document.querySelector(idDivColorTallas);
    divColorTallas.hidden=false;
    //===== colocar la primera talla de dicho color en 0 solo sí está vacío =======
   //setFirstTalla(divColorTallas);
}


const setFirstTalla = (divColorTallas)=>{
    //====== si el color tiene tallas y la primera talla tiene su input =======
    if(divColorTallas.firstChild && divColorTallas.firstElementChild.children[1]){
        //======= si ese input está vacío ========
        if(!divColorTallas.firstElementChild.children[1].value){
            //======== rellenar con 0 =========
           divColorTallas.firstElementChild.children[1].value = 0;
        }
   }
}

//================= OCULTAR CONTENEDOR DE TALLAS =======================
const hiddenDivColorTallas=(idDivColorTallas)=>{
    divsColorTallas.forEach((dct)=>{
        if(dct.getAttribute('id') != idDivColorTallas){
            dct.hidden = true;
        }
    })
}

//limpiar el valor del primer input talla al desmarcar un color =======
function clearTallas(idColorCheckSelected){
    //========= seleccionando todas las tallas del color ==========
    const tallas = document.querySelectorAll(`input[id^="input_${idColorCheckSelected}_"]`);


    //====== si el color tiene tallas y la primera talla tiene su input =======
    tallas.forEach((i)=>{
        i.value = null;
    })
}

const pintarErroresMarca    =   (errores_marca)=>{
    let message = '';
    errores_marca.forEach((m, index) => {
        message += m;
        if (index < errores_marca.length - 1) {
            message += '\n';
        }
    });
    return message;
}

const pintarErroresModelo    =   (errores_modelo)=>{
    let message = '';
    errores_modelo.forEach((m, index) => {
        message += m;
        if (index < errores_modelo.length - 1) {
            message += '\n';
        }
    });
    return message;
}

const updateSelectCategorias    =   (categorias_actualizadas)=>{
    let items                  = '<option></option>';
    categorias_actualizadas.forEach((c)=>{
        items+=`<option value="${ c.id }" {{ (old('categoria') == ${c.id} ? "selected" : "") }} >${c.descripcion }</option>`;
    })
    selectCategorias.innerHTML  =   items;
}

const updateSelectMarcas    =   (marcas_actualizadas)=>{
    let items                  = '<option></option>';
    marcas_actualizadas.forEach((m)=>{
        items+=`<option value="${ m.id }" {{ (old('marca') == ${m.id} ? "selected" : "") }} >${m.marca }</option>`;
    })
    selectMarcas.innerHTML  =   items;
}

const updateSelectModelos    =   (modelos_actualizados)=>{
    let items                  = '<option></option>';
    modelos_actualizados.forEach((m)=>{
        items+=`<option value="${ m.id }" {{ (old('marca') == ${m.id} ? "selected" : "") }} >${m.descripcion }</option>`;
    })
    selectModelos.innerHTML  =   items;
}