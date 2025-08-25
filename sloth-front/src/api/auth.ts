import axios from "axios";
import {LoginPayload, RegisterPayload} from "@/types/auth";

export async function registerUser(payload: RegisterPayload) {
    return axios.post('/api/auth/register', payload);
}
export async function loginUser(payload: LoginPayload) {
    return axios.post('/api/auth/login', payload);
}