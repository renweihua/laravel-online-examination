import request from '@/utils/request'

export function getList(params) {
  return request({
    url: '/video-manage/authors',
    method: 'get',
    params
  })
}

export function syncAuthor(data) {
  return request({
    url: '/video-manage/authors/sync-author',
    method: 'post',
    data
  })
}

export function syncAuthorVideos(data) {
  return request({
    url: '/video-manage/authors/sync-author-videos',
    method: 'post',
    data
  })
}

export function changeFiledStatus(data) {
  return request({
    url: `/video-manage/authors/changeFiledStatus`,
    method: 'put',
    data
  })
}
