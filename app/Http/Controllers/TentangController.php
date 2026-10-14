<?php
namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Http\Request;

/** Halaman CMS statis "Tentang" — profil singkat perpustakaan. */
class TentangController extends Controller
{
    public function __invoke(Request $request)
    {
        return view('site.tentang');
    }
}
