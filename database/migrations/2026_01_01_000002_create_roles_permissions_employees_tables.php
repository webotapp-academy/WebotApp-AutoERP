<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('group', 50)->default('general');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->after('role_id')->constrained('branches')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->after('branch_id')->constrained('companies')->nullOnDelete();
            $table->string('phone', 25)->nullable()->after('email');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('password');
            $table->string('avatar')->nullable()->after('status');
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('employee_code', 30)->unique();
            $table->string('designation');
            $table->string('department')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('emergency_contact', 50)->nullable();
            $table->decimal('salary', 12, 2)->default(0.00);
            $table->decimal('hourly_rate', 10, 2)->default(0.00);
            $table->decimal('commission_rate', 5, 2)->default(0.00);
            $table->enum('status', ['active', 'on_leave', 'resigned'])->default('active');
            $table->timestamps();
        });

        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('skill_name');
            $table->enum('proficiency', ['beginner', 'intermediate', 'expert'])->default('intermediate');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('employees');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['company_id']);
            $table->dropColumn(['role_id', 'branch_id', 'company_id', 'phone', 'status', 'avatar']);
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

