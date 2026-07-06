<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Program;
use App\Models\Event;
use App\Models\Product;
use App\Models\Gallery;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin Cibun',
            'email' => 'admin@cibun.com',
            'password' => Hash::make('password123')
        ]);

        // Programs (No image_url needed anymore, just icons)
        $programs = [
            [ 'title' => "Edukasi Warga Desa", 'description' => "Program peningkatan kapasitas warga dalam sadar wisata, hospitality, kebersihan, sanitasi, storytelling desa, pengelolaan sampah, konservasi lingkungan, dan tata kelola desa wisata.", 'icon' => "📚" ],
            [ 'title' => "Socio-Preneurship Warga", 'description' => "Program pembentukan jiwa kewirausahaan sosial agar warga mampu mengubah potensi lokal menjadi produk dan layanan bernilai ekonomi secara adil dan berkelanjutan.", 'icon' => "🤝" ],
            [ 'title' => "Nature-Based Event", 'description' => "Kegiatan wisata berbasis alam seperti eco-walk, jelajah kebun, susur desa, edukasi terasiring, konservasi hutan, dan pengalaman senja Cibun.", 'icon' => "🍃" ],
            [ 'title' => "Festival Budaya dan Seni Lokal", 'description' => "Festival rakyat yang mengangkat macapatan, pembacaan babad, seni tutur, kuliner lokal, musik tradisional, dan kearifan lokal Banyumasan.", 'icon' => "🎭" ],
            [ 'title' => "Festival Musim Durian", 'description' => "Agenda wisata berbasis musim panen durian lokal untuk menggerakkan ekonomi warga melalui bazaar durian, edukasi buah lokal, kuliner, dan produk olahan.", 'icon' => "🍈" ],
            [ 'title' => "Twilight Dinner Cibun", 'description' => "Pengalaman makan malam jelang senja di alam terbuka dengan pelayanan elegan, menu Nusantara, bahan pangan lokal, dan narasi kuliner desa.", 'icon' => "🍽️" ],
            [ 'title' => "Peak Event Desember 2026", 'description' => "Acara puncak yang menampilkan karya anak dusun, pasar UMKM, pertunjukan budaya, showcase produk lokal, dan refleksi pengembangan Cibun sebagai desa wisata berkelanjutan.", 'icon' => "🌟" ]
        ];
        foreach ($programs as $program) Program::create($program);

        // Events
        $events = [
            ['title' => "Festival Musim Durian", 'date' => "Musim Panen", 'focus' => ["Durian lokal", "Produk olahan durian", "Bazaar warga", "Edukasi pertanian", "Kuliner desa"]],
            ['title' => "Twilight Dinner Cibun", 'date' => "Akhir Pekan", 'focus' => ["Makan malam di alam terbuka", "Suasana senja", "Menu Nusantara", "Pelayanan elegan", "Pengalaman premium rural tourism"]],
            ['title' => "Festival Budaya Cibun", 'date' => "Agustus", 'focus' => ["Macapatan", "Maca babad", "Seni Banyumasan", "Budayawan lokal", "Pertunjukan warga"]],
            ['title' => "Cibun Nature Experience", 'date' => "Setiap Hari", 'focus' => ["Eco-walk", "Jelajah hutan pinus", "Terasiring", "Wisata edukasi lingkungan", "Storytelling desa"]],
            ['title' => "Peak Event Desember 2026", 'date' => "Desember 2026", 'focus' => ["Showcase desa", "UMKM", "Karya anak", "Seni budaya", "Refleksi program"]]
        ];
        foreach ($events as $event) Event::create($event);

        // Products
        $products = [
            ['name' => "Durian Lokal Cibun", 'description' => "Durian asli hasil kebun warga Cibun dengan cita rasa manis pahit yang khas dan daging buah tebal.", 'features' => ["Panen langsung dari pohon", "Tanpa bahan kimia", "Rasa otentik"]],
            ['name' => "Madu Hutan Asli", 'description' => "Madu murni yang dipanen secara lestari dari hutan pinus/damar di sekitar kawasan Cibun.", 'features' => ["100% murni", "Kaya antioksidan", "Proses panen lestari"]],
            ['name' => "Teh Kapulaga", 'description' => "Minuman herbal penghangat tubuh yang diracik dari kapulaga pilihan hasil bumi Cibun.", 'features' => ["Menghangatkan badan", "Meningkatkan imun", "Aroma rempah kuat"]],
            ['name' => "Kerajinan Bambu Warga", 'description' => "Produk kerajinan tangan berbahan dasar bambu lokal yang dibuat oleh pengrajin desa.", 'features' => ["Handmade", "Ramah lingkungan", "Desain estetik & fungsional"]],
            ['name' => "Pancake Durian", 'description' => "Produk olahan durian Cibun berbalut kulit tipis lembut dengan isian krim manis.", 'features' => ["Daging durian asli", "Tanpa pengawet", "Cocok untuk oleh-oleh"]],
            ['name' => "Bumbu Pecel Cibun", 'description' => "Bumbu pecel khas desa dengan resep turun-temurun, menggunakan kacang pilihan.", 'features' => ["Resep tradisional", "Tahan lama", "Rasa pedas manis pas"]]
        ];
        foreach ($products as $product) Product::create($product);

        // Galleries
        $galleries = [
            ['title' => "Hutan Pinus di Pagi Hari", 'category' => 'Alam Cibun'],
            ['title' => "Kerja Bakti Warga", 'category' => 'Warga & Gotong Royong'],
            ['title' => "Panen Durian", 'category' => 'Produk Lokal'],
            ['title' => "Acara Macapatan", 'category' => 'Budaya & Macapatan'],
            ['title' => "Twilight Dinner", 'category' => 'Event'],
            ['title' => "Sungai Jernih Cibun", 'category' => 'Alam Cibun'],
            ['title' => "UMKM Lokal", 'category' => 'Produk Lokal'],
            ['title' => "Wisata Edukasi", 'category' => 'Event']
        ];
        foreach ($galleries as $gallery) Gallery::create($gallery);

        // News
        $newsItems = [
            ['title' => "Grumbul Cibun: Dari Keterisolasian Menuju Desa Wisata Berkelanjutan", 'category' => "Kabar Cibun", 'date' => "12 Okt 2025", 'excerpt' => "Perjalanan panjang warga Cibun dalam membuka akses dan membangun desa wisata yang mandiri dan berwawasan lingkungan.", 'content' => "<h2>Dari Keterisolasian Menuju Desa Wisata</h2><p>Grumbul Cibun, sebuah dusun yang sebelumnya sulit diakses, kini mulai menata diri...</p>"],
            ['title' => "Menjaga Budaya Macapatan dan Babad di Tengah Hutan Pinus", 'category' => "Budaya Banyumasan", 'date' => "05 Nov 2025", 'excerpt' => "Bagaimana generasi tua dan muda Cibun melestarikan kesenian tradisional Macapatan di era modernisasi.", 'content' => "<h2>Lestari di Tengah Hutan</h2><p>Alunan macapat terdengar sayup-sayup dari balai desa...</p>"],
            ['title' => "Festival Musim Durian: Menghidupkan Ekonomi Warga Cibun", 'category' => "Produk Lokal", 'date' => "20 Jan 2026", 'excerpt' => "Antusiasme pengunjung dalam menikmati durian lokal asli Cibun yang berdampak langsung pada perekonomian warga.", 'content' => "<h2>Musim Panen Tiba</h2><p>Durian Cibun dikenal dengan cita rasa manis pahitnya yang khas...</p>"]
        ];
        foreach ($newsItems as $news) News::create($news);
    }
}
