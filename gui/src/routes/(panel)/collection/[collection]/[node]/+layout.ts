import req from '$lib/req';

export const load = async ({ params }) => {
    const response = await req.get(`node/${params.node}`);
    return response.ok ? response.data : {};
};
