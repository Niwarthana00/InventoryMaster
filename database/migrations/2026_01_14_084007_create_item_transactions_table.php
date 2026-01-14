<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade'); // Link to item
            $table->foreignId('user_id')->nullable()->constrained(); // Track who made the change (optional if not logged in context, but req says authenticated)
            $table->enum('type', ['add', 'deduct']); // Type of transaction
            $table->decimal('quantity', 10, 3); // Amount added or deducted
            $table->decimal('balance_after', 10, 3); // Stock balance after this transaction (Snapshot)
            $table->string('note')->nullable(); // Optional note for the transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_transactions');
    }
};
