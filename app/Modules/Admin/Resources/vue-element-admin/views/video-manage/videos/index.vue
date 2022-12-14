<template>
    <div class="app-container">
        <div class="filter-container">
            <el-input
                v-model="listQuery.search"
                placeholder="请输入作品描述"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-input
                v-model="listQuery.author_search"
                placeholder="请输入作者 昵称/unique/uid/sec_uid"
                style="width: 350px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />
            <el-input
                v-model="listQuery.author_id"
                placeholder="请输入作者Id"
                style="width: 200px;"
                class="filter-item"
                @keyup.enter.native="handleFilter"
            />

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


        <!-- gutter 属性来指定每一栏之间的间隔，默认间隔为 0 -->
        <!--  :style="{height: height}" -->
        <el-row :gutter="15" ref="lists" style="height: auto;min-height: 500px">
            <el-col v-for="item in list" :key="item.video_id" :span="item.video_type == 0 ? 6 : 12" ref="items">
                <el-card class="box-card">
                    <div slot="header" class="clearfix" style="height: 100%;">
                        <span>{{ item.desc }}</span>
                    </div>
                    <div class="component-item" v-if="item.video_type == 0">
                        <!-- controls：向用户显示播放按钮控件 -->
                        <!-- poster：封面图 -->
                        <!--  ref="video" -->
                        <video
                            :id="'video-'+item.video_id"
                            class="video-js vjs-default-skin vjs-big-play-centered mt10"
                            :poster="item.cover"
                            controls
                            style="width: 100%;max-width:500px;height: auto;"
                        >
                            <source :src="item.video.path" type="video/mp4" />
                        </video>
                    </div>
                    <div class="component-item" v-else>
                        <!--
                        <el-carousel :interval="carousel_time" type="card">
                            <el-carousel-item v-for="image in item.images" :key="image">
                                <img :src="image" style="width: 100%" />
                            </el-carousel-item>
                        </el-carousel>
                        -->
                        <el-carousel :interval="carousel_time" arrow="always" style="height: 100%;">
                            <el-carousel-item v-for="image in item.images" :key="image">
                                <img :src="image" style="height:100%;" />
                            </el-carousel-item>
                        </el-carousel>
                    </div>
                </el-card>
            </el-col>
        </el-row>


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

<style lang="scss" scoped>
    .el-col{
        /*height: auto;*/
        height: 750px;
        margin-bottom: 20px;
    }
    .el-carousel__item h3 {
        color: #475669;
        font-size: 14px;
        opacity: 0.75;
        line-height: 200px;
        margin: 0;
    }
    .el-card div.clearfix{
        height: 37px;

        overflow:hidden;
        text-overflow:ellipsis;
        display:-webkit-box;
        -webkit-box-orient:vertical;
        -webkit-line-clamp:2;
    }
    .el-card,
    .el-card__body,
    .el-card__body .component-item,
    .el-carousel,
    .el-carousel__container
    {
        height: 100%;
    }

    .el-carousel__item{
        text-align: center;
    }

    .el-carousel__item:nth-child(2n) {
        background-color: #99a9bf;
    }

    .el-carousel__item:nth-child(2n+1) {
        background-color: #d3dce6;
    }
</style>

<script>
    import {getList} from '@/api/video-manage/videos.js';

    import waves from '@/directive/waves'; // waves directive
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
        components: {TextOverflow},
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
                    author_search: '',
                    author_id: '',
                    page: 1,
                    limit: 20,
                    enable: '',
                    search: '',
                    is_download: 0, // 是否下载：1.是；默认0
                },
                // 轮播图切换时长
                carousel_time: 5000,
                // 每几秒重新设置一次轮播图元素的高度
                carousel_time_authSetItemHeight: 1000,

                waterfallConfig: {
                    count: 4,
                    width: 0,
                },

                height: '500px',
                timer: null
            }
        },
        created() {
            this.getList();
        },
        // 在mounted中挂载
        mounted() {
            this.timer = setInterval(() => {
                this.authSetItemHeight();
            }, this.carousel_time_authSetItemHeight);


            // 监听窗口变动大小计算banner高度
            window.addEventListener("resize", () => {
                this.authSetItemHeight()
            });
        },
        // 在beforeDestroy（）中卸载，不然页面会报错，而且再次进入页面的时候，视频无法重新初始化
        beforeDestroy() {
            this.$once('hook:beforeDestroy',() =>{
                clearInterval(this.timer)
            })
        },
        methods: {
            // 获取图片的实际长度和宽度
            getImgAttr (url, callback) {
                let img = new Image()
                img.src = url
                if (img.complete) {
                    callback(img.width, img.height);
                    // console.log(img.width, img.height, 'img.width, img.height')
                } else {
                    img.onload = function () {
                        callback(img.width, img.height)
                        img.onload = null
                    }
                }
            },
            imgLoad() {
                this.$nextTick(function() {
                    // 定义页面初始的走马灯高度, 默认以第一张图片高度
                    if (this.bannerList.length) {
                        this.getImgAttr(this.bannerList[0].image, (width, height) => {
                            // 获取屏宽计算图片容器高度
                            let screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
                            this.imgHeight = height / width * screenWidth + 'px'
                            // console.log(this.imgHeight, 'this.imgHeight')
                        })
                    }
                });
            },
            // 自动设置轮播图的元素高度
            authSetItemHeight(){
                let card_bodys = document.querySelectorAll('.el-card__body');
                for (let i = 0; i < card_bodys.length; i++) {
                    card_bodys[i].style.height = '100%';
                }

                let carousel__containers = document.querySelectorAll('.el-carousel__container');
                for (let i = 0; i < carousel__containers.length; i++) {
                    carousel__containers[i].style.height = '100%';
                }
            },
            waterfall(){
                this.listLoading = true;

                console.log('--- waterfall ---');
                let lists_el = this.$refs['lists'].$el;
                // 获取列数
                let count = this.waterfallConfig.count;
                let imgWidth = this.$refs['lists'].$el.offsetWidth / count

                // 高度数组
                let heightArr = [];
                for(let i = 0; i< count; i++) {
                    heightArr[i] = 0;
                }

                //
                let max_top = 0, item_height = 0;

                // 遍历所有图片进行定位处理
                let itemHeight, minHeight, minIndex, useHeight;
                lists_el.children.forEach(item => {
                    // 高度数组最小的高度
                    minHeight = Math.min(...heightArr);
                    // 高度数组最小的高度的索引
                    minIndex = heightArr.indexOf(minHeight);

                    // 设置属性
                    item.style.position = 'absolute'
                    // useHeight = minHeight > 0 ? (minHeight + 25) : minHeight;
                    useHeight = minHeight;
                    item.style.top =  useHeight + 'px'
                    item.style.left = minIndex * imgWidth + 'px'
                    item.style.paddingLeft = '5px';
                    item.style.paddingRight = '5px';

                    // 当前元素的高度
                    itemHeight = item.offsetHeight + 25;
                    heightArr[minIndex] += itemHeight;

                    // 更改常量配置
                    if (useHeight > max_top){
                        max_top = minHeight > itemHeight ? minHeight : itemHeight;
                    }
                    if (itemHeight > item_height){
                        item_height = itemHeight;
                    }
                });
                // 设置父级的高度~
                // console.log(lists_el);
                // console.log(max_top);
                // console.log(item_height);

                this.height = max_top + item_height + 50 + 'px';
                console.log(this.height);


                setTimeout(() => {
                    this.listLoading = false;
                }, 300);
            },
            handleCopy(text, event) {
                clip(text, event)
            },
            checkFilter(val) {
                return calendarCheckKeyValue[val] || '';
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

                    // this.waterfall();
                    this.authSetItemHeight();
                }, 300);
            },
        }
    }
</script>
