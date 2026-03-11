<form action="" id="formRegistrarCliente" method="post">
    @csrf
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-toggle="pill" data-target="#pills-home"
                type="button" role="tab" aria-controls="pills-home" aria-selected="true">General</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile"
                type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Datos del
                Negocio</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-contact-tab" data-toggle="pill" data-target="#pills-contact"
                type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Datos del
                Propietario</button>
        </li>
    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            @include('ventas.clientes.tabs.tab1')
        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
            @include('ventas.clientes.tabs.tab2')
        </div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
            @include('ventas.clientes.tabs.tab3')
        </div>
    </div>
</form>
