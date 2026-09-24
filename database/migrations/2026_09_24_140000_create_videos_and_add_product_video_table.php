<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Thêm cột video_url trực tiếp vào bảng perfumes
        Schema::table('perfumes', function (Blueprint $table) {
            if (!Schema::hasColumn('perfumes', 'video_url')) {
                $table->text('video_url')->nullable()->after('image_url');
            }
        });

        // 2. Tạo bảng videos quản lý toàn bộ Fragrance Shorts & Video Reviews
        if (!Schema::hasTable('videos')) {
            Schema::create('videos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('perfume_id')->nullable()->constrained('perfumes')->nullOnDelete();
                $table->string('title');
                $table->text('video_url');
                $table->text('thumbnail_url')->nullable();
                $table->string('duration', 20)->nullable()->default('0:45');
                $table->unsignedInteger('views_count')->default(1200);
                $table->text('description')->nullable();
                $table->string('placement', 30)->default('all'); // 'home', 'product', 'all'
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Khởi tạo sẵn một số video mẫu liên kết với nước hoa có sẵn
        $now = now();
        $perfumes = DB::table('perfumes')->select('id', 'name', 'slug', 'brand', 'image_url')->get()->keyBy('slug');

        $sampleVideos = [
            [
                'slug' => 'miss-dior-blooming-bouquet',
                'title' => 'Miss Dior: Hương hoa hồng ngọt dịu đầu mùa',
                'video_url' => 'https://www.youtube.com/watch?v=kYv9bH8F688', // Miss Dior official campaign
                'thumbnail_url' => 'images/products/miss-dior-blooming.jpg',
                'duration' => '0:45',
                'views_count' => 12400,
                'description' => 'Khám phá nốt hương hoa mẫu đơn dịu dàng và hoa hồng Grasse kiêu sa cùng độ tỏa hương mềm mại suốt ngày dài.',
                'placement' => 'all',
                'sort_order' => 1,
            ],
            [
                'slug' => 'dior-sauvage-edp',
                'title' => 'Dior Sauvage: Vì sao quý ông nào cũng nên có?',
                'video_url' => 'https://www.youtube.com/watch?v=Lq_j0s35E2k', // Dior Sauvage film
                'thumbnail_url' => 'images/products/dior-sauvage.jpg',
                'duration' => '0:35',
                'views_count' => 24800,
                'description' => 'Đánh giá độ nam tính, vòi xịt phun sương tỏa đều và thời gian bám tỏa vượt trội lên đến 10 giờ.',
                'placement' => 'all',
                'sort_order' => 2,
            ],
            [
                'slug' => 'bleu-de-chanel-edp',
                'title' => 'Bleu de Chanel: Độ lưu hương thực tế sau 8 tiếng',
                'video_url' => 'https://www.youtube.com/watch?v=oG-nnDlnDDg', // Bleu de Chanel
                'thumbnail_url' => 'images/products/chanel-bleu.jpg',
                'duration' => '0:50',
                'views_count' => 18200,
                'description' => 'Hương gỗ tuyết tùng kết hợp bưởi hồng tươi mát – Lựa chọn hoàn hảo cho môi trường công sở và gặp gỡ đối tác.',
                'placement' => 'all',
                'sort_order' => 3,
            ],
            [
                'slug' => 'narciso-rodriguez-musc-noir-rose',
                'title' => 'Narciso Musc Noir Rose: Sự quyến rũ nồng nàn',
                'video_url' => 'https://www.youtube.com/watch?v=FqBqC8m1u6w',
                'thumbnail_url' => 'images/products/narciso-musc-noir-rose.jpg',
                'duration' => '0:30',
                'views_count' => 9600,
                'description' => 'Xạ hương đặc trưng hòa quyện cùng mận chín và hoa huệ – Vũ khí bí mật cho những buổi hẹn hò lãng mạn.',
                'placement' => 'all',
                'sort_order' => 4,
            ],
        ];

        foreach ($sampleVideos as $sample) {
            $perfumeId = null;
            $thumb = $sample['thumbnail_url'];
            if (isset($perfumes[$sample['slug']])) {
                $p = $perfumes[$sample['slug']];
                $perfumeId = $p->id;
                if (!empty($p->image_url)) {
                    $thumb = $p->image_url;
                }
                // Cập nhật video_url cho sản phẩm đó
                DB::table('perfumes')->where('id', $perfumeId)->update(['video_url' => $sample['video_url']]);
            }

            DB::table('videos')->insert([
                'perfume_id' => $perfumeId,
                'title' => $sample['title'],
                'video_url' => $sample['video_url'],
                'thumbnail_url' => $thumb,
                'duration' => $sample['duration'],
                'views_count' => $sample['views_count'],
                'description' => $sample['description'],
                'placement' => $sample['placement'],
                'sort_order' => $sample['sort_order'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
        Schema::table('perfumes', function (Blueprint $table) {
            if (Schema::hasColumn('perfumes', 'video_url')) {
                $table->dropColumn('video_url');
            }
        });
    }
};
