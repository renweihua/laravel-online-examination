<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input
                v-model="listQuery.search"
                placeholder="请输入 会员`名称/手机号/邮箱`"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-select v-model="listQuery.is_check" placeholder="请选择账户状态" clearable class="filter-item">
                <el-option key="全部" label="全部" :checked="-1 == listQuery.is_check" value="-1" />
                <el-option key="禁用" label="禁用" :checked="0 == listQuery.is_check" value="0" />
                <el-option key="正常" label="正常" :checked="1 == listQuery.is_check" value="1" />
                <el-option key="异地登录" label="异地登录" :checked="2 == listQuery.is_check" value="2" />
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
            <el-table-column show-overflow-tooltip type="selection"/>
            <el-table-column
                show-overflow-tooltip
                prop="user_id"
                label="Id"
                align="center"
            />
            <el-table-column
                label="会员账户"
                align="left"
                width="200px"
            >
                <template v-slot="{row}">
                    <p>UserId: <span>{{row.user_id}}</span></p>
                    <p>账户: <span>{{row.user_name}}</span></p>
                    <p>手机号: <span>{{row.user_phone}}</span></p>
                    <p>邮箱: <span>{{row.user_email}}</span></p>
                    <p>昵称: <span>{{row.user_info.nick_name}}</span></p>
                </template>
            </el-table-column>
            <el-table-column show-overflow-tooltip align="center" label="头像">
                <template slot-scope="{row}">
                    <img v-if="row.user_info.user_avatar" :src="row.user_info.user_avatar">
                </template>
            </el-table-column>
            <el-table-column
                label="资料信息"
                align="left"
                width="200px"
            >
                <template v-slot="{row}">
                    <p>性别: <span>{{row.user_info.user_sex_text}}</span></p>
                    <p>城市: <span>{{row.user_info.city_info}}</span></p>
                    <p>获赞数: <span>{{row.user_info.get_likes}}</span></p>
                    <p>注册IP: <span>{{row.user_info.created_ip}}</span></p>
                    <p>注册来源: <span>{{row.user_otherlogin.user_origin_text}}</span></p>
                </template>
            </el-table-column>
            <el-table-column
                label="第三方账户"
                align="left"
                width="200px"
            >
                <template v-slot="{row}">
                    <p>QQ: <span>{{row.user_otherlogin.qq_info}}</span></p>
                    <p>Github: <span>{{row.user_otherlogin.github_info}}</span></p>
                    <p>微博: <span>{{row.user_otherlogin.weibo_info}}</span></p>
                </template>
            </el-table-column>
            <el-table-column label="注册时间" show-overflow-tooltip align="center">
                <template slot-scope="{ row }">
                    {{ row.user_info.created_time | parseTime("{y}-{m}-{d} {h}:{i}") }}
                </template>
            </el-table-column>
            <el-table-column
                fixed="right"
                label="操作"
                align="center"
            >
                <template v-slot="{row}">
                    <!-- 状态变更 -->
                    <el-button v-if="row.is_sync == 0" type="text"
                               @click="changeStatus(row, 1, 'is_check')">
                        <el-tag :type="1 | statusFilter">
                            <i class="el-icon-unlock"/>
                            启用`同步`
                        </el-tag>
                    </el-button>
                    <el-button v-else-if="row.is_sync == 1" type="text"
                               @click="changeStatus(row, 0, 'is_check')">
                        <el-tag :type="0 | statusFilter">
                            <i class="el-icon-lock"/>
                            关闭`同步`
                        </el-tag>
                    </el-button>
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
    import {getList} from '@/api/bbs/users.js';

    import waves from '@/directive/waves'; // waves directive
    import Edit from './components/detail';
    import {parseTime, getFormatDate} from '@/utils/index';
    import clip from '@/utils/clipboard' // use clipboard directly

    import TextOverflow from '@/components/TextOverflow/index';

    export default {
        name: 'users-manage',
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
                    is_check: -1,
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
                    'author_id': row.author_id,
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
        }
    }
</script>
