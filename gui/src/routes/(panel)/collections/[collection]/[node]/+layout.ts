import req from '$lib/req';

export const load = async ({ params }) => {
    const response = await req.get(`collection/${params.node}`);
    return (response.ok ? response.data : {}) as Collection;
};
