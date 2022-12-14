<template>
    <div class="app-container">
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
                placeholder="请输入角色/帮派名称"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
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
        </div>

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
                prop="dist"
                label="区服名称"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="gid"
                label="Id"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="name"
                label="帮派名称"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="annouce"
                label="公告"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="level"
                label="帮派等级"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="last_activity_time"
                label="上次活跃时间"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="money"
                label="金币"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="update_time"
                label="更新时间"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="construct"
                label="construct"
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
    import {getParties as getList} from '@/api/asktao/parties.js';

    import waves from '@/directive/waves'; // waves directive
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
                    dist: '',
                    account: '',
                    is_download: 0, // 是否下载：1.是；默认0
                },
            }
        },
        created() {
            this.getList();
        },
        methods: {
            checkFilter(val) {
                return calendarCheckKeyValue[val] || '';
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
