@extends('page.main.index')

@section('content')


<div class="shadow p-5 rounded">
    <form action="{{ route('home.proses-kurang-stok',['id' => $product->sku_id]) }}" method="POST">
        @csrf
        <h3 class="text-danger mb-3"><i class="fas fa-minus"></i>
            Pengurangan Stok 
        </h3>
        <hr class="mb-4">

        
        <div class="text-danger px-4" style="border-left: 5px solid red;">
            <h4>{{ $product->nm_barang }}</h4>
        <h6><i class="fas fa-key"></i> Kode SKU: {{ $product->sku_id }}</h6>
        </div>
        <hr class="mb-4 mt-4">

        @if(Session::has('messages'))
            <div class="shadow px-4 py-3 text-danger my-4 fw-bold" role="alert">
                <i class="fas fa-triangle-exclamation"></i>&nbsp; {{ Session::get('messages') }}
            </div>
        @endif

        <h4 class="text-danger">Data Export</h4>
        <hr class="w-50">

        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="form-control" id="exampleFormControlInput1" placeholder="Tanggal">
                @error('tanggal')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-4">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Penginput</label>
                <input type="text" name="penginput" value="{{ old('penginput') }}" class="form-control" id="exampleFormControlInput1" placeholder="Penginput">
                @error('penginput')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-4">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">No. Transaksi</label>
                <input type="text" name="no_transaksi" value="{{ old('no_transaksi') }}" class="form-control" id="exampleFormControlInput1" placeholder="No Transaksi">
                @error('no_transaksi')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="mb-3 col-lg-6">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Nama Pembeli</label>
                <input type="text" name="nm_pembeli" value="{{ old('nm_pembeli') }}" class="form-control" id="exampleFormControlInput1" placeholder="Nama Pembeli">
                @error('nm_pembeli')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-3">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">No. Hp</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-control" id="exampleFormControlInput1" placeholder="No. Hp">
                @error('no_hp')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-3">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold">Toko</label>
                <input type="text" name="toko" value="{{ old('toko') }}" class="form-control" id="exampleFormControlInput1" placeholder="toko">
                @error('toko')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <div class="row mt-3">
            <div class="mb-3 col-lg-6">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold"> Alamat</label>
                <input type="text" name="alamat" value="{{ old('alamat') }}" class="form-control" id="exampleFormControlInput1" placeholder="Alamat">
                @error('alamat')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-3">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold"> Kota / Kabupaten</label>
                <input type="text" name="kota_kab" value="{{ old('kota_kab') }}" class="form-control" id="exampleFormControlInput1" placeholder="Kota - Kabupaten">
                @error('kota_kab')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-3">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold"> Provinsi</label>
                <input type="text" name="provinsi" value="{{ old('provinsi') }}" class="form-control" id="exampleFormControlInput1" placeholder="Provinsi">
                @error('provinsi')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>



        <h4 class="text-danger mt-4">Pengurangan Stok</h4>
        <hr class="w-50">

        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold"> Stok</label>
                <input type="text" name="qty" value="{{ old('qty') }}" class="form-control" id="exampleFormControlInput1" placeholder="Stok">
                @error('qty')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3 col-lg-8">
                <label for="exampleFormControlInput1" class="text-danger form-label fw-bold"> Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}" class="form-control" id="exampleFormControlInput1" placeholder="Keterangan">
                @error('keterangan')
                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                @enderror
            </div>
        </div>
        
        <div class="mt-4">
            <button onclick="this.disabled=true;this.value='Sending, please wait...';this.form.submit();" class="btn btn-danger w-25 me-3">Kurangi Stok</button>
        <a href="{{ route('home') }}" class="btn btn-danger d-inline-block w-25">Kembali</a>
        </div>
    </form>
</div>

@endSection