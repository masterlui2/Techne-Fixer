import axios from 'axios'
import router from '@/router';

const instance = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
    headers:{
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
})

instance.interceptors.request.use((config) => {
    const token = localStorage.getItem('token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

instance.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token');
      router.push('/auth/login');
    }
    return Promise.reject(error);
  }
);

export default instance
export const baseURL = import.meta.env.VITE_APP_URL || 'http://localhost:8000';