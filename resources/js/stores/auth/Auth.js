// src/stores/auth.js
import { defineStore } from 'pinia';
import axios from 'axios';
import { ref } from 'vue';
import { computed } from 'vue';
import eventBus from '../../eventBus';


export const useAuthStore = defineStore('auth', () => {


  const IDPredioGlobal = computed(() => eventBus.state.IDPredioGlobal);

  const user = ref(JSON.parse(localStorage.getItem('user')) || null);
  const token = ref(localStorage.getItem('token') || null); // Obtener el token desde localStorage para mantener la sesión persistente
  const IDCliente = ref(null)

  const login = async (credentials) => {


    try {

      const { data: { data } } = await axios.post(route('api.loginUser'), credentials);

      const newToken = data.token;
      token.value = newToken;
      user.value = data.user;
      // Guardar token en localStorage
      localStorage.setItem('token', newToken);

      localStorage.setItem('user', JSON.stringify(data.user));
      IDCliente.value = data.user.IDCliente
      // Configurar el token para que se envíe en todas las peticiones de axios

    // const idPredio = "7b8c1849-715e-11ef-9b4f-00090ffe0001";  // Cambia por el valor que necesitas
    //   localStorage.setItem('IDPredio', IDPredioGlobal.value);

    setTimeout(() => {

        if(data.user.IDRol !== 1){

            getPredios()
        }
    }, 0);

      axios.defaults.headers.common['Authorization'] = `Bearer ${newToken}`;

      setTimeout(() => {
         const redirectUrl = localStorage.getItem('redirectAfterLogin');
        if (redirectUrl) {
            localStorage.removeItem('redirectAfterLogin');
            window.location.href = redirectUrl;
        } else {
            // Redirección por defecto al dashboard
            window.location.href = data.redirect;
        }

    }, 10);


    } catch (error) {
        const mensajeError = error?.response?.data?.data?.error || 'Error inesperado';
        const captcha = error?.response?.data?.data?.captcha;
        throw {
                mensajeError,
                captcha
            };
    }
  };

  const logout = () => {
    token.value = null;
    user.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('redirectAfterLogin'); // Importante limpiar esto
    delete axios.defaults.headers.common['Authorization'];
    window.location.href = '/';
};

  const getPredios = async () => {
    try {

        const { data: { data } } = await axios.get(route('obtenerPrediosRolesLayout'));

        if(data.length > 0){
            // eventBus.methods.setIDPredioGlobal(data[0].IDPredio);
        }
    } catch (error) {
        console.error('Error al obtener los predios:', error);
    }
}

  const checkAuth = async () => {

    const token = localStorage.getItem('token') ?? null;
    const currentPath = window.location.pathname;
    // Lista de patrones de URL que requieren autenticación
    // Puedes agregar nuevos módulos fácilmente a esta lista
    const authenticatedRoutePatterns = [
        '/admin/ticket',
        '/admin/OrdenesTrabajo',
        // Agrega más rutas de módulos protegidos aquí cuando los crees
    ];

    // Verificar si la URL actual coincide con algún patrón protegido
    const requiresAuth = authenticatedRoutePatterns.some(pattern =>
        currentPath.startsWith(pattern)
    );

    // Si requiere autenticación y no hay token, guardar la URL para redirigir después
    if (requiresAuth && !token) {
        localStorage.setItem('redirectAfterLogin', window.location.href);
    }

    if (token) {
        axios.defaults.withCredentials = true;
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        try {
          const { data : { data} } = await axios.get(route('api.get.user'));
          user.value = data;

          localStorage.setItem('user', JSON.stringify( user.value));

        // Verificar si hay una URL guardada para redirección después del login
            const redirectUrl = localStorage.getItem('redirectAfterLogin');

            if (redirectUrl) {
                // Limpiar la URL guardada para evitar redirecciones futuras no deseadas
                localStorage.removeItem('redirectAfterLogin');
                // Redirigir al usuario a la URL que quería ver
                window.location.href = redirectUrl;
            }

        } catch (error) {

          console.log(error)
          logout(); // Si el token ya no es válido, limpiar el estado
        //   window.location.href = route('login');
        }
      }else{

      if (window.location.pathname !== '/') {
            window.location.href = '/';
        }
      }
  };


  return { user, token, login, logout, checkAuth };
});
