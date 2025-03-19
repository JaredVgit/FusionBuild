<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->text('amount'); // Changed from decimal to text for encryption
            $table->foreignId('released_by')->constrained('accounts')->onDelete('cascade');
            $table->date('date');
            $table->string('attachment')->nullable();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->enum('status', ['active', 'removed'])->default('active');
            $table->foreignId('removed_by')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->date('date_removed')->nullable();
            $table->timestamps();
        });
    }    

    public function down() {
        Schema::dropIfExists('expenses');
    }
};
