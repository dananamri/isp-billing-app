<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_profiles', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('price')->default(0);
            $table->string('validity', 30)->nullable(); // e.g. 1h, 3h, 1d, 30d
            $table->string('rate_limit', 50)->nullable(); // e.g. 2M/2M
            $table->unsignedSmallInteger('shared_users')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active'], 'voucher_profiles_tenant_active_idx');
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('profile_id')->nullable()->constrained('voucher_profiles')->nullOnDelete();
            $table->string('code', 50);
            $table->string('password', 50)->nullable();
            $table->string('status', 20)->default('unused'); // unused, used, expired
            $table->string('batch_code', 50)->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'code'], 'vouchers_tenant_code_unique');
            $table->index(['tenant_id', 'status'], 'vouchers_tenant_status_idx');
            $table->index(['tenant_id', 'batch_code'], 'vouchers_tenant_batch_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('voucher_profiles');
    }
};
