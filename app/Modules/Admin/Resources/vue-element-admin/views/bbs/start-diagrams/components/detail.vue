<template>
    <el-dialog
            :title="title"
            :visible.sync="dialogFormVisible"
            width="500px"
            @close="close"
    >
        <el-form ref="form" :model="form" :rules="rules" label-width="80px">
            <el-form-item label="名称" prop="diagram_name">
                <el-input v-model.trim="form.diagram_name" autocomplete="off"/>
            </el-form-item>
            <el-form-item label="封面" prop="diagram_cover">
                <pan-thumb :image="form.diagram_cover" />

                <el-button
                    type="primary"
                    icon="el-icon-upload"
                    style="position: absolute;bottom: 15px;margin-left: 40px;"
                    @click="openSelectFiles"
                >
                    选择图标
                </el-button>
            </el-form-item>
            <el-form-item label="排序" prop="diagram_sort">
                <el-input v-model.trim="form.diagram_sort" type="number" autocomplete="off"/>
            </el-form-item>
            <el-form-item label="是否启用" prop="is_check">
                <el-radio-group v-model="form.is_check">
                    <el-radio :label="0" :checked="form.is_check == 0 ? 'checked' : ''">禁用</el-radio>
                    <el-radio :label="1" :checked="form.is_check == 1 ? 'checked' : ''">启用</el-radio>
                </el-radio-group>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer">
            <el-button @click="close">取 消</el-button>
            <el-button type="primary" @click="save">确 定</el-button>
        </div>
        <file-select v-if="show_files" ref="file" :batch_select="false" @handleSubmit="selectImageSubmit" />
    </el-dialog>
</template>

<script>
    import {create, update} from '@/api/bbs/start-diagrams'
    import {getUploadUrl} from '@/api/common'
    import {isUrl} from '@/utils/validate'

    import PanThumb from '@/components/PanThumb'
    import FileSelect from '@/components/FilesSelect/index'

    export default {
        name: '',
        components: {
            PanThumb,
            FileSelect
        },
        data() {
            return {
                form: {
                    diagram_name: '',
                    diagram_cover: '',
                    diagram_sort: 100,
                    is_check: 0
                },
                rules: {
                    diagram_name: [
                        {required: true, trigger: 'blur', message: '请输入启动图名称'},
                        {
                            min: 2,
                            max: 20,
                            message: '长度在 2 到 20 个字符',
                            trigger: 'blur'
                        }
                    ]
                },
                title: '',
                dialogFormVisible: false,

                // 图片上传
                upload_url: '',
                show_files: false,
            }
        },
        created() {
            this.upload_url = getUploadUrl();
        },
        methods: {
            // 打开文件选择器
            openSelectFiles(){
                this.show_files = true;
                this.$nextTick(() => {
                    this.$refs.file.init();
                });
            },
            // 选择指定文件之后，点击’确认‘，获取到的文件信息
            selectImageSubmit(e){
                this.form.diagram_cover = e.file_url;
            },
            showEdit(row) {
                const detail = Object.assign({}, row);
                if (!detail) {
                    this.title = '添加';
                } else {
                    this.title = '编辑';
                    this.form = Object.assign(this.form, detail);
                }
                this.dialogFormVisible = true;
            },
            close() {
                this.$refs['form'].resetFields();
                this.form = this.$options.data().form;
                this.dialogFormVisible = false;
            },
            save() {
                this.$refs['form'].validate(async (valid) => {
                    if (valid) {
                        const {msg, status} = this.form.id ? await update(this.form) : await create(this.form);
                        this.$message({
                            message: msg,
                            type: status == 1 ? 'success' : 'error',
                        });
                        this.$emit('fetchData');
                        this.close();
                    } else {
                        return false;
                    }
                });
            }
        }
    }
</script>
