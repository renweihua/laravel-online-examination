import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/video-manage/videos',
    method: 'get',
    params
  })
}

export function setDel(data) {
  return request({
    url: `/video-manage/videos/delete`,
    method: 'delete',
    data
  })
}

export function changeFiledStatus(data) {
  return request({
    url: `/video-manage/videos/changeFiledStatus`,
    method: 'put',
    data
  })
}
