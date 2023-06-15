import req from '$lib/req';

interface Node {
    uid: string;
    title: string;
    type: string;
    creator?: string;
    editor?: string;
    created: string;
    changed: string;
}

interface Collection {
    title: string;
    slug: string;
    nodes: Node[];
}

export const load = async ({ params }) => {
    const response = await req.get(`collection/${params.collection}`);
    return (response.ok ? response.data : {}) as Collection;
};
