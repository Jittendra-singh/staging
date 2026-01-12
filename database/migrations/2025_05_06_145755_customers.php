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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('AUTO_INCREMENT');
            $table->unsignedBigInteger('referral_id')->nullable();
            $table->string('name', 255)->collation('utf8mb4_general_ci')->nullable();
            $table->string('phone', 25)->collation('utf8mb4_general_ci')->nullable();
            $table->string('sec_phone', 20)->collation('utf8mb4_general_ci')->nullable();
            $table->string('email', 255)->collation('utf8mb4_general_ci')->nullable();
            $table->text('photo')->collation('utf8mb4_general_ci')->nullable();
            $table->text('driving_licence_photo')->collation('utf8mb4_general_ci')->nullable();
            $table->text('aadhar_front_photo')->collation('utf8mb4_general_ci')->nullable();
            $table->text('aadhar_back_photo')->collation('utf8mb4_general_ci')->nullable();
            $table->string('dl_number', 255)->collation('utf8mb4_general_ci')->nullable();
            $table->string('aadhar_number', 255)->collation('utf8mb4_general_ci')->nullable();
            $table->tinyInteger('is_phone_verified')->default(0);
            $table->tinyInteger('is_dl_verified')->default(0);
            $table->tinyInteger('is_aadhar_verified')->default(0);
            $table->string('phone_verify_otp', 10)->collation('utf8mb4_general_ci')->nullable();
            $table->date('email_verified_at')->nullable();
            $table->text('address')->collation('utf8mb4_general_ci')->nullable();
            $table->string('city', 100)->collation('utf8mb4_general_ci')->nullable();
            $table->string('state', 100)->collation('utf8mb4_general_ci')->nullable();
            $table->string('country', 100)->collation('utf8mb4_general_ci')->nullable();
            $table->string('zipcode', 25)->collation('utf8mb4_general_ci')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->string('login_otp', 10)->collation('utf8mb4_general_ci')->nullable();
            $table->dateTime('login_otp_created_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
