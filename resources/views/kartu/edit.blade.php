@extends('layouts.site')

@section('title', 'Kartu Anggota')
@section('page-title', 'Kartu Anggota')

@section('content')
<div class="site-container page-body py-4">
    <div class="row g-4 align-items-start align-items-md-center">
        <div class="col-12">
            <h1 class="mb-1">Kartu Anggota</h1>
            <p class="text-muted mb-0">
                Pilih jenis kartu keanggotaan untuk mendapatkan tarif denda yang sesuai.
            </p>
        </div>
    </div>

    <hr class="my-4">

    <form method="POST" action="{{ route('user.kartu.update') }}" enctype="multipart/form-data"
          id="kartuForm">
        @csrf
        @method('PUT')

        {{-- Pilih jenis kartu --}}
        <fieldset class="row g-3 mb-4">
            <legend class="col-form-label col-12 mb-0 fw-medium">Jenis Kartu</legend>

            @foreach (['normal' => 'Normal (tidak ada kartu)', 'pelajar' => 'Pelajar', 'mahasiswa' => 'Mahasiswa'] as $val => $label)
                <div class="col-12 col-md-auto d-flex align-items-start">
                    <div class="form-check form-check-custom mt-1">
                        <input class="form-check-input" type="radio" name="jenis"
                               id="kartu-{{ $val }}" value="{{ $val }}"
                               {{ (($kartu->jenis ?? 'normal') === $val) ? 'checked' : '' }}>
                        <label class="form-check-label" for="kartu-{{ $val }}">{{ $label }}</label>
                    </div>
                </div>
            @endforeach
        </fieldset>

        @error('jenis')
            <div class="alert alert-danger small mb-3">{{ $message }}</div>
        @enderror

        {{-- Upload foto kartu --}}
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-12 col-md-auto">
                @if (! empty($kartu->file_url))
                    <img src="{{ $kartu->file_url }}" alt="Foto kartu"
                         class="img-thumbnail rounded" style="max-width:120px;max-height:80px;object-fit:cover;">
                @else
                    <div class="border rounded d-flex align-items-center justify-content-center text-muted small"
                         style="width:120px;height:80px;">Belum ada foto</div>
                @endif
            </div>

            <div class="col-12 col-md">
                <label for="file" class="form-label mb-0">Foto Kartu (opsional)</label>
                <input class="form-control form-control-sm mt-1" type="file" name="file" id="file"
                       accept="image/jpeg,image/png">
                <small class="text-muted">Format: jpg / png. Maksimal 2 MB.</small>
            </div>
        </div>

        @error('file')
            <div class="alert alert-danger small mb-3">{{ $message }}</div>
        @enderror

        {{-- Aksi --}}
        <div class="d-flex flex-column flex-md-row-reverse gap-2 pt-2">
            <button type="submit" class="btn btn-green btn-sm">
                <i class="bi bi-save"></i> Simpan
            </button>

                        @if (! empty($kartu->file))
                <form method="POST" action="{{ route('user.kartu.destroy') }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Hapus foto kartu?');">
                        <i class="bi bi-trash"></i> Hapus Foto
                    </button>
                </form>
            @endif

            <a href="{{ route('user.profile.index') }}" class="btn btn-outline-secondary btn-sm ms-auto">Kembali</a>
        </div>
    </form>
</div>
@endsection
