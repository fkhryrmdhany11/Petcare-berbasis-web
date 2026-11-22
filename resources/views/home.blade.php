@extends('layouts.app')

@section('title', 'Beranda | PawPaw Pet Care')

@section('content')
  <h2 class="sub-title">LAYANAN KAMI</h2>
  <div class="menu-container">
    @foreach($layanan as $item)
      <a href="{{ route($item['route']) }}" class="menu-card {{ $item['kelas'] }}">
        <img src="{{ asset($item['gambar']) }}" alt="{{ $item['nama'] }}">
        <h3>{{ $item['nama'] }}</h3>
      </a>
    @endforeach
  </div>

  <h2 class="testi-title">APA KATA MEREKA?</h2>
  <div class="testi-list">
    @foreach($testimoni as $t)
      <div class="testi-card">
        <div class="testi-top">
          <img class="testi-avatar" src="{{ asset($t['avatar']) }}" alt="Foto {{ $t['nama'] }}">
          <span class="testi-stars" aria-label="Rating 5 dari 5 bintang" role="img"><span aria-hidden="true">★★★★★</span></span>
        </div>
        <div class="testi-body">
          <span class="testi-name">{{ $t['nama'] }}</span>
          <span class="testi-divider"></span>
          <span class="testi-text">{{ $t['teks'] }}</span>
        </div>
      </div>
    @endforeach
  </div>
@endsection
