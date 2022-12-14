<template>
    <div class="app-container">
        <!--
        <div class="filter-container">
            <el-input
                v-model="listQuery.dist"
                placeholder="区服筛选"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-input
                v-model="listQuery.account"
                placeholder="账户筛选"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-input
                v-model="listQuery.search"
                placeholder="请输入角色"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="handleFilter">
                {{ $t('table.search') }}
            </el-button>
        </div>
        -->
        <h2>在线区服统计</h2>
        <el-table
            v-loading="listLoading"
            :data="statistics_list"
            :element-loading-text="elementLoadingText"
            @selection-change="setSelectRows"
            border
            class="margin-buttom-10"
        >
            <el-table-column
                show-overflow-tooltip
                prop="dist"
                label="区服名称"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="server"
                label="线路"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="online"
                label="在线角色数量"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="max_user"
                label="上限数量"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="report_time"
                label="最近更新时间"
                align="center"
            />
        </el-table>

        <h2>在线角色列表</h2>
        <el-table
            v-loading="listLoading"
            :data="list"
            :element-loading-text="elementLoadingText"
            @selection-change="setSelectRows"
            border
            class="margin-buttom-10"
        >
            <el-table-column
                show-overflow-tooltip
                prop="content.locked_gid"
                label="Gid"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="content.game_server"
                label="线路"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="name"
                label="账户"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="content.active_char"
                label="角色名称"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="time"
                label="登录时间"
                align="center"
            />
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
    </div>
</template>

<script>
    import {getOnlineUsers, getOnlineUserStatistics} from '@/api/asktao/online-statistics.js';

    import waves from '@/directive/waves'; // waves directive
    import {parseTime, getFormatDate} from '@/utils/index';

    export default {
        name: 'friendlinkManage',
        components: {},
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
                    dist: '',
                    account: '',
                    is_download: 0, // 是否下载：1.是；默认0
                },

                statistics_list:[],
            }
        },
        created() {
            this.getList();
            this.getOnlineUserStatistics();
        },
        methods: {
            async getOnlineUserStatistics(){
                const {data} = await getOnlineUserStatistics();
                this.statistics_list = data.data;

                console.log(this.statistics_list);
            },
            setSelectRows(val) {
                this.selectRows = val;
                this.is_batch = 1;
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
                const {data, status, msg} = await getOnlineUsers(this.listQuery);
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
