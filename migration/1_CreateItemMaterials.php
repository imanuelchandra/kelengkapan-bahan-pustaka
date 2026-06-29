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
            $table->tinynumber('property_stamp')->nullable(); 
            $table->tinynumber('inventory_stamp')->nullable(); 
            $table->tinynumber('barcode')->nullable(); 
            $table->tinynumber('book_pocket')->nullable(); 
            $table->tinynumber('book_card')->nullable(); 
            $table->tinynumber('catalog_card')->nullable(); 
            $table->tinynumber('book_label')->nullable(); 
            $table->tinynumber('date_due_slip')->nullable(); 
            $table->timestamps();
            $table->engine = 'MyISAM';
        });

    }

    function down()
    {
        Schema::drop('item_materials');
    }
}