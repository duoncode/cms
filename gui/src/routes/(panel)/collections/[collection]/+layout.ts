import req from '$lib/req';

export const load = async ({ params }) => {
    const response = await req.get(`collection/${params.collection}`);
    const pages = response.ok ? response.data : [];

    return {
        collection: params.collection,
        pages,
    };
};
