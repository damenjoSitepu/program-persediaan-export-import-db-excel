<?php

namespace App\Http\Controllers;

use App\Models\Export;
use App\Models\History;
use App\Models\Product;
use App\Models\Stock;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Home extends Controller
{
    public function __construct()
    {
        // Check Expired
        checkExpired();
    }

    // Halaman Home
    public function index()
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');
        else

            // Data
            $data = [
                'title'     => 'Home',
                'product'   => DB::select("SELECT * FROM product ORDER BY product.product_id DESC")
            ];

        return view('home.index', $data);
    }

    // Halaman Master
    public function master()
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Data
        $data = [
            'title'     => 'Master',
            'product'   => Product::all()
        ];

        return view('master.index', $data);
    }

    // Proses tambah stok
    public function prosestambahstok($id = '', Request $request)
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Validasi
        $request->validate([
            'tanggal_transaksi'              => 'required',
            'penginput'              => 'required',
            'no_transaksi'              => 'required',
            'qty'              => 'required',
            'tanggal_exp'              => 'required'
        ]);

        // Dapatkan Data SKU Id
        $getProduct = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$id}'"))->first();

        if (!empty($getProduct)) {
            // Simpan Langsung Ke Database
            $stock = new Stock;

            $stock->penginput = $request->penginput;
            $stock->tanggal_transaksi = $request->tanggal_transaksi;
            $stock->no_transaksi = $request->no_transaksi;
            $stock->qty = $request->qty;
            $stock->tanggal_exp = $request->tanggal_exp;
            $stock->sku_id = $id;

            $stock->save();

            $history = new History;
            $history->type = '4';
            $history->created_at = changeMyIndoTimestamp();
            $history->updated_at = changeMyIndoTimestamp();
            $history->status = '1';
            $history->error_count = '0';

            $history->save();

            return redirect()->route('home')->with('messages', 'Stok Produk Berhasil Ditambahkan!');
        } else {
            return redirect()->route('home')->with('messages', 'Kode SKU Ini Tidak Terdaftar Sehingga Penambahan Stok Otomatis Dibatalkan!');
        }
    }

    // Halaman tambah stok
    public function tambahstok($id = '')
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Data
        $data = [
            'title'     => 'Home',
            'product'   => Product::where('sku_id', $id)->first()
        ];

        return view('home.tambah-stok', $data);
    }

    // Proses tambah stok
    public function proseskurangstok($id = '', Request $request)
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Validasi
        $request->validate([
            'tanggal'  => 'required',
            'penginput'  => 'required',
            'no_transaksi'  => 'required',
            'nm_pembeli'  => 'required',
            'toko'  => 'required',
            'alamat'  => 'required',
            'no_hp'     => 'required',
            'kota_kab'  => 'required',
            'provinsi'  => 'required',
            'qty'  => 'required',
            'keterangan' => 'required'
        ]);

        // Data
        $tanggal = $request->tanggal;
        $penginput = $request->penginput;
        $no_transaksi = $request->no_transaksi;
        $nm_pembeli = $request->nm_pembeli;
        $no_hp = $request->no_hp;
        $toko = $request->toko;
        $alamat = $request->alamat;
        $kota_kab = $request->kota_kab;
        $provinsi = $request->provinsi;
        $qty = $request->qty;
        $keterangan = $request->keterangan;

        // Periksa total stok 
        $getProduct = DB::select("SELECT * FROM stock WHERE stock.sku_id='{$id}' ORDER BY stock.tanggal_transaksi ASC");

        // Jika Kosong Maka Tolak
        if (empty($getProduct)) {
            return redirect()->route('home.kurang-stok', ['id' => $id])->with('messages', 'Produk Ini Sama Sekali Belum Memiliki Stok! Anda Tidak Bisa Melakukan Pengurangan Stok Karenanya');
        } elseif (!empty($getProduct)) {
            $qtyKurangExport = $qty;

            $getTotalStock = 0;
            foreach ($getProduct as $checkP) {
                $getTotalStock += $checkP->qty;
            }

            // Jika total stock dari semua barang per tanggal 0, maka langsung kembalikan pesan
            if ($getTotalStock == 0) {
                return redirect()->route('home.kurang-stok', ['id' => $id])->with('messages', 'Semua Stok Transaksi Yang Berkaitan Dengan Produk Ini Telah Habis');
            }

            // Sebelum loop barang per tanggal dilakukan, cek dulu apakah stok export tersebut lebih besar dari keseluruhan stok yang dimiliki di database atau tidak
            if ($qtyKurangExport > $getTotalStock) {
                return redirect()->route('home.kurang-stok', ['id' => $id])->with('messages', 'Stok Export Melebihi Kapasitas Stok Produk Yang Dicatat Oleh Sistem');
            }

            foreach ($getProduct as $p) {
                // Cek apakah qty database ini kosong atau tidak
                if ($p->qty != 0) {
                    // Cek apakah stok export lebih kecil dari stok qty per periode atau tidak
                    if ($qtyKurangExport < $p->qty) {
                        DB::statement("UPDATE stock SET stock.qty = stock.qty - {$qtyKurangExport} WHERE stock.sku_id='{$id}' AND stock.tanggal_transaksi='{$p->tanggal_transaksi}'");
                        break;
                    } else {
                        $qtyKurangExport -= $p->qty;
                        DB::statement("UPDATE stock SET stock.qty = 0 WHERE stock.sku_id='{$id}' AND stock.tanggal_transaksi='{$p->tanggal_transaksi}'");
                    }
                }
            }
        }

        // Input Data
        $getSkuProduct = collect(DB::select("SELECT * FROM product WHERE product.sku_id='{$id}'"))->first();

        $export = new Export;

        $export->tanggal = $tanggal;
        $export->penginput = $penginput;
        $export->no_transaksi = $no_transaksi;
        $export->nm_pembeli = $nm_pembeli;
        $export->alamat = $alamat;
        $export->kota_kab = $kota_kab;
        $export->provinsi = $provinsi;
        $export->no_hp = $no_hp;
        $export->toko = $toko;
        $export->qty = $qty;
        $export->keterangan = $keterangan;
        $export->sku_id = $id;
        $export->nm_barang = $getSkuProduct->nm_barang;
        $export->kategori = $getSkuProduct->kategori;
        $export->size = $getSkuProduct->ukuran;

        $export->save();

        $history = new History;
        $history->type = '5';
        $history->created_at = changeMyIndoTimestamp();
        $history->updated_at = changeMyIndoTimestamp();
        $history->status = '1';
        $history->error_count = '0';

        $history->save();

        return redirect()->route('home')->with('messages', 'Pengurangan Stok Berhasil Dilakukan!');
    }

    // Halaman kurang stok
    public function kurangstok($id = '')
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        // Data
        $data = [
            'title'     => 'Home',
            'product'   => Product::where('sku_id', $id)->first()
        ];

        return view('home.kurang-stok', $data);
    }

    // Export Product Qrcode ( Single ) 
    public function cetakqrcode($id = '')
    {
        // Validasi Session
        if (!session()->get('login'))
            return redirect()->route('auth')->with('message', 'Anda Belum Login!');

        $data = [
            'title'     => 'Get Qr Code',
            'product'   => Product::where('sku_id', $id)->first()
        ];

        // DOMPDF
        $pdf = new Dompdf();
        $options = new Options();
        $options->set('defaultFont', 'Courier');
        $options->set('isRemoteEnabled', TRUE);
        $options->set('debugKeepTemp', TRUE);
        $options->set('isHtml5ParserEnabled', true);
        // $options->set('chroot', base_url('assets/img/'));
        $pdf = new Dompdf($options);

        $pdf = new Dompdf();

        $pdf->loadHtml(view('home/cetak-qrcode', $data));
        $pdf->setPaper('A4', 'portrait');

        $pdf->render();
        $pdf->stream(hash('ripemd160', 'Data-Single-Product'), array("Attachment" => false));
    }
}
