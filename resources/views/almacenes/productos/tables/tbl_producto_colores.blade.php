<table class="table table-striped table-hover" id="table-colores" width="100%">
    <thead>
      <tr>
        <th scope="col" style="text-align: left;">#</th>
        <th scope="col">NOMBRE</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($colores as $color)
        <tr>
          <th style="text-align: left;" scope="row">{{$color->id}}</th>
     
          <td>
            <div class="form-check">
              <input class="form-check-input checkColor" type="checkbox" value="" id="checkColor_{{$color->id}}" data-color-id="{{$color->id}}">
              <label class="form-check-label" for="checkColor_{{$color->id}}">
                {{$color->descripcion}}
              </label>
            </div>
          </td>
          
        </tr>
      @endforeach
      
    </tbody>
</table>