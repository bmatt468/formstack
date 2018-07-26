<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title')->nullable()->comment('The title of the document.');
            $table->unsignedInteger('creator_id')->comment('The user who created this document.');
            $table->unsignedInteger('last_modifier_id')->nullable()->comment('The user who last edited this document.');
            $table->unsignedInteger('last_exporter_id')->nullable()->comment('The user who last exported this document.');
            $table->timestamps();
            $table->datetime('last_export')->nullable();
            $table->softDeletes();

            $table->foreign('creator_id')->references('id')->on('users');
            $table->foreign('last_modifier_id')->references('id')->on('users');
            $table->foreign('last_exporter_id')->references('id')->on('users');
        });

        Schema::create('data', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('document_id')->comment('The document associated with this data.');
            $table->string('key')->nullable();
            $table->string('type')->default('string')->nullable();
            $table->binary('value')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')
                ->references('id')
                ->on('documents')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        Schema::create('exports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('The user who created this export.');
            $table->unsignedInteger('document_id')->comment('The document associated with this export.');
            $table->string('format');
            $table->string('service');
            $table->text('url');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('data');
        Schema::dropIfExists('exports');
    }
}
