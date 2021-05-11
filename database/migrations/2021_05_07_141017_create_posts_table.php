<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wp_post_id')->unsigned();
            $table->text('post_title');
            $table->text('post_excerpt')->nullable();
            $table->longText('post_content');
            $table->string('featured_image')->nullable();
            $table->text('categories')->nullable();
            $table->text('tags')->nullable();
            $table->unique(array('wp_post_id','id'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
