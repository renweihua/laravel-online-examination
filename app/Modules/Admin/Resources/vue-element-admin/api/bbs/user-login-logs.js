import request from '@/utils/request'

export function getList(params, get_url = false) {
    let url = `/bbs/user-login-logs`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'get',
        params
    });
}

export function setDel(data, get_url = false) {
    let url = `/bbs/user-login-logs/delete`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'delete',
        data
    });
}
