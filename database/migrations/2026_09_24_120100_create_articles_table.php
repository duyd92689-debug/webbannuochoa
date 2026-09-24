<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500);
            $table->longText('body');
            $table->string('image_url')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamps();
        });

        $now = now();
        DB::table('articles')->insert([
            ['title' => 'Hiểu ba tầng hương khi chọn nước hoa', 'slug' => 'hieu-ba-tang-huong',
                'excerpt' => 'Đừng vội quyết định chỉ sau lần xịt đầu. Một mùi hương luôn thay đổi theo thời gian.',
                'body' => "Hương đầu là cảm giác đầu tiên khi bạn xịt nước hoa. Tầng này thường bay đi khá nhanh, vì vậy ấn tượng lúc mới thử chưa kể hết câu chuyện của chai nước hoa.\n\nHương giữa xuất hiện khi hương đầu dịu xuống. Đây thường là phần thể hiện rõ cá tính của mùi hương. Hương cuối lưu lại sau cùng và có thể khác nhau trên từng làn da.\n\nKhi thử nước hoa, hãy xịt lên da sạch và chờ ít nhất 20–30 phút. Nếu có thể, hãy quay lại cảm nhận thêm sau vài giờ trước khi chọn mua chai lớn.",
                'image_url' => 'images/products/miss-dior-blooming.jpg', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Xịt nước hoa ở đâu để cảm nhận dễ chịu?', 'slug' => 'xit-nuoc-hoa-o-dau',
                'excerpt' => 'Chọn đúng vị trí và lượng xịt để mùi hương ở gần bạn một cách tinh tế.',
                'body' => "Cổ tay và hai bên cổ là những vị trí ấm, thuận tiện để cảm nhận nước hoa. Bạn có thể bắt đầu với một lượng nhỏ rồi điều chỉnh theo nồng độ của sản phẩm và không gian sử dụng.\n\nSau khi xịt, hãy để nước hoa khô tự nhiên. Việc chà xát mạnh hai cổ tay có thể làm trải nghiệm các tầng hương thay đổi. Nếu bạn nhạy cảm với mùi, hãy thử một lần xịt trên quần áo ở vị trí kín và kiểm tra vải trước để tránh vết ố.\n\nTrong văn phòng hoặc không gian kín, một mùi hương nhẹ nhàng với lượng vừa phải thường dễ chịu hơn cho cả bạn lẫn người xung quanh.",
                'image_url' => 'images/products/delina-exclusif.jpg', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'Cách bảo quản nước hoa tại nhà', 'slug' => 'bao-quan-nuoc-hoa',
                'excerpt' => 'Ánh nắng, nhiệt độ và không khí có thể ảnh hưởng đến mùi hương theo thời gian.',
                'body' => "Nên cất nước hoa ở nơi khô, mát và ít thay đổi nhiệt độ. Ánh nắng trực tiếp và nhiệt độ cao có thể khiến mùi hương biến đổi nhanh hơn.\n\nSau khi dùng, hãy đậy nắp và đặt chai đứng thẳng. Hạn chế để nước hoa ở phòng tắm nếu nơi đó thường xuyên nóng ẩm. Hộp đựng ban đầu cũng là một cách đơn giản để giảm ánh sáng chiếu vào chai.\n\nNếu màu hoặc mùi thay đổi rõ rệt, hãy kiểm tra lại thời gian mở nắp và điều kiện bảo quản. Chất lượng của từng chai còn phụ thuộc công thức và cách sử dụng thực tế.",
                'image_url' => 'images/products/tom-ford-rose-prick.jpg', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
            ['title' => 'EDT, EDP và Parfum khác nhau thế nào?', 'slug' => 'edt-edp-parfum',
                'excerpt' => 'Hiểu tên gọi nồng độ để chọn sản phẩm hợp với thói quen dùng hương của bạn.',
                'body' => "EDT, EDP và Parfum thường là các cách gọi thể hiện mức nồng độ hương liệu khác nhau trong một dòng nước hoa. Tuy nhiên, con số cụ thể có thể thay đổi tùy thương hiệu và công thức.\n\nNồng độ cao hơn không luôn đồng nghĩa với mùi hương mạnh hơn hoặc phù hợp hơn. Cách phối nguyên liệu, làn da, thời tiết và lượng xịt đều ảnh hưởng đến độ lưu hương thực tế.\n\nKhi phân vân, hãy đọc mô tả của chính sản phẩm và thử trên da. Bạn có thể bắt đầu từ chai chiết nhỏ để trải nghiệm qua nhiều thời điểm trong ngày trước khi chọn dung tích lớn.",
                'image_url' => 'images/products/black-gold.jpg', 'is_published' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
