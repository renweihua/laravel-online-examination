<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'question_banks';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('question_id')->unsigned()->comment('Id');
            $table->integer('course_id')->unsigned()->default(0)->comment('课程Id');
            $table->integer('teacher_id')->unsigned()->default(0)->comment('教师Id');
            $table->boolean('question_type')->unsigned()->default(0)->comment('题库类型：0.单选；1.多选；2.填空；3.判断；4.简答题');
            $table->string('question_content', 200)->default('')->comment('问题内容');
            $table->string('question_images', 200)->default('')->comment('图片');
            $table->string('question_options', 200)->default('')->comment('选项列表');
            $table->string('question_answer', 200)->default('')->comment('问题答案');
            $table->string('answer_explain', 200)->default('')->comment('题目解析');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->boolean('compose_flag')->unsigned()->default(0)->comment('是否被组成试卷：0.否；1.是');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除：0.否；1.是');
            $table->index(['course_id']);
            $table->index(['teacher_id']);
            $table->index(['question_type']);
            $table->index(['is_delete']);
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '题库表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_banks');
    }
}
