import './assets/main.css'
import { createApp } from 'vue'
import App from './App.vue'
import router from "@/router/router";
import { createPinia } from "pinia";
import axios from "axios";
import { useAuthStore } from "@/stores/authStore";

axios.defaults.baseURL = import.meta.env.VITE_API_URL;

const GUEST_TOKEN_KEY = 'guest_token';
function getOrCreateGuestToken(): string {
    let token = localStorage.getItem(GUEST_TOKEN_KEY);
    if (!token) {
        token = (window.crypto?.randomUUID?.() || `${Date.now()}-${Math.random()}`).toString();
        localStorage.setItem(GUEST_TOKEN_KEY, token);
    }
    return token;
}

axios.interceptors.request.use((config) => {
    const guestToken = getOrCreateGuestToken();
    config.headers = config.headers || {};
    (config.headers as any)['X-Guest-Token'] = guestToken;
    return config;
});

const app = createApp(App);
const pinia = createPinia();

app.use(pinia);

const auth = useAuthStore();
auth.initFromStorage();

app.use(router);
app.mount('#app');
