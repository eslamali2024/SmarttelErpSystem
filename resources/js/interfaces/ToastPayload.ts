export interface ToastPayload {
    title: string;
    type?: 'success' | 'error' | 'info' | 'warning';
    duration?: number;
}
