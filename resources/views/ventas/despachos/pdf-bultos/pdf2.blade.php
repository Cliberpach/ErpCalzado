<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento PDF</title>
    <link rel="stylesheet" href="styles.css">

    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

.container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
    padding: 20px;
}

.row {
    margin-bottom: 20px;
}

.logo img {
    max-width: 200px;
}

.company-name, .phone-number {
    font-size: 24px;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="logo">
                @if($empresa->ruta_logo)
                    <img src="{{ base_path() . '/storage/app/'.$empresa->ruta_logo }}" class="img-fluid" style="width:50%;">
                @else
                    <img src="{{ public_path() . '/img/default.png' }}" class="img-fluid">
                @endif
            </div>
        </div>
        <div class="row">
            <div class="company-name">
                {{$empresa->razon_social_abreviada}}
            </div>
        </div>
        <div class="row">
            <div class="phone-number">
                Número de Celular
            </div>
        </div>
    </div>
</body>
</html>
