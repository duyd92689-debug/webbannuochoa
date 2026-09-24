<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Perfume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PerfumeController extends Controller
{
    public function index(Request $request): View
    {
        $perfumes = Perfume::query()
            ->with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $keyword = trim((string) $request->input('search'));
                $query->where(function ($query) use ($keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('gender'), fn ($query) => $query->where('gender', $request->input('gender')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Perfume::count(),
            'active' => Perfume::where('is_active', true)->count(),
            'low_stock' => Perfume::where('stock', '<=', 5)->count(),
        ];

        return view('perfumes.index', compact('perfumes', 'stats'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('perfumes.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->storeUploadedImage($request, $data);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        Perfume::create($data);

        return redirect()->route('perfumes.index')
            ->with('success', 'Đã thêm nước hoa mới thành công.');
    }

    public function show(Perfume $perfume): View
    {
        $perfume->load('category');

        return view('perfumes.show', compact('perfume'));
    }

    public function edit(Perfume $perfume): View
    {
        $categories = Category::orderBy('name')->get();

        return view('perfumes.edit', compact('perfume', 'categories'));
    }

    public function update(Request $request, Perfume $perfume): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->storeUploadedImage($request, $data);

        if ($perfume->name !== $data['name']) {
            $data['slug'] = $this->uniqueSlug($data['name'], $perfume->id);
        }

        $data['is_active'] = $request->boolean('is_active');
        $perfume->update($data);

        return redirect()->route('perfumes.show', $perfume)
            ->with('success', 'Đã cập nhật nước hoa thành công.');
    }

    public function destroy(Perfume $perfume): RedirectResponse
    {
        $perfume->delete();

        return redirect()->route('perfumes.index')
            ->with('success', 'Đã xóa nước hoa khỏi danh sách.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:120'],
            'gender' => ['required', Rule::in(['nam', 'nu', 'unisex'])],
            'concentration' => ['nullable', 'string', 'max:50'],
            'volume_ml' => ['required', 'integer', 'min:1', 'max:5000'],
            'weight' => ['nullable', 'integer', 'min:1', 'max:50000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999999'],
            'stock_10ml' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'stock_50ml' => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'image_url' => ['nullable', 'string', 'max:2048', 'regex:/^(https?:\/\/|\/?images\/)/i'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required'        => 'Vui lòng nhập tên nước hoa.',
            'brand.required'       => 'Vui lòng chọn hoặc nhập thương hiệu cho nước hoa.',
            'gender.required'      => 'Vui lòng chọn giới tính.',
            'volume_ml.required'   => 'Vui lòng nhập dung tích chai nước hoa (ml).',
            'volume_ml.integer'    => 'Dung tích phải là một số nguyên hợp lệ.',
            'volume_ml.min'        => 'Dung tích tối thiểu phải từ 1 ml trở lên.',
            'weight.integer'       => 'Khối lượng phải là số nguyên (gram).',
            'weight.min'           => 'Khối lượng tối thiểu phải từ 1 gram trở lên.',
            'price.required'       => 'Vui lòng nhập giá bán.',
            'price.numeric'        => 'Giá bán phải là định dạng số.',
            'price.min'            => 'Giá bán không được là số âm.',
            'stock.required'       => 'Vui lòng nhập số lượng hàng trong kho.',
            'stock.integer'        => 'Số lượng tồn kho phải là số nguyên.',
            'stock.min'            => 'Số lượng tồn kho không được là số âm.',
            'sale_price.lte'       => 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá niêm yết.',
            'image_url.regex'      => 'Ảnh phải là URL http/https hoặc đường dẫn trong thư mục images.',
            'image_file.image'     => 'Tệp tải lên phải là hình ảnh.',
            'image_file.mimes'     => 'Ảnh phải có định dạng JPG, PNG hoặc WEBP.',
            'image_file.max'       => 'Ảnh không được lớn hơn 5MB.',
        ]);

        $validated['weight'] = (int) ($validated['weight'] ?? 200) ?: 200;

        $s = (int) ($validated['stock'] ?? 0);
        if (!isset($validated['stock_10ml']) || $validated['stock_10ml'] === null) {
            $validated['stock_10ml'] = $s > 0 ? max(5, (int) round($s * 2.5)) : 0;
        }
        if (!isset($validated['stock_50ml']) || $validated['stock_50ml'] === null) {
            $validated['stock_50ml'] = $s > 0 ? max(3, (int) round($s * 1.5)) : 0;
        }

        return $validated;
    }

    private function storeUploadedImage(Request $request, array $data): array
    {
        unset($data['image_file']);

        if (! $request->hasFile('image_file')) {
            return $data;
        }

        $file = $request->file('image_file');
        $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('images/products'), $filename);
        $data['image_url'] = 'images/products/'.$filename;

        return $data;
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $baseSlug = Str::slug($name) ?: 'nuoc-hoa';
        $slug = $baseSlug;
        $suffix = 2;

        while (Perfume::where('slug', $slug)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists()) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
