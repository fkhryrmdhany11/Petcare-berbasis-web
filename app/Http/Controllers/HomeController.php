<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $layanan = [
            ['nama' => 'Adopsi Hewan', 'kelas' => 'adopt', 'gambar' => 'assets/img/meng.png', 'route' => 'adoption.index'],
            ['nama' => 'Toko Online', 'kelas' => 'shop', 'gambar' => 'assets/img/food.png', 'route' => 'shop.index'],
            ['nama' => 'Cek Kesehatan', 'kelas' => 'health', 'gambar' => 'assets/img/vet.png', 'route' => 'health.index'],
            ['nama' => 'Jadwalkan Vaksin', 'kelas' => 'vaksin', 'gambar' => 'assets/img/vaksin.png', 'route' => 'health.index'],
        ];

        $testimoni = [
            ['nama' => 'JESIKA', 'avatar' => 'assets/img/jesika.jfif', 'teks' => 'Pas lagi ga selera makan, aku selalu beli whiskas disini!'],
            ['nama' => 'ARIN', 'avatar' => 'assets/img/puncak-lukisem.jfif', 'teks' => 'Kambing disini ganteng-ganteng.'],
            ['nama' => 'FARRAS', 'avatar' => 'assets/img/farras.jfif', 'teks' => ':3'],
            ['nama' => 'FAKHRY', 'avatar' => 'assets/img/fakhry.jfif', 'teks' => 'Penyelamat pas anabulku tiba-tiba aslam.'],
        ];

        return view('home', compact('layanan', 'testimoni'));
    }
}
