import request from '@/utils/request'

export function getAdminsSelect(params, get_url = false) {
    var url = `/admins/getSelectLists`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'get',
        params
    });
}

export function getList(params, get_url = false) {
    var url = `/admins`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'get',
        params
    });
}

// export function detail(data) {
//     return request({
//         url: '/admins/detail',
//         method: 'post',
//         data
//     });
// }

export function create(data, get_url = false) {
    var url = `/admins/create`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'post',
        data
    });
}

export function update(data, get_url = false) {
    var url = `/admins/update`;
    if (get_url) return url;
    return request({
        url: url,
        method: 'put',
        data
    });
}

export function setDel(data, get_url = false) {
    var url = '/admins/delete';
    if (get_url) return url;
    return request({
        url: url,
        method: 'delete',
        data
    });
}

export function changeFiledStatus(data, get_url = false) {
    var url = '/admins/changeFiledStatus';
    if (get_url) return url;
    return request({
        url: url,
        method: 'put',
        data
    });
}
