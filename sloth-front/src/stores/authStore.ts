import {defineStore} from 'pinia'
import {loginUser, registerUser} from "@/api/auth";
import type {AxiosError} from "axios";
import {useRouter} from 'vue-router';
import type {AuthState, LoginPayload, RegisterPayload} from "@/types/auth";
import { useCartStore } from '@/stores/cartStore';
import axios from 'axios';

const STORAGE_KEY = 'auth';

export const useAuthStore = defineStore('auth', {
    state: (): AuthState => ({
        isLoggedIn: false,
        token: null,
        email: '',
        password: '',
        user: null,
        errors: {
            email: '',
            password: '',
            name: '',
        },
    }),
    getters: {
        isAuthenticated: (state) => state.isLoggedIn,
        userName: (state) => state.user?.name || state.email || '',
    },
    actions: {
        initFromStorage() {
            try {
                const raw = localStorage.getItem(STORAGE_KEY);
                if (!raw) return;

                const parsed = JSON.parse(raw) as { token?: string; user?: any; email?: string };
                this.token = parsed?.token ?? null;
                this.user = parsed?.user ?? null;
                this.email = parsed?.email ?? '';
                this.isLoggedIn = !!this.token;

                if (this.token) {
                    axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                }
            } catch {
                this.token = null;
                this.user = null;
                this.email = '';
                this.isLoggedIn = false;
            }
        },

        persist() {
            try {
                localStorage.setItem(
                    STORAGE_KEY,
                    JSON.stringify({
                        token: this.token,
                        user: this.user,
                        email: this.email,
                    })
                );
            } catch {

            }
        },

        setAuth(token: string, user?: { name?: string }, email?: string) {
            this.token = token;
            this.isLoggedIn = true;
            if (email) this.email = email;
            if (user?.name) this.user = { name: user.name };

            axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
            this.persist();
        },

        async login(payload:LoginPayload) {
            const router = useRouter();
            this.errors = {
                email: '',
                password: '',
            };
            if (!payload.email) this.errors.email = 'Email is required';
            if (!payload.password) this.errors.password = 'Password is required';

            if (Object.values(this.errors).some(error => error)) {
                throw new Error('Please fill in all required fields.');
            }

            try {
                const response = await loginUser({
                    email: payload.email,
                    password: payload.password,
                });

                this.setAuth(response.data.token, response.data.user, payload.email);

                const cart = useCartStore();
                await cart.loadCart();

                await router.push({name: 'products'});
            } catch (error) {
                const err = error as AxiosError<any>;
                if (err.response?.data?.errors) {
                    this.errors = {...this.errors, ...err.response.data.errors};
                }
                throw (err.response?.data?.message
                    ? new Error(err.response.data.message)
                    : (err instanceof Error ? err : new Error('Login failed')));

            }
        },

        register: async function (payload: RegisterPayload) {
            const router = useRouter();
            this.errors = {
                email: '',
                password: '',
                name: '',
            };
            if (!payload.email) this.errors.email = 'Email is required';
            if (!payload.password) this.errors.password = 'Password is required';
            if (!payload.name) this.errors.name = 'Name is required';
            if (payload.password !== payload.password_confirmation) {
                this.errors.password = 'Passwords do not match';
            }

            if (Object.values(this.errors).some(error => error)) return;

            try {
                const response = await registerUser(payload);

                this.setAuth(response.data.token, { name: response.data.user?.name || payload.name }, payload.email);

                const cart = useCartStore();
                await cart.loadCart();

                await router.push({name: 'products'});
            } catch (error) {
                const err = error as AxiosError<any>;
                if (err.response?.data?.errors) {
                    this.errors = {...this.errors, ...err.response.data.errors};
                }
            }
        },

        logout() {
            const router = useRouter();
            this.token = null;
            this.isLoggedIn = false;
            this.email = '';
            this.password = '';
            this.user = null;

            delete axios.defaults.headers.common['Authorization'];

            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch {

            }

            router.push({name: 'login'});
        }
    },
})