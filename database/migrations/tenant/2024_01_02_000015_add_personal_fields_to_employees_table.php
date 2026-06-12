<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('phone');
            $table->string('national_id', 16)->nullable()->unique()->after('photo_path');
            $table->enum('gender', ['male', 'female'])->nullable()->after('national_id');
            $table->date('birth_date')->nullable()->after('gender');
            $table->text('address')->nullable()->after('birth_date');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['photo_path', 'national_id', 'gender', 'birth_date', 'address']);
        });
    }
};
