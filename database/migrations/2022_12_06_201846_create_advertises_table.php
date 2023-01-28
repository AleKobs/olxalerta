<?php

use App\Models\Url;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertises', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Url::class);
            $table->string('title');
            $table->string('url');
            $table->string('price_str')->nullable();
            $table->float('price')->nullable();
            $table->datetime('published_at')->nullable();
            $table->string('image')->nullable();
            $table->datetime('alert_date')->nullable();
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
        Schema::dropIfExists('advertises');
    }
};
