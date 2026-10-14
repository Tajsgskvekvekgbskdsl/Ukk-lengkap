@extends(auth()->user()->isAdmin() ? 'layouts.app' : 'layouts.site')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">Form Edit Profil</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger small">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                            class="form-control @error('nama_lengkap') is-invalid @enderror" required>
                        @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @if($user->anggota)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">NIS</label>
                                <input type="text" name="nis" value="{{ old('nis', $user->anggota->nis) }}"
                                    class="form-control @error('nis') is-invalid @enderror">
                                @error('nis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <input type="text" name="kelas" value="{{ old('kelas', $user->anggota->kelas) }}"
                                    class="form-control @error('kelas') is-invalid @enderror">
                                @error('kelas')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" rows="2"
                                    class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $user->anggota->alamat) }}</textarea>
                                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                                        @endif

                    <div class="mb-3">
                        <label class="form-label">Foto Profil</label>
                        <input type="file" name="foto"
                               class="form-control form-control-sm @error('foto') is-invalid @enderror"
                               accept="image/jpeg,image/png">
                        <small class="text-muted d-block mt-1">JPG / PNG, maksimal 2 MB. Biarkan kosong untuk tidak mengganti.</small>
                        @if ($user->foto)
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="{{ $user->url_foto }}" alt="Foto profil" class="rounded-circle"
                                     style="width:48px;height:48px;object-fit:cover;">
                            </div>
                        @endif
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-green"><i class="bi bi-save"></i> Simpan</button>
                        <a href="{{ route('user.profile.index') }}" class="btn btn-outline-navy">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
