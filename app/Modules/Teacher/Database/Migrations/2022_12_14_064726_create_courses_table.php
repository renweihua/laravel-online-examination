<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'courses';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('course_id')->unsigned()->comment('Id');
            $table->integer('teacher_id')->unsigned()->default(0)->comment('教师Id');
            $table->string('course_name', 200)->default('')->comment('课程名称');
            $table->string('course_description', 200)->default('')->comment('课程简介/描述');
            $table->string('course_cover', 200)->default('')->comment('课程封面/图标');
            $table->boolean('is_recommend')->unsigned()->default(0)->comment('是否推荐：0.否；1.是');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('last_change_teacher')->unsigned()->default(0)->comment('上一次更新课程的教师Id');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除：0.否；1.是');
            $table->index(['teacher_id']);
            $table->index(['is_delete']);
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '课程表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
