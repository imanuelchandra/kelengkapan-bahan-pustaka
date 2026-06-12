<?php
use SLiMS\Migration\Migration;
use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class CreateMemberCollege extends Migration
{
    function up()
    {
        Schema::create('member_college', function(Blueprint $table) {
            $table->autoIncrement('id');
            $table->string('study_program', 3)->notNull();
            $table->string('class_year', 4)->notNull();
            $table->timestamps();
            $table->engine = 'MyISAM';
        });

    }

    function down()
    {
         Schema::drop('member_college');
    }
}