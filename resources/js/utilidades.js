import { HTTP } from "./helpers";

$(document).ready(function () {
    $("#mes").select2({
        placeholder: "SELECCIONAR MES",
        allowClear: true,
        height: "200px",
        width: "100%",
    });

    $("#anio").select2({
        placeholder: "SELECCIONAR AÑO",
        allowClear: true,
        height: "200px",
        width: "100%",
    });
    obtenerDatos();

});

async function obtenerDatos() {
    
    let mes = $("#mes").val();
    let anio = $("#anio").val();
    let correcto = true;

    limpiar();

    if (mes == "") {
        toastr.error("Seleccionar mes.", "Error");
        correcto = false;
    }

    if (anio == "") {
        toastr.error("Seleccionar año.", "Error");
        correcto = false;
    }

    if (correcto) {
        $("#panel_detalle").children(".ibox-content").toggleClass("sk-loading");
        const { data } = await HTTP.get(
            route("consultas.utilidad.getDatos", { mes: mes, anio: anio })
        );

        let inversion_mensual_dolares = data.inversion_mensual_dolares;
        let inversion_mensual = data.inversion_mensual;
        let ventas_mensual_dolares = data.ventas_mensual_dolares;
        let ventas_mensual = data.ventas_mensual;
        let utilidad_mensual_dolares = data.utilidad_mensual_dolares;
        let utilidad_mensual = data.utilidad_mensual;

        $("#inversion_dolar").text(
            FormatoMoneda(inversion_mensual_dolares, "en-US", "USD")
        );
        $("#inversion_soles").text(
            FormatoMoneda(inversion_mensual, "es-PE", "PEN")
        );
        $("#ventas_dolar").text(
            FormatoMoneda(ventas_mensual_dolares, "en-US", "USD")
        );
        $("#ventas_soles").text(FormatoMoneda(ventas_mensual, "es-PE", "PEN"));
        $("#utilidad_dolar").text(
            FormatoMoneda(utilidad_mensual_dolares, "en-US", "USD")
        );
        $("#utilidad_soles").text(
            FormatoMoneda(utilidad_mensual, "es-PE", "PEN")
        );
        $("#porcentaje_dolar").text(data.porcentaje.toFixed(2) + "%");
        $("#porcentaje_soles").text(data.porcentaje.toFixed(2) + "%");

        $("#panel_detalle").children(".ibox-content").toggleClass("sk-loading");
    }
}

function limpiar() {
    
    $("#inversion_dolar").text("0.00");
    $("#inversion_soles").text("0.00");
    $("#ventas_dolar").text("0.00");
    $("#ventas_soles").text("0.00");
    $("#utilidad_dolar").text("0.00");
    $("#utilidad_soles").text("0.00");
    $("#porcentaje_dolar").text("0.00%");
    $("#porcentaje_soles").text("0.00%");
}
function FormatoMoneda(monto, pais, formato) {
    let res = new Intl.NumberFormat(pais, {
        style: "currency",
        currency: formato,
    }).format(monto);
    return res;
}

$(document).on("change","#mes",limpiar);
$(document).on("change","#anio",limpiar);
$(document).on("click","#obtener-datos",obtenerDatos);