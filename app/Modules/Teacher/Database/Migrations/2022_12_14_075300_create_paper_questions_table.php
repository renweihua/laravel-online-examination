<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'paper_questions';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned()->comment('Id');
            $table->integer('paper_id')->unsigned()->default(0)->comment('试卷Id');
            $table->integer('question_id')->unsigned()->default(0)->comment('问题Id');
            $table->index(['paper_id']);
            $table->index(['question_id']);
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '试卷题库表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paper_questions');
    }
}
