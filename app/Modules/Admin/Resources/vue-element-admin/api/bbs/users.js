import request from '@/utils/request'

export function getList(params) {
    return request({
        url: '/bbs/users',
        method: 'get',
        params
    });
}
