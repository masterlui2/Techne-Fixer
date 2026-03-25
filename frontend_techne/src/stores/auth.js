import { defineStore } from "pinia";
import axios from "@/api/axios";

export const useAuthStore = defineStore('auth',{
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
    },

    actions:{
        async register(form){
            const {data} = await axios.post('/auth/register', form)
            this.token = data.data.access_token
            this.user = data.data.user
            localStorage.setItem('token', this.token)
            localStorage.setItem('user', JSON.stringify(this.user))
        },

        async login(form){
            const {data} = await axios.post('/auth/login', form)
            this.token = data.data.access_token
            this.user = data.data.user
            localStorage.setItem('token', this.token)
            localStorage.setItem('user', JSON.stringify(this.user))
        },

        async logout(){
            await axios.post('/auth/logout')
            this.token = null
            this.user = null
            localStorage.removeItem('token')
            localStorage.removeItem('user')
        },

        async fetchUser(){
            const {data} = await axios.get('/auth/user')
            this.user = data.user
        }
    }
})