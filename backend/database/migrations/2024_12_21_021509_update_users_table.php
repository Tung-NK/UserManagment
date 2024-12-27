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
            $table->string('image', 225)->nullable()->after('updated_at');
            $table->string('phone', 225)->nullable()->after('image');
            $table->string('description', 225)->nullable()->after('phone');
            $table->string('address', 225)->nullable()->after('description');
            $table->date('birth_date')->nullable()->after('address');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birth_date');
            $table->string('nationality', 225)->nullable()->after('gender');
            $table->string('job_status', 225)->nullable()->after('nationality');
            $table->string('occupation', 225)->nullable()->after('job_status');
            $table->string('hobbies', 225)->nullable()->after('occupation');
            $table->enum('account_status', ['active', 'inactive', 'banned'])->nullable()->after('hobbies');
            $table->softDeletes()->after('account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'image',
                'phone',
                'description',
                'address',
                'birth_date',
                'gender',
                'nationality',
                'job_status',
                'occupation',
                'hobbies',
                'account_status',
                'deleted_at',
            ]);
        });
    }
};
