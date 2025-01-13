<form action="" id="formRegistrarVehiculo" method="post">    
    <div class="row">
        @csrf      
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="placa" class="required_field mb-2" style="font-weight: bold;">Placa</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-list-ol"></i>                   
                </span>
                <input required id="placa" minlength="6" maxlength="8"  name="placa" type="text" class="form-control" placeholder="N° PLACA" aria-label="Username" aria-describedby="basic-addon1">
             </div>   
            <span style="color:rgb(0, 89, 255);display:block; font-style: italic;">(6 - 8 CARACTERES ALFANUMÉRICOS)</span>                                
            <span class="placa_error msgError"  style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="modelo" class="required_field mb-2" style="font-weight: bold;">Modelo</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-th-large"></i>                   
                </span>
                <input required id="modelo" maxlength="100"  name="modelo" type="text" class="form-control" placeholder="Modelo" aria-label="Username" aria-describedby="basic-addon1">
             </div>  
             <span style="color:rgb(0, 89, 255);display:block; font-style: italic;">(100 CARACTERES)</span>                                                
            <span class="modelo_error msgError"  style="color:red;"></span>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 pb-2">
            <label for="marca" class="required_field mb-2" style="font-weight: bold;">Marca</label>
            <div class="input-group">
                <span class="input-group-text" id="basic-addon1">
                    <i class="fas fa-th-large"></i>                   
                </span>
                <input required id="marca" maxlength="100"  name="marca" type="text" class="form-control" placeholder="Marca" aria-label="Username" aria-describedby="basic-addon1">
             </div>     
             <span style="color:rgb(0, 89, 255);display:block; font-style: italic;">(100 CARACTERES)</span>                                                             
            <span class="marca_error msgError"  style="color:red;"></span>
        </div> 
    </div>
</form> 