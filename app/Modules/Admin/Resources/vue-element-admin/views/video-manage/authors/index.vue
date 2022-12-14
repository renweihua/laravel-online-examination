<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input
                v-model="sync_author.share_url"
                placeholder="请输入`作者`的分享URL"
                style="width: 500px;"
                class="filter-item"
            />
            <el-button
                class="filter-item"
                style="margin-left: 10px;"
                type="primary"
                icon="el-icon-refresh"
                @click="syncAuthor"
            >
                立即同步`抖音`作者
            </el-button>
        </div>
        <div class="filter-container">
            <el-input
                v-model="listQuery.search"
                placeholder="请输入作者昵称/unique/uid/签名"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-select v-model="listQuery.is_special" placeholder="请选择特别关注" clearable class="filter-item">
                <el-option key="全部" label="全部" :checked="-1 == listQuery.is_special" value="-1" />
                <el-option key="是" label="是" :checked="1 == listQuery.is_special" value="1" />
                <el-option key="否" label="否" :checked="0 == listQuery.is_special" value="0" />
            </el-select>
            <el-select v-model="listQuery.is_sync" placeholder="请选择同步状态" clearable class="filter-item">
                <el-option
                    v-for="item in calendarCheckOptions"
                    :key="item.key"
                    :checked="item.key == listQuery.is_sync"
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
            <el-table-column show-overflow-tooltip type="selection"/>
            <el-table-column
                show-overflow-tooltip
                prop="author_id"
                label="Id"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="nick_name"
                label="昵称"
                align="center"
            />
            <el-table-column show-overflow-tooltip align="center" label="头像">
                <template slot-scope="{row}">
                    <img v-if="row.avatar_thumb" :src="row.avatar_thumb">
                </template>
            </el-table-column>
            <el-table-column
                show-overflow-tooltip
                prop="signature"
                label="签名"
                align="center"
            />
            <el-table-column
                label="相关标识"
                align="left"
                width="200px"
            >
                <template v-slot="{row}">
                    <p>
                        <text-overflow maxWidth="55px" content='sec_uid:'></text-overflow>
                        <text-overflow maxWidth="50px" :content='row.sec_uid'></text-overflow>
                        <el-button type="primary" size="small" @click='handleCopy(row.sec_uid, $event)'>复制</el-button>
                    </p>
                    <p>uid: <span>{{row.uid}}</span></p>
                    <p>unique_id: <span>{{row.unique_id}}</span></p>
                    <p>分享链接: <span>{{row.share_url}}</span></p>
                    <p>获赞数: <span>{{row.total_favorited}}</span></p>
                    <p>作者关注: <span>{{row.follow_count}}</span></p>
                    <p>作者粉丝: <span>{{row.fan_count}}</span></p>
                </template>
            </el-table-column>
            <el-table-column label="上一次同步时间" show-overflow-tooltip align="center">
                <template slot-scope="{ row }">
                    {{ row.last_sync | parseTime("{y}-{m}-{d} {h}:{i}") }}
                </template>
            </el-table-column>
            <el-table-column align="center" label="是否启用同步">
                <template slot-scope="{row}">
                    <el-tag :type="row.is_sync | statusFilter">
                        <i :class="row.is_sync == 1 ? 'el-icon-unlock' : 'el-icon-lock'" />
                        {{ row.is_sync == 1 ? '是' : '否' }}
                    </el-tag>
                </template>
            </el-table-column>
            <el-table-column align="center" label="是否特别关注">
                <template slot-scope="{row}">
                    <el-tag :type="row.is_special | statusFilter">
                        <i :class="row.is_special == 1 ? 'el-icon-unlock' : 'el-icon-lock'" />
                        {{ row.is_special == 1 ? '是' : '否' }}
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

                    <!-- 特别关注 -->
                    <el-button v-if="row.is_special == 0" type="text"
                               @click="changeStatus(row, 1, 'is_special')">
                        <el-tag :type="1 | statusFilter">
                            <i class="el-icon-unlock"/>
                            设置`特别关注`
                        </el-tag>
                    </el-button>
                    <el-button v-else-if="row.is_special == 1" type="text"
                               @click="changeStatus(row, 0, 'is_special')">
                        <el-tag :type="0 | statusFilter">
                            <i class="el-icon-lock"/>
                            移出`特别关注`
                        </el-tag>
                    </el-button>

                    <el-button type="text" @click="syncVideosByAuthor(row)">
                        <el-tag type="success">
                            <i icon="el-icon-refresh"/>
                            同步`作品`
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
    import {getList, syncAuthor, syncAuthorVideos, changeFiledStatus} from '@/api/video-manage/author.js';

    import waves from '@/directive/waves'; // waves directive
    import Edit from './components/detail';
    import {parseTime, getFormatDate} from '@/utils/index';
    import clip from '@/utils/clipboard' // use clipboard directly

    import TextOverflow from '@/components/TextOverflow/index';

    const calendarCheckOptions = [
        {key: '-1', display_name: '全部'},
        {key: '1', display_name: '同步'},
        {key: '0', display_name: '关闭'}
    ];

    const calendarCheckKeyValue = calendarCheckOptions.reduce((acc, cur) => {
        acc[cur.key] = cur.display_name
        return acc
    }, {});

    export default {
        name: 'video-author-manage',
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
                    is_special: '', // 是否特别关注
                    is_sync: '', // 是否同步
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
