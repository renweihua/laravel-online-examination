import request from '@/utils/request'

// 区服列表
export function dists() {
    return request({
        url: '/asktao/dists',
        method: 'get'
    })
}

export function getList(params) {
    return request({
        url: '/asktao/servers',
        method: 'get',
        params
    })
}

export function create(data) {
    return request({
        url: '/asktao/servers/create',
        method: 'post',
        data
    })
}

export function update(data) {
    return request({
        url: '/asktao/servers/update',
        method: 'put',
        data
    })
}

export function changeFiledStatus(data) {
    return request({
        url: '/asktao/servers/changeFiledStatus',
        method: 'put',
        data
    })
}
