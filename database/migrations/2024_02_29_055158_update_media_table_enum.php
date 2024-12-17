<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ImageType;

class UpdateMediaTableEnum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->enum('type', ImageType::getValues())->default(ImageType::Image)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->enum('type', ['thumbnail', 'logo', 'banner', 'image', 'video', 'author','resume','secondaryBanner'])->default('image');
        });
    }
}
