import store from '@/store'
import {checkPermission as checkPermissionApi} from '@/api/common';

/**
 * @param {Array} value
 * @returns {Boolean}
 * @example see @/views/permission/directive.vue
 */
export default function checkPermission(value) {
    if (value && value instanceof Array && value.length > 0) {
        const roles = store.getters && store.getters.roles;
        const permissionRoles = value;

        const hasPermission = roles.some(role => {
            return permissionRoles.includes(role);
        });
        return hasPermission;
    } else {
        console.error(`need roles! Like v-permission="['admin','editor']"`);
        return false;
    }
}

// 检测按钮的权限
export function checkButtonPermission(that, apis){
    checkPermissionApi(apis).then(function (res){
        let result = res.data;
        for (let key in result) {
            for (let permission in that.permission){
                if (that.permission[permission].url == result[key].old_url){
                    that.permission[permission].status = result[key].status;
                }
            }
        }
    });
}
