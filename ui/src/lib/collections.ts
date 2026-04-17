import { writable, type Writable } from 'svelte/store';
import req from '$lib/req';

export interface NavMeta {
	label: string;
	icon: string | null;
	badge: string | null;
	permission: string | null;
	hidden: boolean;
	order: number;
}

export interface SectionItem {
	type: 'section';
	name: string;
	meta: NavMeta;
	children: NavItem[];
}

export interface CollectionItem {
	type: 'collection';
	name: string;
	slug: string;
	meta: NavMeta;
	children: [];
}

export type NavItem = SectionItem | CollectionItem;

export const collections: Writable<NavItem[]> = writable([]);

export async function fetchCollections(fetchFn: typeof window.fetch) {
	const response = await req.get('collections', {}, fetchFn);

	if (!response?.ok) {
		throw new Error('Fatal error while requesting collections');
	}

	const data = response.data as NavItem[];

	collections.set(data);

	return data;
}
