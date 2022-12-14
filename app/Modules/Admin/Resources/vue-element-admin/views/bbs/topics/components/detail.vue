<template>
    <el-dialog
            :title="title"
            :visible.sync="dialogFormVisible"
            width="500px"
            @close="close"
    >
        <el-form ref="form" :model="form" :rules="rules" label-width="80px">
            <el-form-item label="话题名称" prop="topic_name">
                <el-input v-model.trim="form.topic_name" autocomplete="off"/>
            </el-form-item>
            <el-form-item label="封面图" prop="topic_cover">
                <pan-thumb :image="form.topic_cover" />

                <el-button
                    type="primary"
                    icon="el-icon-upload"
                    style="position: absolute;bottom: 15px;margin-left: 40px;"
                    @click="openSelectFiles"
                >
                    选择图标
                </el-button>
            </el-form-item>
            <el-form-item label-width="70px" label="描述:">
                <el-input v-model="form.topic_description" :rows="2" type="textarea" class="article-textarea"
                          autosize placeholder="Please enter the 话题描述"/>
                <span v-show="contentShortLength" class="word-counter">{{ contentShortLength }} words</span>
            </el-form-item>
            <el-form-item label="排序" prop="topic_sort">
                <el-input v-model.trim="form.topic_sort" type="number" autocomplete="off"/>
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
    import {create, update} from '@/api/bbs/topics'
    import {getUploadUrl} from '@/api/common'
    import {isUrl} from '@/utils/validate'

    import PanThumb from '@/components/PanThumb'
    import FileSelect from '@/components/FilesSelect/index'

    // 定义一个全局的变量，谁用谁知道
    var validUrl = (rule, value, callback) => {
        if (!isUrl(value)) {
            callback(new Error('请输入正确的网址'))
        } else {
            callback()
        }
    }

    export default {
        name: '',
        components: {
            PanThumb,
            FileSelect
        },
        data() {
            return {
                form: {
                    topic_name: '',
                    topic_cover: '',
                    topic_description: '',
                    topic_sort: 100
                },
                rules: {
                    topic_name: [
                        {required: true, trigger: 'blur', message: '请输入话题名称'},
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
        computed: {
            contentShortLength() {
                return this.form.topic_description?.length;
            },
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
                this.form.topic_cover = e.file_url;
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
                        const {msg, status} = this.form.topic_id ? await update(this.form) : await create(this.form);
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
