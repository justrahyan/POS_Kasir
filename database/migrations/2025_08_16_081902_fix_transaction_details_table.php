<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            // Cek apakah foreign key 'transaction_details_product_id_foreign' ada sebelum menghapusnya
            $foreignKeys = collect(DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'transaction_details' AND REFERENCED_TABLE_NAME = 'products'"))->pluck('CONSTRAINT_NAME');

            if ($foreignKeys->contains('transaction_details_product_id_foreign')) {
                $table->dropForeign('transaction_details_product_id_foreign');
            }

            // Tambahkan kolom baru hanya jika belum ada
            if (!Schema::hasColumn('transaction_details', 'product_name')) {
                $table->string('product_name')->after('product_id')->nullable();
            }
            if (!Schema::hasColumn('transaction_details', 'product_image')) {
                $table->string('product_image')->after('product_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'product_image']);
            $table->foreign('product_id')->references('id')->on('products');
            $table->decimal('price', 15, 2)->change(); // change back to original definition if needed
        });
    }
};
