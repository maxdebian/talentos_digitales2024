import axios from 'axios';

const axiosInstance = axios.create({
  baseURL: 'http://localhost:9000/', 
  // Aquí podemos agregar configuraciones como headers por defecto para JWT
});

export default axiosInstance;