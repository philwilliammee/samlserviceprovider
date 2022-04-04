<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSamlLogins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saml_logins', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('saml_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('xml_string')->nullable();
            $table->timestamp('not_on_or_after')->nullable();
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
        Schema::dropIfExists('saml_logins');
    }
}
