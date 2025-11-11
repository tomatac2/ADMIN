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
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'face_verification')) {
                    $table->boolean('face_verification')->default(false)->after('notifiable');
                }
                if (!Schema::hasColumn('users', 'last_face_image_id')) {
                    $table->unsignedBigInteger('last_face_image_id')->nullable()->after('profile_image_id');
                }
                if (!Schema::hasColumn('users', 'face_verified_at')) {
                    $table->timestamp('face_verified_at')->nullable()->after('last_face_image_id');
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'face_verified_at')) $table->dropColumn('face_verified_at');
            if (Schema::hasColumn('users', 'last_face_image_id')) {
                $table->dropColumn('last_face_image_id');
            }
            if (Schema::hasColumn('users', 'face_verification')) $table->dropColumn('face_verification');

        });
    }
};
