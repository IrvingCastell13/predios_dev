
export function can(permission){

   // Obtener los datos guardados en localStorage
   const dataGuardada = localStorage.getItem('funciones');


   if (!dataGuardada) {
     return false; // Si no hay datos en localStorage, retornar false
   }

   // Parsear los datos de localStorage a un objeto JSON
   const funciones = JSON.parse(dataGuardada);


   // Verificar si el permiso existe dentro del array de funciones
   const permisoEncontrado = funciones.find(funcion => funcion.permiso.name === permission);

   // Retornar true si se encuentra el permiso, de lo contrario false
   return permisoEncontrado ? true : false;
}


export function cerrarModal (idModal) {
    const modalElement = document.getElementById(idModal);
    const modalInstance = bootstrap.Modal.getInstance(modalElement);

    if (modalInstance) {
        modalInstance.hide();
    } else {
        // Si no hay instancia, la crea y luego cierra el modal
        const modal = new bootstrap.Modal(modalElement);
        modal.hide();
    }
}
export function abrirModal (idModal) {

    const modal = new bootstrap.Modal(document.getElementById(idModal));
    modal.show();
}


export function ucwords(texto) {
    return texto.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
  }
