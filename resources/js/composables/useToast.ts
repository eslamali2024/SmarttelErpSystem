import { toast } from 'vue-sonner'
import { ToastPayload } from '@/interfaces/ToastPayload'

export const useToast = () => {
    const showToast = (payload: ToastPayload) => {
        const type = payload.type || 'success'
        const duration = payload.duration || 3000

        toast[type](payload.title, {
            duration,
            closeButton: true,
        })
    }

    // Usage Helpers
    const success = (title: string, duration?: number) => {
        showToast({ title, type: 'success', duration })
    }

    const error = (title: string, duration?: number) => {
        showToast({ title, type: 'error', duration })
    }

    const info = (title: string, duration?: number) => {
        showToast({ title, type: 'info', duration })
    }

    const warning = (title: string, duration?: number) => {
        showToast({ title, type: 'warning', duration })
    }

    return {
        showToast,
        success,
        error,
        info,
        warning
    }
}
