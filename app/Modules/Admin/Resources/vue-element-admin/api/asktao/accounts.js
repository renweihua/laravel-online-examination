import request from '@/utils/request'

export function getList(params) {
    return request({
        url: '/asktao/accounts',
        method: 'get',
        params
    })
}

export function create(data) {
    return request({
        url: '/asktao/accounts/create',
        method: 'post',
        data
    })
}

export function update(data) {
    return request({
        url: '/asktao/accounts/update',
        method: 'put',
        data
    })
}

export function changeFiledStatus(data) {
    return request({
        url: '/asktao/accounts/changeFiledStatus',
        method: 'put',
        data
    })
}

// 更改登录密码
export function updatePasswordByAccount(data) {
    return request({
        url: '/asktao/accounts/updatePasswordByAccount',
        method: 'put',
        data
    })
}

// 重置checknum效验字段
export function resetChecknum(data) {
    return request({
        url: '/asktao/accounts/resetChecknum',
        method: 'post',
        data
    })
}

// 拉黑账户
export function setBlocked(data) {
    return request({
        url: '/asktao/accounts/setBlocked',
        method: 'post',
        data
    })
}

// 取消拉黑账户
export function cancelBlocked(data) {
    return request({
        url: '/asktao/accounts/cancelBlocked',
        method: 'post',
        data
    })
}

// 账户元宝充值
export function rechargeCoin(data) {
    return request({
        url: '/asktao/accounts/rechargeCoin',
        method: 'post',
        data
    })
}

// 解除自动锁定
export function cancelAutoLock(data) {
    return request({
        url: '/asktao/accounts/cancelAutoLock',
        method: 'patch',
        data
    });
}

// 解除交易锁定
export function cancelTradeLock(data) {
    return request({
        url: '/asktao/accounts/cancelTradeLock',
        method: 'patch',
        data
    });
}
