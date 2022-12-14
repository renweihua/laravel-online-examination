import request from '@/utils/request'

export function getOnlineUserStatistics(params) {
    return request({
        url: '/asktao/get-online-user-statisitcs-by-dist',
        method: 'get',
        params
    })
}
export function getOnlineUsers(params) {
    return request({
        url: '/asktao/get-online-users',
        method: 'get',
        params
    })
}
