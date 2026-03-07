<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('social_connections')
            ->whereNotIn('user_id', function ($query) {
                $query->select('id')->from('users');
            })
            ->delete();

        DB::table('social_connections')
            ->whereNotIn('provider_id', function ($query) {
                $query->select('id')->from('social_providers');
            })
            ->delete();

        Schema::table('social_connections', function (Blueprint $table) {
            $table->unsignedBigInteger('provider_id')->change();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('provider_id')
                ->references('id')->on('social_providers')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_connections', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['provider_id']);

            $table->unsignedInteger('user_id')->change();
            $table->unsignedInteger('provider_id')->change();
        });
    }
};
