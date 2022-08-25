<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export', function (Blueprint $table) {
            $table->id('export_id');
            $table->date('tanggal');
            $table->string('penginput', 200);
            $table->string('no_transaksi', 200);
            $table->string('nm_pembeli', 200);
            $table->text('alamat');
            $table->string('kota_kab', 200);
            $table->string('provinsi', 200);
            $table->string('no_hp', 200);
            $table->string('sku_id', 200);
            $table->string('nm_barang', 200);
            $table->string('kategori', 200);
            $table->string('size', 200);
            $table->double('qty');
            $table->string('toko', 200);
            $table->text('keterangan')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('export');
    }
};
