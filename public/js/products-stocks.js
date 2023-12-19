const checkColores= document.querySelectorAll('.color');
const divsColorTallas= document.querySelectorAll('.color-tallas');

//solo marcar un color a la vez
document.addEventListener('DOMContentLoaded',()=>{
    events();
})
function events(){
    document.addEventListener('click',(e)=>{
        if(e.target.classList.contains('color')){
            const idColorCheckSelected= e.target.getAttribute('id');
            if(e.target.checked){
                clearChecksColores(idColorCheckSelected);
                showColorTallas(idColorCheckSelected);
            }else{
                hiddenDivColorTallas(0);
            }
            
        }
    })
}
const clearChecksColores=(idColorCheckSelected)=>{
    checkColores.forEach((cc)=>{
        if(cc.getAttribute('id') != idColorCheckSelected){
            cc.checked=false;
        }
    })
}
const showColorTallas=(idColorCheckSelected)=>{
    const idDivColorTallas=`#color_tallas_${idColorCheckSelected}`;
    hiddenDivColorTallas(idDivColorTallas);
    const divColorTallas= document.querySelector(idDivColorTallas);
    divColorTallas.hidden=false;
}
const hiddenDivColorTallas=(idDivColorTallas)=>{
    divsColorTallas.forEach((dct)=>{
        if(dct.getAttribute('id') != idDivColorTallas){
            dct.hidden = true;
        }
    })
}