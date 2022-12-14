<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'students';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('student_id')->unsigned()->comment('Id');
            $table->string('student_no', 20)->default('')->comment('学号');
            $table->string('student_name', 200)->default('')->comment('姓名');
            $table->string('password', 200)->default('')->comment('登录密码');
            $table->string('student_avatar', 100)->default('')->comment('头像');
            $table->boolean('student_sex')->unsigned()->default(0)->comment('性别');
            $table->string('student_mobile', 20)->default('')->comment('手机号');
            $table->string('student_email', 100)->default('')->comment('邮箱');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('last_login_time')->unsigned()->default(0)->comment('上一次登录时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除：0.否；1.是');
            $table->index(['is_delete']);
            $table->index(['student_no']);
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '学生表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
