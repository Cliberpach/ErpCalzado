@push('styles')
<style>
.loader {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: inline-block;
  border-top: 4px solid #FFF;
  border-right: 4px solid transparent;
  box-sizing: border-box;
  animation: rotation 1s linear infinite;
}
.loader::after {
  content: '';  
  box-sizing: border-box;
  position: absolute;
  left: 0;
  top: 0;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border-left: 4px solid #FF3D00;
  border-bottom: 4px solid transparent;
  animation: rotation 0.5s linear infinite reverse;
}
@keyframes rotation {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
} 

.overlay_esfera_1 {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8); 
    display: flex;
    justify-content: center; 
    align-items: center; 
    z-index: 9999; 
    display: none;
}

.loader_estilo {
  border: solid black 1px;
  padding: 100px; 
  background-color: rgb(6, 12, 37); 
  border-radius: 2%; 
}

</style> 
@endpush

<div class="overlay_esfera_1" id="overlay_esfera_1">
  <div class="loader_estilo">
    <span class="loader"></span>
  </div>
</div>