<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\HealthService;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin & Customer Default
        User::updateOrCreate(
            ['email' => 'admin@pawpaw.com'],
            [
                'name' => 'Admin PawPaw',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jl. Panam Raya No. 12, Pekanbaru',
                'bio' => 'Administrator PawPaw Pet Care',
            ]
        );

        $customer = User::updateOrCreate(
            ['email' => 'arini@pawpaw.com'],
            [
                'name' => 'Arini Dewinda',
                'username' => 'arini',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone' => '0812-3456-7890',
                'address' => 'Jl. Sudirman No. 12, Pekanbaru',
                'bio' => 'Cat lover & proud pet parent ♡',
            ]
        );

        // 2. Hewan Peliharaan Customer
        Pet::updateOrCreate(
            ['user_id' => $customer->user_id, 'pet_name' => 'Lumi'],
            [
                'species' => 'Kucing',
                'breed' => 'Persia',
                'gender' => 'Betina',
                'age' => '2 Tahun',
                'health_status' => 'Sehat',
            ]
        );

        Pet::updateOrCreate(
            ['user_id' => $customer->user_id, 'pet_name' => 'Milo'],
            [
                'species' => 'Anjing',
                'breed' => 'Golden Retriever',
                'gender' => 'Jantan',
                'age' => '1 Tahun',
                'health_status' => 'Vaksin Rutin',
            ]
        );

        // 3. Produk Toko (6 Produk Awal)
        $products = [
            [
                'slug' => 'whiskas',
                'product_name' => 'Whiskas Adult',
                'price' => 75000,
                'stock' => 25,
                'category' => 'makanan',
                'image' => 'assets/img/shop/whiskas.png',
                'description' => 'Makanan nutrisi lengkap untuk kucing dewasa.',
            ],
            [
                'slug' => 'pedigree',
                'product_name' => 'Pedigree Adult',
                'price' => 100000,
                'stock' => 20,
                'category' => 'makanan',
                'image' => 'assets/img/shop/pedigree.png',
                'description' => 'Makanan bergizi tinggi untuk kesehatan anjing dewasa.',
            ],
            [
                'slug' => 'shampoo-anjing',
                'product_name' => 'Shampoo Anjing',
                'price' => 90000,
                'stock' => 15,
                'category' => 'perawatan',
                'image' => 'assets/img/shop/shampoo-anjing.png',
                'description' => 'Shampoo lembut anti kutu & menjaga kilau bulu anjing.',
            ],
            [
                'slug' => 'shampoo-kucing',
                'product_name' => 'Shampoo Kucing',
                'price' => 85000,
                'stock' => 18,
                'category' => 'perawatan',
                'image' => 'assets/img/shop/shampoo-kucing.png',
                'description' => 'Formula khusus kulit sensitif kucing kesayangan.',
            ],
            [
                'slug' => 'sisir',
                'product_name' => 'Sisir Grooming',
                'price' => 60000,
                'stock' => 30,
                'category' => 'perawatan',
                'image' => 'assets/img/shop/sisir-grooming.png',
                'description' => 'Sisir ergonomis untuk merapikan bulu rontok.',
            ],
            [
                'slug' => 'tulang',
                'product_name' => 'Mainan Tulang Karet',
                'price' => 35000,
                'stock' => 40,
                'category' => 'mainan',
                'image' => 'assets/img/shop/mainan-tulang.png',
                'description' => 'Mainan gigit elastis & aman untuk kesehatan gigi hewan.',
            ],
        ];

        foreach ($products as $p) {
            Product::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 4. Hewan Adopsi (4 Hewan Awal)
        $animals = [
            [
                'nama' => 'Siti',
                'species' => 'kucing',
                'breed' => 'Anggora',
                'gender' => 'Betina',
                'age' => '1 Tahun',
                'status' => 'available',
                'image' => 'assets/img/anggora.jpg',
                'description' => 'Kucing anggora jinak, aktif, dan sudah vaksin dasar.',
            ],
            [
                'nama' => 'Jeno',
                'species' => 'lainnya',
                'breed' => 'African Pygmy',
                'gender' => 'Jantan',
                'age' => '8 Bulan',
                'status' => 'available',
                'image' => 'assets/img/goat.jpg',
                'description' => 'Kambing mini ramah dan sangat suka bermain di halaman.',
            ],
            [
                'nama' => 'Jubariyah',
                'species' => 'kucing',
                'breed' => 'Bengal',
                'gender' => 'Betina',
                'age' => '13 Bulan',
                'status' => 'adopted',
                'image' => 'assets/img/bengal.jpg',
                'description' => 'Kucing Bengal lincah dengan corak bulu eksotis.',
            ],
            [
                'nama' => 'Noe',
                'species' => 'kelinci',
                'breed' => 'Rex',
                'gender' => 'Jantan',
                'age' => '10 Bulan',
                'status' => 'available',
                'image' => 'assets/img/rabbit.jpg',
                'description' => 'Kelinci Rex berbulu halus seperti beludru dan tenang.',
            ],
        ];

        foreach ($animals as $a) {
            Animal::updateOrCreate(['nama' => $a['nama'], 'breed' => $a['breed']], $a);
        }

        // 5. Layanan Kesehatan (3 Layanan Awal dengan Jadwal Praktik & Dokter)
        $services = [
            [
                'service_name' => 'Booking Jadwal Periksa',
                'doctor_name' => 'drh. Clara Wijaya',
                'practice_days' => 'Senin - Sabtu',
                'time_slots' => '09:00, 11:00, 14:00, 16:00',
                'daily_quota' => 5,
                'description' => 'Pemeriksaan fisik menyeluruh & konsultasi kesehatan klinik',
                'icon' => '🩺',
                'card_color' => 'card-green',
                'price' => 100000,
            ],
            [
                'service_name' => 'Jadwalkan Vaksin',
                'doctor_name' => 'drh. Budi Pratama',
                'practice_days' => 'Senin - Minggu',
                'time_slots' => '09:00, 13:00, 15:30',
                'daily_quota' => 8,
                'description' => 'Vaksinasi lengkap (Rabies, Tricat, Eurican) + buku vaksin',
                'icon' => '💉',
                'card_color' => 'card-blue',
                'price' => 150000,
            ],
            [
                'service_name' => 'Panggil Dokter Ke Rumah',
                'doctor_name' => 'drh. Siska Amelia',
                'practice_days' => 'Selasa - Minggu',
                'time_slots' => '10:00, 14:00, 16:30',
                'daily_quota' => 3,
                'description' => 'Dokter kami datang langsung ke rumah Anda untuk Home Visit',
                'icon' => '🏥',
                'card_color' => 'card-purple',
                'price' => 250000,
            ],
        ];

        foreach ($services as $s) {
            HealthService::updateOrCreate(['service_name' => $s['service_name']], $s);
        }
    }
}
