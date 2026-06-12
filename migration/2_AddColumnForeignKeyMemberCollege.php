<?php
use SLiMS\Migration\Migration;
use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class AddColumnForeignKeyMemberCollege extends Migration
{
    function up()
    {

        Schema::table('member', function (Blueprint $table) {
            // This creates an 'user_id' column (UNSIGNED BIGINT) and automatically 
            // sets it as a foreign key to the 'id' column on the 'users' table.
            //$table->foreignId('user_id')->constrained()->nullable(); 
           
           
            $table->number('member_college_id');
            $table->nullable();
            //$table->default('0');
            //$table->index('item_material_id');
            $table->after('member_type_id');
            $table->add();
          
           
        });

    }

    function down()
    {

         Schema::table('member', function (Blueprint $table) {
            $table->drop('member_college_id');    // Drops the column
        });
    }
}