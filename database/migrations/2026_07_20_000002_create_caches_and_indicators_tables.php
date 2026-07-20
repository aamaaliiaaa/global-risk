<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('title', 500);
            $table->string('source')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('url', 1000)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('sentiment')->default('Neutral'); // Positive, Neutral, Negative
            $table->integer('positive_count')->default(0);
            $table->integer('negative_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('weather_cache', function (Blueprint $table) {
            $table->id();
            $table->string('weatherable_type');
            $table->unsignedBigInteger('weatherable_id');
            $table->double('temperature');
            $table->double('wind_speed');
            $table->integer('weather_code');
            $table->string('condition');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['weatherable_type', 'weatherable_id']);
        });

        Schema::create('currency_cache', function (Blueprint $table) {
            $table->id();
            $table->string('currency_code', 10)->unique();
            $table->double('rate'); // against USD
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('country_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->double('gdp')->nullable(); // in USD
            $table->double('inflation')->nullable(); // % rate
            $table->bigInteger('population')->nullable();
            $table->double('exports')->nullable(); // in USD
            $table->double('imports')->nullable(); // in USD
            $table->timestamps();
            $table->unique(['country_id', 'year']);
        });

        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->double('weather_risk')->default(0);
            $table->double('inflation_risk')->default(0);
            $table->double('currency_risk')->default(0);
            $table->double('news_sentiment_risk')->default(0);
            $table->double('total_risk')->default(0);
            $table->timestamps();
            $table->unique(['country_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
        Schema::dropIfExists('country_indicators');
        Schema::dropIfExists('currency_cache');
        Schema::dropIfExists('weather_cache');
        Schema::dropIfExists('news_cache');
    }
};
