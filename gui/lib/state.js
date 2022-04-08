import { writable, derived, get } from 'svelte/store';
import toast from './toast';

const pristine = writable(true);
const dirty = derived(pristine, ($pristine) => !$pristine);

function setDirty() {
    pristine.set(false);
}

function setPristine() {
    pristine.set(true);
}

function success(message) {
    setPristine();
    toast.reset();

    if (message) {
        toast.add({
            ...(Object(message) === message ? message : { message }),
            kind: 'success',
        });
    }
}

function error(message) {
    toast.add({
        ...(Object(message) === message ? message : { message }),
        kind: 'error',
    });
}

export {
    pristine,
    dirty,
    isPristine,
    isDirty,
    setDirty,
    setPristine,
    success,
    error,
};
