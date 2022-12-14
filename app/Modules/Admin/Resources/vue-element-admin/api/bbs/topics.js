import request from '@/utils/request'

export function getList(params) {
    return request({
        url: '/bbs/topics',
        method: 'get',
        params
    });
}

export function create(data, get_url = false) {
    let url = `/bbs/topics/create`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'post',
        data
    });
}

export function update(data, get_url = false) {
    let url = `/bbs/topics/update`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'put',
        data
    });
}

export function changeFiledStatus(data, get_url = false) {
    let url = `/bbs/topics/changeFiledStatus`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'patch',
        data
    });
}