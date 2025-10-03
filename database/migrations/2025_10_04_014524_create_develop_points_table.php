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
        Schema::create('develop_points', function (Blueprint $table) {
            $table->id();
            $table->string('member_notion_id');
            $table->date('in_review_date');
            $table->integer('point');
            $table->integer('target');
            $table->foreign('member_notion_id')->references('notion_id')->on('members')->onDelete('cascade');
            $table->unique(['member_notion_id', 'in_review_date'], 'unique_member_review_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('develop_points');
    }
};
