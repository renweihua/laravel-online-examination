<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input
                v-model="listQuery.search"
                placeholder="请输入区服名称"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-select v-model="listQuery.dist" placeholder="请选择区服" clearable class="filter-item">
                <el-option
                    v-for="item in dists"
                    :key="item.dist"
                    :checked="item.dist == listQuery.search"
                    :label="item.dist"
                    :value="item.dist"
                />
            </el-select>

            <el-select v-model="listQuery.enable" placeholder="请选择启用状态" clearable class="filter-item">
                <el-option
                    v-for="item in calendarCheckOptions"
                    :key="item.key"
                    :checked="item.key == listQuery.enable"
                    :label="item.display_name+'('+item.key+')'"
                    :value="item.key"
                />
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
            <el-table-column show-overflow-tooltip type="selection"/>
            <el-table-column
                show-overflow-tooltip
                prop="id"
                label="区线Id"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="dist"
                label="区服名称"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="server"
                label="区线名称"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="ip"
                label="IP"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="port"
                label="端口"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="max_user"
                label="最大人数"
                align="center"
            />
            <el-table-column align="center" prop="enable" label="启用状态">
                <template slot-scope="{row}">
                    <el-tag :type="row.enable | statusFilter">
                        <i :class="enable == 1 ? 'el-icon-unlock' : 'el-icon-lock'"/>
                        {{ row.enable | checkFilter }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column
                fixed="right"
                label="操作"
                align="center"
            >
                <template v-slot="{row}">
                    <!-- 状态变更 -->
                    <el-button v-if="row.enable == 0" type="text"
                               @click="changeStatus(row, 1)">
                        <el-tag :type="1 | statusFilter">
                            <i class="el-icon-unlock"/>
                            启用
                        </el-tag>
                    </el-button>
                    <el-button v-else-if="row.enable == 1" type="text"
                               @click="changeStatus(row, 0)">
                        <el-tag :type="0 | statusFilter">
                            <i class="el-icon-lock"/>
                            禁用
                        </el-tag>
                    </el-button>
                    <!-- 编辑与删除 -->
                    <el-button type="text" icon="el-icon-edit" @click="handleEdit(row)">编辑</el-button>
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
    import {dists, getList, changeFiledStatus} from '@/api/asktao/servers.js';

    import waves from '@/directive/waves'; // waves directive
    import Edit from './components/detail';
    import {parseTime, getFormatDate} from '@/utils/index';

    const calendarCheckOptions = [
        {key: '-1', display_name: '全部'},
        {key: '1', display_name: '启用'},
        {key: '0', display_name: '禁用'}
    ];

    const calendarCheckKeyValue = calendarCheckOptions.reduce((acc, cur) => {
        acc[cur.key] = cur.display_name
        return acc
    }, {});

    export default {
        name: 'friendlinkManage',
        components: {Edit},
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
            },
            checkFilter(type) {
                return calendarCheckKeyValue[type] || '';
            }
        },
        data() {
            return {
                is_batch: 0, // 默认不开启批量删除
                layout: 'total, sizes, prev, pager, next, jumper',
                selectRows: '',
                elementLoadingText: '正在加载...',
                calendarCheckOptions,

                list: [],
                total: 0,
                listLoading: true,
                listQuery: {
                    page: 1,
                    limit: 20,
                    enable: '',
                    search: '',
                    is_download: 0, // 是否下载：1.是；默认0
                },

                // 区服列表
                dists: [],
            }
        },
        created() {
            this.getList();
            this.getAllDists();
        },
        methods: {
            async getAllDists() {
                this.listLoading = true;
                const {data, status, msg} = await dists();
                this.dists = data;
            },
            checkFilter(val) {
                return calendarCheckKeyValue[val] || '';
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
            async changeStatus(row, value) {
                const {data, msg, status} = await changeFiledStatus({
                    'link_id': row.link_id,
                    'change_field': 'enable',
                    'change_value': value
                });

                // 设置成功之后，同步到当前列表数据
                if (status == 1) row.enable = value;
                this.$message({
                    message: msg,
                    type: status == 1 ? 'success' : 'error',
                });
            },
        }
    }
</script>
