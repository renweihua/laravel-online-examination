import request from '@/utils/request'

// 帮派列表
export function getParties(params) {
    return request({
        url: '/asktao/party-basic-infos',
        method: 'get',
        params
    })
}

// 帮派人员列表
export function getPartyUsers(params) {
    return request({
        url: '/asktao/party-users',
        method: 'get',
        params
    })
}
