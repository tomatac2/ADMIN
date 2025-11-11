<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations. test
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vehicle information fields
            if (!Schema::hasColumn('users', 'insurance_number')) {
                $table->string('insurance_number')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('users', 'insurance_company_name')) {
                $table->string('insurance_company_name')->nullable()->after('insurance_number');
            }
            
            // Document image IDs (attachments)
            if (!Schema::hasColumn('users', 'id_photo_id')) {
                $table->unsignedBigInteger('id_photo_id')->nullable()->after('profile_image_id');
            }
            if (!Schema::hasColumn('users', 'car_registration_photo_id')) {
                $table->unsignedBigInteger('car_registration_photo_id')->nullable()->after('id_photo_id');
            }
            if (!Schema::hasColumn('users', 'car_license_photo_id')) {
                $table->unsignedBigInteger('car_license_photo_id')->nullable()->after('car_registration_photo_id');
            }
            if (!Schema::hasColumn('users', 'car_interior_photo_id')) {
                $table->unsignedBigInteger('car_interior_photo_id')->nullable()->after('car_license_photo_id');
            }
            
            // Car exterior photos (4 sides) - stored as JSON array of attachment IDs
            if (!Schema::hasColumn('users', 'car_exterior_photos')) {
                $table->json('car_exterior_photos')->nullable()->after('car_interior_photo_id');
            }
            
            // Document verification status
            if (!Schema::hasColumn('users', 'documents_verified')) {
                $table->boolean('documents_verified')->default(false)->after('car_exterior_photos');
            }
            if (!Schema::hasColumn('users', 'documents_submitted_at')) {
                $table->timestamp('documents_submitted_at')->nullable()->after('documents_verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'insurance_number',
                'insurance_company_name',
                'id_photo_id',
                'car_registration_photo_id',
                'car_license_photo_id',
                'car_interior_photo_id',
                'car_exterior_photos',
                'documents_verified',
                'documents_submitted_at',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

