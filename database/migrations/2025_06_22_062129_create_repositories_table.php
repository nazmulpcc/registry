<?php

use App\Enums\Status;
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
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')
                ->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('name');
            $table->string('fqn')->unique()
                ->comment('Fully Qualified Name including the owner username & repository name');
            $table->string('status', 20)
                ->default(Status::Active);
            $table->timestamps();
        });

        Schema::create('repository_user', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('repository_id')
                ->constrained('repositories')->cascadeOnUpdate()->cascadeOnDelete();
            $table->json('permissions'); // ['read', 'write', 'admin']
            $table->timestamps();
            $table->primary(['user_id', 'repository_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};
