<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input
                v-model="listQuery.search"
                placeholder="请输入 话题`名称/描述`"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-select v-model="listQuery.is_default" placeholder="请选择默认状态" clearable class="filter-item">
                <el-option key="全部" label="全部" :checked="-1 == listQuery.is_default" value="-1" />
                <el-option key="否" label="否" :checked="0 == listQuery.is_default" value="0" />
                <el-option key="默认" label="默认" :checked="1 == listQuery.is_default" value="1" />
            </el-select>
            <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
                {{ $t('table.search') }}
            </el-button>
            <el-button
                class="filter-item"
                style="margin-left: 10px;"
                type="primary"
                icon="el-icon-plus"
                @click="handleEdit"
            >
                {{ $t('table.add') }}
            </el-button>
        </div>
        <el-table
            v-loading="listLoading"
            :data="list"
            :element-loading-text="elementLoadingText"
            @selection-change="setSelectRows"
            border
            class="margin-buttom-10"
        >
            <el-table-column v-if="false" show-overflow-tooltip type="selection"/>
            <el-table-column
                show-overflow-tooltip
                prop="topic_id"
                label="Id"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="topic_name"
                label="话题名称"
                align="center"
            />
            <el-table-column show-overflow-tooltip align="center" label="封面">
                <template slot-scope="{row}">
                    <img v-if="row.topic_cover" :src="row.topic_cover">
                </template>
            </el-table-column>
            <el-table-column
                show-overflow-tooltip
                prop="topic_description"
                label="话题描述"
                align="center"
            />
            <el-table-column
                label="相关统计"
                align="left"
                width="200px"
            >
                <template v-slot="{row}">
                    <p>动态数量: <span>{{row.dynamic_count}}</span></p>
                    <p>关注人数: <span>{{row.follow_count}}</span></p>
                </template>
            </el-table-column>
            <el-table-column label="创建时间" show-overflow-tooltip align="center">
                <template slot-scope="{ row }">
                    {{ row.created_time | parseTime("{y}-{m}-{d} {h}:{i}") }}
                </template>
            </el-table-column>
            <el-table-column
                fixed="right"
                label="操作"
                align="center"
            >
                <template v-slot="{row}">
                    <!-- 状态变更 -->
                    <el-button v-if="row.is_default == 0" type="text"
                               @click="changeStatus(row, 1, 'is_default')">
                        <el-tag :type="1 | statusFilter">
                            <i class="el-icon-unlock"/>
                            设为`默认`
                        </el-tag>
                    </el-button>
                    <el-button v-else-if="row.is_default == 1" type="text"
                               @click="changeStatus(row, 0, 'is_default')">
                        <el-tag :type="0 | statusFilter">
                            <i class="el-icon-lock"/>
                            关闭`默认`
                        </el-tag>
                    </el-button>
                    <!-- 编辑与删除 -->
                    <el-button type="text" icon="el-icon-edit" @click="handleEdit(row)">编辑</el-button>
                    <el-button v-if="false" type="text" icon="el-icon-delete" @click="handleDelete(row)">删除</el-button>
                </template>
            </el-table-column>
        </el-table>
        <el-pagination
            background
            v-show="total > 0"
            :current-page="listQuery.page"
            :page-size="listQuery.limit"
            :layout="layout"
            :total="total"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
        />
        <edit ref="edit" @fetchData="getList"/>
    </div>
</template>

<script>
    import {getList,  create, update, changeFiledStatus} from '@/api/bbs/topics.js';

    import waves from '@/directive/waves'; // waves directive
    import Edit from './components/detail';
    import {parseTime, getFormatDate} from '@/utils/index';
    import clip from '@/utils/clipboard' // use clipboard directly

    import TextOverflow from '@/components/TextOverflow/index';

    export default {
        name: 'topics-manage',
        components: {Edit, TextOverflow},
        directives: {waves},
        filters: {
            parseTime: parseTime,
            getFormatDate: getFormatDate,
            statusFilter(status) {
                const statusMap = {
                    1: 'success',
                    0: 'danger'
                }
                return statusMap[status];
            }
        },
        data() {
            return {
                is_batch: 0, // 默认不开启批量删除
                layout: 'total, sizes, prev, pager, next, jumper',
                selectRows: '',
                elementLoadingText: '正在加载...',

                list: [],
                total: 0,
                listLoading: true,
                listQuery: {
                    page: 1,
                    limit: 20,
                    enable: '',
                    search: '',
                    is_default: -1,
                    is_download: 0, // 是否下载：1.是；默认0
                },
            }
        },
        created() {
            this.getList();
        },
        methods: {
            handleCopy(text, event) {
                clip(text, event)
            },
            setSelectRows(val) {
                this.selectRows = val;
                this.is_batch = 1;
            },
            handleEdit(row) {
                if (row) {
                    this.$refs['edit'].showEdit(row)
                } else {
                    this.$refs['edit'].showEdit()
                }
            },
            handleSizeChange(val) {
                this.listQuery.limit = val;
                this.listQuery.is_download = 0;
                this.getList();
            },
            handleCurrentChange(val) {
                this.listQuery.page = val;
                this.listQuery.is_download = 0;
                this.getList();
            },
            handleFilter() {
                this.listQuery.page = 1;
                this.listQuery.is_download = 0;
                this.getList();
            },
            async getList(callback) {
                this.listLoading = true;
                const {data, status, msg} = await getList(this.listQuery);
                if(this.listQuery.is_download == 1){
                    if (callback){
                        callback(data, status, msg);
                    }
                }else{
                    this.list = data.data;
                    this.total = data.total;
                    this.listQuery.limit = data.per_page || 10;
                }
                setTimeout(() => {
                    this.listLoading = false;
                }, 300);
            },
            // 状态变更
            async changeStatus(row, value, field) {
                const {data, msg, status} = await changeFiledStatus({
                    'topic_id': row.topic_id,
                    'change_field': field,
                    'change_value': value
                });

                // 设置成功之后，同步到当前列表数据
                if (status == 1) row[field] = value;
                this.$message({
                    message: msg,
                    type: status == 1 ? 'success' : 'error',
                });
            },
            // 删除
            handleDelete(row) {
                return;
                var ids = '';
                if (row.topic_id) {
                    ids = row.topic_id;
                } else {
                    if (this.selectRows.length > 0) {
                        ids = this.selectRows.map((item) => item.topic_id).join();
                    } else {
                        this.$message('未选中任何行', 'error');
                        return false
                    }
                }
                // 删除流程
                this.$confirm(
                    '你确定要删除操作吗？删除之后将无法恢复，请谨慎操作',
                    'Warning',
                    {
                        confirmButtonText: 'Confirm',
                        cancelButtonText: 'Cancel',
                        type: 'warning'
                    })
                    .then(async () => {
                        const {status, msg} = await setDel({topic_id: ids, 'is_batch' : this.is_batch});

                        switch (status) {
                            case 1:
                                // this.list.splice($index, 1);
                                this.getList();

                                this.$message({
                                    type: 'success',
                                    message: msg
                                });
                                break;
                            default:
                                this.$message({
                                    type: 'error',
                                    message: msg
                                });
                                break;
                        }

                    })
                    .catch(err => {
                        console.error(err)
                    })
            },
        }
    }
</script>
