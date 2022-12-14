import request from '@/utils/request'

export function getList(params, get_url = false) {
    let url = `/bbs/start-diagrams`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'get',
        params
    });
}

export function create(data, get_url = false) {
    let url = `/bbs/start-diagrams/create`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'post',
        data
    });
}

export function update(data, get_url = false) {
    let url = `/bbs/start-diagrams/update`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'put',
        data
    });
}

export function setDel(data, get_url = false) {
    let url = `/bbs/start-diagrams/delete`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'delete',
        data
    });
}

export function changeFiledStatus(data, get_url = false) {
    let url = `/bbs/start-diagrams/changeFiledStatus`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'patch',
        data
    });
}
