<?php

namespace App\Models\Rabc;

use App\Models\Model;
use Illuminate\Support\Facades\Storage;

class Admin extends Model
{
    protected $primaryKey = 'admin_id';
    protected $is_delete = 0; //是否开启删除（1.开启删除，就是直接删除；0.假删除）

    /**
     * 是否主动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;

    protected $hidden = ['password'];

    public function getAdminByName(string $admin_name)
    {
        return $this->where('admin_name', $admin_name)->first();
    }

    public function setPasswordAttribute($key)
    {
        if (empty($key)) unset($this->attributes['password']);
        else $this->attributes['password'] = hash_encryption($key);
    }

    public function adminInfo()
    {
        return $this->hasOne(AdminInfo::class, $this->primaryKey, $this->primaryKey);
    }

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, AdminWithRole::class, 'admin_id', 'role_id')->withPivot(['admin_id', 'role_id']);
    }

    /**
     * @Function         assignRole
     *
     * @param $roles
     *
     * @return bool
     * @author           : cnpscy <[2278757482@qq.com]>
     * @chineseAnnotation:给用户分配角色
     * @englishAnnotation:
     */
    public function assignRole($roles)
    {
        return $this->roles()->save($roles);
    }

    /**
     * @Function         deleteRole
     *
     * @param $roles
     *
     * @return mixed
     * @author           : cnpscy <[2278757482@qq.com]>
     * @chineseAnnotation:取消用户分配的角色，取消而不是删除
     * @englishAnnotation:
     */
    public function deleteRole($roles)
    {
        return $this->roles()->detach($roles);
    }

    public function getAdminHeadAttribute($key)
    {
        if (empty($key)) return $key;
        return Storage::url($key);
    }

    public function setAdminHeadAttribute($key)
    {
        if (!empty($key)) {
            $this->attributes['admin_head'] = str_replace(Storage::url('/'), '', $key);
        }
    }

    // 获取指定管理员的权限列表
    public static function getRabcByAdmin($admin_id)
    {
        $roles = Admin::find($admin_id)->roles->toArray();
        if (empty($roles)) return [];
        $role_ids = array_column($roles, 'role_id');
        if (empty($role_ids)) return [];
        $menu_ids = AdminRoleWithMenu::getInstance()->getMenuIdsByRoles($role_ids);
        if (empty($menu_ids)) return [];
        $menus = AdminMenu::getInstance()->getMenusByIdsForRabc($menu_ids)->toArray();
        if (empty($menus)) return [];
        return $menus;
    }
}
