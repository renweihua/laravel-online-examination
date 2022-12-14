import request from '@/utils/request'

export function getList(params, get_url = false) {
    let url = `/bbs/user-sigin-logs`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'get',
        params
    });
}

export function create(data, get_url = false) {
    let url = `/bbs/user-sigin-logs/create`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'post',
        data
    });
}

export function setDel(data, get_url = false) {
    let url = `/bbs/user-sigin-logs/delete`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'delete',
        data
    });
}
