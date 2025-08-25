export interface LoginPayload {
    email: string;
    password: string;
}
export interface RegisterPayload {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

export interface User {
    name: string;
}

export interface AuthState {
    isLoggedIn: boolean;
    token: string | null;
    email: string;
    password: string;
    user: User | null;
    errors: {
        email: string;
        password: string;
        name?: string;
    };
}
