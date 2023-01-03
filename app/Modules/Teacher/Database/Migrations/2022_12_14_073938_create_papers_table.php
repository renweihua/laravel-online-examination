<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'papers';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('paper_id')->unsigned()->comment('Id');
            $table->integer('teacher_id')->unsigned()->default(0)->comment('教师Id');
            $table->integer('course_id')->unsigned()->default(0)->comment('课程Id');
            $table->string('paper_name', 200)->default('')->comment('试卷名称');
            $table->integer('paper_duration')->unsigned()->default(0)->comment('考试时长(秒)：0.不限时');
            $table->boolean('paper_difficulty')->unsigned()->default(0)->comment('考试难度');
            $table->string('paper_attention', 200)->default('')->comment('考试注意事项');
            $table->boolean('paper_mechanism')->unsigned()->default(0)->comment('组卷机制：0.随机；1.固定');
            $table->string('paper_config', 500)->default('')->comment('考试题目对应分数的配置');
            $table->integer('student_count')->unsigned()->default(0)->comment('已参加学生数量');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除：0.否；1.是');
            $table->index(['course_id']);
            $table->index(['teacher_id']);
            $table->index(['is_delete']);
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '试卷表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('papers');
    }
}
