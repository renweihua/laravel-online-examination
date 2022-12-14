<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input
                v-model="listQuery.search"
                placeholder="请输入私服账户"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
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
            border
            class="margin-buttom-10"
        >
            <el-table-column
                show-overflow-tooltip
                prop="account"
                label="账户"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="gold_coin"
                label="金元宝"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="silver_coin"
                label="银元宝"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="name"
                label="姓名"
                align="center"
            />
            <el-table-column align="center" label="交易锁状态">
                <template slot-scope="{row}">
                    <div v-if="row.auto_lock != ''">
                        <p>自动锁定时间：{{ row.auto_lock }}</p>
                        <el-button type="text" icon="el-icon-unlock" @click="cancelAutoLockButton(row)">解除自动锁定</el-button>
                    </div>
                    <div v-if="row.trade_lock_time != ''">
                        <p>交易锁定时间：{{ row.trade_lock_time }}</p>
                        <el-button type="text" icon="el-icon-unlock" @click="cancelTradeLockButton(row)">解除交易锁定</el-button>
                    </div>
                </template>
            </el-table-column>
            <el-table-column align="center" prop="blocked_time" label="黑名单状态">
                <template slot-scope="{row}">
                    <el-tag :type="row.blocked_time == '' ? 'success' : 'danger'">
                        <i :class="row.blocked_time == '' ? 'el-icon-unlock' : 'el-icon-lock'"/>
                        {{ row.blocked_time == '' ? '正常' : '已拉黑' }}
                    </el-tag>
                    <p v-if="row.blocked_time != ''">{{ row.blocked_reason }}</p>
                </template>
            </el-table-column>
            <el-table-column
                show-overflow-tooltip
                prop="create_time"
                label="创建时间"
                align="center"
            />
            <el-table-column
                show-overflow-tooltip
                prop="update_time"
                label="更新时间"
                align="center"
            />
            <el-table-column
                fixed="right"
                label="操作"
                align="center"
            >
                <template v-slot="{row}">
                    <!-- 编辑 -->
                    <el-button type="text" icon="el-icon-edit" @click="handleEdit(row)">编辑</el-button>

                    <!-- 更改密码 -->
                    <el-button type="text" icon="el-icon-edit" @click="startUpdatePass(row)">更改密码</el-button>

                    <!-- 重置checknum效验字段 -->
                    <el-button type="text" icon="el-icon-refresh" @click="resetChecknumButton(row)">重置checknum</el-button>

                    <!-- 元宝充值 -->
                    <el-button type="text" icon="el-icon-coin" @click="startRecharge(row)">元宝充值</el-button>

                    <!-- 拉黑账户 -->
                    <el-button v-if="row.blocked_time == ''" type="text" icon="el-icon-lock" @click="startSetBlocked(row)">拉黑账户</el-button>

                    <!-- 取消拉黑账户 -->
                    <el-button v-else type="text" icon="el-icon-unlock" @click="cancelAccountBlocked(row)">取消拉黑</el-button>
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

        <el-dialog title="更改登录密码" :visible.sync="updatePassDialogFormVisible">
          <el-form :model="updatePassForm" label-width="80px">
            <el-form-item label="账户">
              <el-input v-model="updatePassForm.account" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item label="登录密码">
              <el-input placeholder="请输入登录密码" v-model="updatePassForm.password" show-password autocomplete="off"></el-input>
            </el-form-item>
          </el-form>
          <div slot="footer" class="dialog-footer">
            <el-button @click="updatePassDialogFormVisible = false">取 消</el-button>
            <el-button type="primary" @click="updateAccountPass">确 定</el-button>
          </div>
        </el-dialog>

        <el-dialog title="元宝充值" :visible.sync="rechargeDialogFormVisible">
          <el-form :model="rechargeForm" label-width="120px">
            <el-form-item label="账户">
              <el-input v-model="rechargeForm.account" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item label="元宝种类">
                <el-select v-model="rechargeForm.coin_type" placeholder="请选择元宝种类">
                    <el-option
                      v-for="item in recharge_options"
                      :key="item.value"
                      :label="item.label"
                      :value="item.value">
                    </el-option>
                </el-select>
            </el-form-item>
            <el-form-item label="元宝数量">
              <el-input placeholder="请输入元宝数量" v-model="rechargeForm.money" type="number" maxlength="10" autocomplete="off"></el-input>
            </el-form-item>
          </el-form>
          <div slot="footer" class="dialog-footer">
            <el-button @click="rechargeDialogFormVisible = false">取 消</el-button>
            <el-button type="primary" v-if="recharge_load == false" @click="accountRecharge">确 定</el-button>
            <el-button type="info" v-else>充值中……</el-button>
          </div>
        </el-dialog>

        <el-dialog title="拉黑账户" :visible.sync="setBlockedDialogFormVisible">
          <el-form :model="setBlockedForm" label-width="120px">
            <el-form-item label="账户">
              <el-input v-model="setBlockedForm.account" :disabled="true"></el-input>
            </el-form-item>
            <el-form-item label="请输入拉黑原因">
              <el-input placeholder="请输入拉黑原因" v-model="setBlockedForm.blocked_reason" autocomplete="off"></el-input>
            </el-form-item>
          </el-form>
          <div slot="footer" class="dialog-footer">
            <el-button @click="setBlockedDialogFormVisible = false">取 消</el-button>
            <el-button type="primary" @click="setBlockedButton">确 定</el-button>
          </div>
        </el-dialog>
    </div>
</template>

<script>
    import {getList, updatePasswordByAccount, resetChecknum, setBlocked, cancelBlocked, rechargeCoin, cancelAutoLock, cancelTradeLock} from '@/api/asktao/accounts.js';

    import waves from '@/directive/waves'; // waves directive
    import Edit from './components/detail';
    import {parseTime, getFormatDate} from '@/utils/index';

    export default {
        name: 'friendlinkManage',
        components: {Edit},
        directives: {waves},
        filters: {
            parseTime: parseTime,
            getFormatDate: getFormatDate,
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
                    is_download: 0, // 是否下载：1.是；默认0
                },

                // 更改登录密码
                updatePassForm: {
                    account: '',
                    password: '',
                },
                updatePassDialogFormVisible: false,

                // 充值
                rechargeDialogFormVisible: false,
                // 验证充值接口是否正在请求中
                recharge_load: false,
                rechargeForm: {
                    account: '',
                    coin_type: 1,
                    money: '',
                },
                recharge_options: [
                    {
                        value: 1,
                        label: '金元宝'
                    },
                    {
                        value: 2,
                        label: '银元宝'
                    }
                ],

                // 拉黑账户
                setBlockedDialogFormVisible: false,
                // 当前操作拉黑的row
                setblockedRow:{},
                setBlockedForm: {
                    account: '',
                    blocked_reason: '',
                },
            }
        },
        created() {
            this.getList();
        },
        methods: {
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
            // 开启`更改登录密码`的界面
            startUpdatePass(row) {
                this.updatePassDialogFormVisible = true;
                this.updatePassForm.account = row.account;
            },
            // 更改登录密码
            async updateAccountPass(){
                const {msg, status} = await updatePasswordByAccount(this.updatePassForm);

                if (status == 1){
                    this.updatePassDialogFormVisible = false;
                    this.updatePassForm.account = '';
                    this.updatePassForm.password = '';
                }

                this.$message({
                    message: msg,
                    type: status == 1 ? 'success' : 'error',
                });
            },
            // 重置checknum效验字段
            async resetChecknumButton(row){
                this.$confirm(
                    '你确定要重置账户{' + row.account + '}的checknum效验字段吗？重置之后将无法恢复，请谨慎操作',
                    '账户{' + row.account + '}，重置checknum效验字段',
                    {
                        confirmButtonText: '立即重置',
                        cancelButtonText: '取消',
                        type: 'warning'
                    })
                    .then(async () => {
                        const {msg, status} = await resetChecknum({account:row.account});

                        this.$message({
                            message: msg,
                            type: status == 1 ? 'success' : 'error',
                        });
                    })
                    .catch(err => {
                        console.error(err);
                    });
            },
            // 开启`拉黑账户`的界面
            startSetBlocked(row) {
                this.setblockedRow = row;
                this.setBlockedDialogFormVisible = true;
                this.setBlockedForm.account = row.account;
            },
            // 拉黑账户
            async setBlockedButton(){
                this.$confirm(
                    '你确定要将账户{' + this.setBlockedForm.account + '}拉入黑名单吗？拉黑之后将无法登录，请谨慎操作！',
                    '拉黑{' + this.setBlockedForm.account + '}账户',
                    {
                        confirmButtonText: '立即拉黑',
                        cancelButtonText: '取消',
                        type: 'warning'
                    })
                    .then(async () => {
                        const {msg, status} = await setBlocked(this.setBlockedForm);

                        if (status == 1){
                            this.setBlockedDialogFormVisible = false;

                            this.setBlockedForm.account = '';
                            this.setBlockedForm.blocked_reason = '';
                            // 标记`已拉黑`
                            this.setblockedRow.blocked_time = 1;
                        }
                        this.$message({
                            message: msg,
                            type: status == 1 ? 'success' : 'error',
                        });
                    })
                    .catch(err => {
                        console.error(err);
                    });
            },
            // 账户取消拉黑
            async cancelAccountBlocked(row){
                const {msg, status} = await cancelBlocked({account: row.account});

                if (status == 1){
                    this.updatePassDialogFormVisible = false;
                    // 标记`正常`
                    row.blocked_time = '';
                }
                this.$message({
                    message: msg,
                    type: status == 1 ? 'success' : 'error',
                });
            },
            // 开启`元宝充值`的界面
            startRecharge(row) {
                this.rechargeDialogFormVisible = true;
                this.rechargeForm.account = row.account;
            },
            // 账户充值
            accountRecharge(){
                if(!this.rechargeForm.money){
                    this.$message({
                        message: '请输入充值的元宝数量',
                        type:'error',
                    });
                    return false;
                }
                if(this.recharge_load){
                    this.$message({
                        message: '正在充值请求中，请耐心等待！',
                        type:'error',
                    });
                    return false;
                }
                this.$confirm(
                    '你确定要对账户{' + this.rechargeForm.account + '}充值' + this.rechargeForm.money + (this.rechargeForm.coin_type == 1 ? '金' : '银') + '元宝吗？充值之后将无法恢复，请谨慎操作！',
                    '账户{' + this.rechargeForm.account + '}元宝充值',
                    {
                        confirmButtonText: '立即充值',
                        cancelButtonText: '取消',
                        type: 'warning'
                    })
                    .then(async () => {
                        // 接口请求中的标识
                        this.recharge_load = true;
                        const {msg, status} = await rechargeCoin(this.rechargeForm);

                        if (status == 1){
                            this.rechargeDialogFormVisible = false;
                            // 清空账户
                            this.rechargeForm.account = '';
                            // 重置元宝种类
                            this.rechargeForm.coin_type = 1;
                            // 数量
                            this.rechargeForm.money = '';

                            // 重新加载当前页面数据
                            this.getList();
                        }
                        this.$message({
                            message: msg,
                            type: status == 1 ? 'success' : 'error',
                        });
                        // 解除锁定
                        this.recharge_load = false;
                    })
                    .catch(err => {
                        console.error(err);
                    });
            },
            // 解除自动锁定
            async cancelAutoLockButton(row){
                this.$confirm(
                    '你确定要解除账户{' + row.account + '}的`自动锁定`吗？操作之后将无法恢复，请谨慎操作！',
                    '解除{' + row.account + '}账户的`自动锁定`',
                    {
                        confirmButtonText: '立即解除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    })
                    .then(async () => {
                        const {msg, status} = await cancelAutoLock({account: row.account});

                        if (status == 1){
                            // 标记 解除`自动锁定`成功
                            row.auto_lock = '';
                        }
                        this.$message({
                            message: msg,
                            type: status == 1 ? 'success' : 'error',
                        });
                    })
                    .catch(err => {
                        console.error(err);
                    });
            },
            // 解除交易锁定
            async cancelTradeLockButton(row){
                this.$confirm(
                    '你确定要解除账户{' + row.account + '}的`交易锁定`吗？操作之后将无法恢复，请谨慎操作！',
                    '解除{' + row.account + '}账户的`交易锁定`',
                    {
                        confirmButtonText: '立即解除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    })
                    .then(async () => {
                        const {msg, status} = await cancelTradeLock({account: row.account});

                        if (status == 1){
                            // 标记 解除`交易锁定`成功
                            row.trade_lock_time = '';
                        }
                        this.$message({
                            message: msg,
                            type: status == 1 ? 'success' : 'error',
                        });
                    })
                    .catch(err => {
                        console.error(err);
                    });
            },
        }
    }
</script>
