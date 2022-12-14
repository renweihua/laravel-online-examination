import request from '@/utils/request'

export function getList(params) {
    return request({
        url: '/bbs/dynamics',
        method: 'get',
        params
    });
}

export function setExcellent(data) {
    return request({
        url: `/bbs/dynamics/set-excellent`,
        method: 'patch',
        data
    });
}
