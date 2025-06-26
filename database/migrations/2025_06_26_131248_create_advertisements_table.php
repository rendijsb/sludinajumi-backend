<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['draft', 'pending', 'active', 'expired', 'rejected'])->default('draft');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained();
            $table->string('location')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('featured_until')->nullable();
            $table->integer('views_count')->default(0);
            $table->boolean('is_negotiable')->default(false);
            $table->timestamps();

            $table->index(['status', 'expires_at']);
            $table->index(['category_id', 'status']);
            $table->index('featured_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
