<?php

namespace App\Http\Requests;

trait SceneValidator
{
    public function getRules($scene = '') : array
    {
        // 如何设定为自动验证为false，则无需进行验证
        if ( !$this->authorize() ) return [];
        // 如果指定的场景存在，则返回该场景的规则
        if ( isset($this->scenes()[$scene]) ) return $this->getSceneRules($scene);
        // 如何没有设定场景验证或没有定义场景验证，那么返回所有的验证规则
        if ( empty($scene) || !method_exists($this, 'scenes')) return $this->rules();
        return [];
    }

    private function getSceneRules($scene = '')
    {
        $rules = [];
        $all_rules = $this->rules();
        $scenes = $this->scenes();
        if (isset($scenes[$scene])){
            foreach ($scenes[$scene] as $rule) {
                if (isset($all_rules[$rule])){
                    $rules[$rule] = $all_rules[$rule];
                }
            }
        }
        // 如果存在公共设置，那么合并
        if (isset($scenes['common'])){
            foreach ($scenes['common'] as $rule){
                if (isset($all_rules[$rule])){
                    $rules[$rule] = $all_rules[$rule];
                }
            }
        }
        return $rules;
    }

    public function getMessages($scene = '') : array
    {
        return $this->messages();
    }
}
