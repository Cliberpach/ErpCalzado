@push('styles')
  <link rel="stylesheet" href="{{asset('css/spinner_lupa.css')}}">
  <link rel="stylesheet" href="{{asset('css/overlay_1.css')}}">
@endpush

<div class="overlay" id="overlay">
  <span class="loader"></span>
</div>


{{-- USAR ESTO PARA ACTIVAR O DESACTIVAR :     const overlay = document.getElementById('overlay'); --}}
{{-- overlay.style.display = 'flex'; 
overlay.style.display = 'none';  --}}
