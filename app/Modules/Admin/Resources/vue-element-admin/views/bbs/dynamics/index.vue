<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input
                v-model="listQuery.search"
                placeholder="请输入 动态标题/内容"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-input
                v-model="listQuery.user_search"
                placeholder="请输入 会员`名称/手机号/邮箱`"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-select v-model="listQuery.is_check" placeholder="请选择`审核`状态" clearable class="filter-item">
                <el-option key="全部" label="全部" :checked="-1 == listQuery.is_check" value="-1" />
                <el-option key="待审核" label="待审核" :checked="0 == listQuery.is_check" value="0" />
                <el-option key="通过" label="通过" :checked="1 == listQuery.is_check" value="1" />
            </el-select>
            <el-select v-model="listQuery.is_public" placeholder="请选择`公开`状态" clearable class="filter-item">
                <el-option key="全部" label="全部" :checked="-1 == listQuery.is_public" value="-1" />
                <el-option key="私密" label="私密" :checked="0 == listQuery.is_public" value="0" />
                <el-option key="公开" label="公开" :checked="1 == listQuery.is_public" value="1" />
                <el-option key="密码访问" label="密码访问" :checked="1 == listQuery.is_public" value="2" />
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
                prop="dynamic_id"
                label="Id"
                align="center"
            />
            <el-table-column
                label="作者信息"
                align="left"
                width="200px"
            >
                <template v-slot="{row}">
                    <p>UserId: <span>{{row.user.user_id}}</span></p>
                    <p>账户: <span>{{row.user.user_name}}</span></p>
                    <p>手机号: <span>{{row.user.user_phone}}</span></p>
                    <p>邮箱: <span>{{row.user.user_email}}</span></p>
                    <p>昵称: <span>{{row.user_info.nick_name}}</span></p>
                </template>
            </el-table-column>
            <el-table-column
                show-overflow-tooltip
                prop="dynamic_title"
                label="动态标题"
                align="center"
            />
            <el-table-column show-overflow-tooltip align="center" label="封面">
                <template slot-scope="{row}">
                    <img v-if="row.dynamic_cover" :src="row.dynamic_cover">
                </template>
            </el-table-column>
            <el-table-column
                show-overflow-tooltip
                prop="created_ip"
                label="发布IP"
                align="center"
            />
            <el-table-column label="发布时间" show-overflow-tooltip align="center">
                <template slot-scope="{ row }">
                    {{ row.created_time | parseTime("{y}-{m}-{d} {h}:{i}") }}
                </template>
            </el-table-column>
            <el-table-column label="精选时间" show-overflow-tooltip align="center">
                <template slot-scope="{ row }">
                    {{ row.excellent_time | parseTime("{y}-{m}-{d} {h}:{i}") }}
                </template>
            </el-table-column>
            <el-table-column
                fixed="right"
                label="操作"
                align="center"
            >
                <template v-slot="{row}">
                    <!-- 状态变更 -->
                    <el-button v-if="row.excellent_time == 0" type="text"
                               @click="setExcellentByDynamic(row, 1)">
                        <el-tag :type="1 | statusFilter">
                            <i class="el-icon-unlock"/>
                            设为`精选`
                        </el-tag>
                    </el-button>
                    <el-button v-else-if="row.excellent_time > 0" type="text"
                               @click="setExcellentByDynamic(row, 0)">
                        <el-tag :type="0 | statusFilter">
                            <i class="el-icon-lock"/>
                            关闭`精选`
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
    import {getList, setExcellent} from '@/api/bbs/dynamics.js';

    import waves from '@/directive/waves'; // waves directive
    import Edit from './components/detail';
    import {parseTime, getFormatDate} from '@/utils/index';
    import clip from '@/utils/clipboard' // use clipboard directly

    import TextOverflow from '@/components/TextOverflow/index';

    export default {
        name: 'dynamics-manage',
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
                    user_search: '',
                    is_check: -1, // 审核状态
                    is_public: -1, // 公开状态
                    is_download: 0, // 是否下载：1.是；默认0
                },
                // 同步作者
                sync_author:{
                    share_url: '',
                }
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
            // 同步作者
            async syncAuthor(){
                const {msg, status, data} = await syncAuthor({
                    'share_url': this.sync_author.share_url,
                });
                if (status){
                    this.sync_author.share_url = '';
                }
                this.$message({
                    message: msg,
                    type: status == 1 ? 'success' : 'error',
                });
            },
            // 同步作者的作品
            async syncVideosByAuthor(row){
                const {msg, status} = await syncAuthorVideos({
                    'author_id': row.author_id,
                });

                this.$message({
                    message: msg,
                    type: status == 1 ? 'success' : 'error',
                });
            },
            // 标记精选
            async setExcellentByDynamic(row, value) {
                const {data, msg, status} = await setExcellent({
                    'dynamic_id': row.dynamic_id,
                    'is_excellent': value,
                });

                // 设置成功之后，同步到当前列表数据
                if (status == 1) row['excellent_time'] = data.excellent_time;
                this.$message({
                    message: msg,
                    type: status == 1 ? 'success' : 'error',
                });
            },
        }
    }
</script>
