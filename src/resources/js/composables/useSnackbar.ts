import { inject, InjectionKey } from 'vue';

export type SnackbarLevel = 'success' | 'info' | 'warning' | 'error';

export interface SnackbarApi {
    showSnackbar: (level: SnackbarLevel, message: string) => void;
}

export const snackbarKey: InjectionKey<SnackbarApi> = Symbol('SnackbarApi');

export function useSnackbar() {
    const snackbar = inject(snackbarKey);
    if (!snackbar) {
        throw new Error('`useSnackbar` must be used after `provideSnackbar()` in parent scope.');
    }
    return snackbar;
}
