import { writable, derived, type Writable } from 'svelte/store';
import toast from '$lib/toast';
import type { Document } from '$types/data';
import type { Field } from '$types/field';

const pristine = writable(true);
const dirty = derived(pristine, $pristine => !$pristine);
const currentDocument: Writable<null | Document> = writable(null);
const currentFields: Writable<null | Field> = writable(null);

function setDirty() {
    pristine.set(false);
}

function setPristine() {
    pristine.set(true);
}

function success(message: string) {
    setPristine();
    toast.reset();

    if (message) {
        toast.add({
            ...(Object(message) === message ? message : { message }),
            kind: 'success',
        });
    }
}

function error(message: string) {
    toast.add({
        ...(Object(message) === message ? message : { message }),
        kind: 'error',
    });
}

export {
    pristine,
    dirty,
    setDirty,
    setPristine,
    success,
    error,
    currentDocument,
    currentFields,
};
