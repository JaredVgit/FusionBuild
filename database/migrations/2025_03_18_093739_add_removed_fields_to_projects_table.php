<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('projects', function (Blueprint $table) {
            // Add 'removed' status to the existing 'status' enum
            $table->enum('status', ['pending', 'ongoing', 'completed', 'cancelled', 'removed'])->default('pending')->change();

            // Add new columns
            $table->date('date_removed')->nullable()->after('end_date');
            $table->foreignId('removed_by')->nullable()->after('date_removed')->constrained('accounts')->onDelete('set null');
        });
    }

    public function down() {
        Schema::table('projects', function (Blueprint $table) {
            // Remove the newly added columns
            $table->dropColumn('date_removed');
            $table->dropForeign(['removed_by']);
            $table->dropColumn('removed_by');

            // Revert 'status' column to the previous enum values
            $table->enum('status', ['pending', 'ongoing', 'completed', 'cancelled'])->default('pending')->change();
        });
    }
};
