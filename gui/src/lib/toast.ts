import { writable } from 'svelte/store';

const { subscribe, update, set } = writable([]);

function remove(toast) {
    update((toasts) =>
        toasts.filter((item) => {
            return item !== toast;
        }),
    );
}

function add(toast, timeout) {
    update((toasts) => [toast, ...toasts]);

    if (!(toast.kind === 'error') || timeout) {
        setTimeout(() => {
            remove(toast);
        }, timeout || 3000);
    }
}

function reset() {
    set([]);
}

export default { subscribe, add, reset, remove };
