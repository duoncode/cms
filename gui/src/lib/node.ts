import type { Node } from '$types/data';
import { base } from '$lib/req';
import { goto } from '$app/navigation';
import { _ } from '$lib/locale';
import { setPristine } from '$lib/state';
import req from '$lib/req';
import toast from '$lib/toast';

export interface Result {
	success: boolean;
	uid: string;
}

export async function save(uid: string, node: Node) {
	const response = await req.put(`node/${uid}`, node);

	if (response?.ok) {
		toast.add({
			kind: 'success',
			message: _('Dokument erfolgreich gespeichert!'),
		});

		setPristine();

		return response?.data as Result;
	} else {
		const data = response?.data;

		toast.add({
			kind: 'error',
			message: data.description
				? data.description
				: _('Fehler beim Speichern des Dokuments aufgetreten!'),
		});

		return response?.data as Result;
	}
}

export async function create(node: Node, type: string, documentPath: string) {
	const response = await req.post(`node/${type}`, node);

	if (response?.ok) {
		toast.add({
			kind: 'success',
			message: _('Dokument erfolgreich erstellt!'),
		});

		setPristine();

		await goto(`${base}${documentPath}/${node.uid}`, {
			invalidateAll: true,
		});

		return response.data as Result;
	} else {
		toast.add({
			kind: 'error',
			message: _('Fehler beim Erstellen des Dokuments aufgetreten!'),
		});

		return response?.data as Result;
	}
}

export async function remove(uid: string, collectionPath: string) {
	const response = await req.del(`node/${uid}`);

	if (response?.ok) {
		await goto(`${base}${collectionPath}`, { invalidateAll: true });

		toast.add({
			kind: 'success',
			message: _('Dokument erfolgreich gelöscht!'),
		});
	} else {
		toast.add({
			kind: 'error',
			message: _('Fehler beim Löschen des Dokuments aufgetreten!'),
		});
	}
}
