<?php
use SLiMS\Migration\Migration;
use SLiMS\Table\Schema;
use SLiMS\Table\Blueprint;

class CreateItemMaterials extends Migration
{
    function up()
    {
        Schema::create('item_materials', function(Blueprint $table) {
            $table->autoIncrement('id');
            $table->tinynumber('property_stamp')->default(0);
            $table->tinynumber('inventory_stamp')->default(0);
            $table->tinynumber('barcode')->default(0);
            $table->tinynumber('book_pocket')->default(0);
            $table->tinynumber('book_card')->default(0);
            $table->tinynumber('catalog_card')->default(0);
            $table->tinynumber('book_label')->default(0);
            $table->tinynumber('date_due_slip')->default(0);
            $table->timestamps();
            $table->engine = 'MyISAM';
        });

    }

    function down()
    {
         //Schema::drop('item_materials');
    }
}