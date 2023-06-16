import req from '$lib/req';

export const load = async ({ params, parent }) => {
    const collection = await parent();
    const response = await req.get(`node/${params.node}`);
    return { collection, node: response.ok ? response.data : {} };
};
