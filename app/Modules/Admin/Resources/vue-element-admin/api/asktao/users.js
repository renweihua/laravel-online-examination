import request from '@/utils/request'

export function getList(params) {
    return request({
        url: '/asktao/users',
        method: 'get',
        params
    })
}

export function create(data) {
    return request({
        url: '/asktao/users/create',
        method: 'post',
        data
    })
}

export function update(data) {
    return request({
        url: '/asktao/users/update',
        method: 'put',
        data
    })
}

export function changeFiledStatus(data) {
    return request({
        url: '/asktao/users/changeFiledStatus',
        method: 'put',
        data
    })
}
