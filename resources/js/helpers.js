import axios from "axios";
export function FormatoMoneda(number,locale="es-PE"){
    let formato = new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: "PEN",
        minimumFractionDigits: 2
    }).format(Number(number));
    return formato;
}
export function RedondearDecimales(number){
    let x =Number(number).toFixed(2);
    return Number(x);
}

export const AJAXHTTP = ()=>{
    return axios.create({
        baseURL:location.origin,
        headers:{
            "Content-type":"application/json"
        }
    })
}

export const HTTP= AJAXHTTP();