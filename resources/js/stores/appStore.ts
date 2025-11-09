import axios from 'axios'
import { defineStore } from 'pinia'
import { AuthPermissions, Module } from '@/types'

export const useAppStore = defineStore('app', {
    state: () => ({
        permissions: [] as AuthPermissions[],
        modules: [] as Module[],
        loaded: false
    }),
    actions: {
        async load() {
            if (this.loaded) return
            try {
                const { data } = await axios.get('/api/app-data')
                if (data) {
                    this.permissions = data.auth_permissions
                    this.modules = data.modules
                }
                this.loaded = true
            } catch (error) {
                console.error('Failed to load app data', error)
            }
        }
    }
})
