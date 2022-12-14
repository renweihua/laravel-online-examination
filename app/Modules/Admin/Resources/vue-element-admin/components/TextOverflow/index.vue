<template>
    <div :style="{maxWidth:maxWidth}" class="text text-overflow">
        <span v-if="!isOver" class="spanItem">{{content}}</span>
        <el-tooltip v-else class="item" effect="dark" :content="content" :placement="placement">
            <span class="spanItem">{{content}}</span>
        </el-tooltip>
    </div>
</template>
<script>
export default {
    props:{
        content:{ //标题内容
            type:String,
            default:''
        },
        maxWidth:{//最大显示宽度
            type:String,
            default:'100px'
        },
        placement:{//提示位置
            type:String,
            default:'top-start'
        }
    },
    data(){
        return {
            isOver:true //是不是超出指定宽度
        }
    },
    mounted(){
    },
    methods:{
    }
}
</script>
<style scoped="scoped">
.text{text-overflow: ellipsis; overflow: hidden; white-space: nowrap; cursor: pointer;}
div.text-overflow{
    display: inline-block;
}
</style>
