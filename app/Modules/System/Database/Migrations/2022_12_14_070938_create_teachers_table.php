<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = 'teachers';
        if (Schema::hasTable($table)) return;
        Schema::create($table, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('teacher_id')->unsigned()->comment('Id');
            $table->string('teacher_mobile', 20)->default('')->comment('手机号');
            $table->string('teacher_email', 100)->default('')->comment('邮箱');
            $table->string('teacher_name', 200)->default('')->comment('姓名');
            $table->string('password', 200)->default('')->comment('登录密码');
            $table->boolean('teacher_sex')->unsigned()->default(0)->comment('性别');
            $table->integer('created_time')->unsigned()->default(0)->comment('创建时间');
            $table->integer('updated_time')->unsigned()->default(0)->comment('更新时间');
            $table->integer('last_login_time')->unsigned()->default(0)->comment('上一次登录时间');
            $table->boolean('is_delete')->unsigned()->default(0)->comment('是否删除：0.否；1.是');
            $table->index(['is_delete']);
            $table->index(['teacher_mobile']);
            $table->index(['teacher_email']);
            $table->index(['teacher_name']);
        });
        $table = get_db_prefix() . $table;
        // 设置表注释
        DB::statement("ALTER TABLE `{$table}` comment '教师表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teachers');
    }
}
