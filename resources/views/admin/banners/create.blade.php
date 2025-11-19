@extends('admin.layouts.app')

@section('title', 'Thêm Banner Mới')

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Thêm Banner Mới</h1>

    <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="space-y-4 sm:space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên Banner *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Loại Banner *</label>
                <select id="type" 
                        name="type" 
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                        required
                        onchange="updateFormByType()">
                    <option value="normal" {{ old('type', 'normal') === 'normal' ? 'selected' : '' }}>Banner thường</option>
                    <option value="modal" {{ old('type') === 'modal' ? 'selected' : '' }}>Modal (Popup khi vào trang)</option>
                    <option value="sticky" {{ old('type') === 'sticky' ? 'selected' : '' }}>Sticky (Cố định đầu trang)</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">
                    <span id="type-description-normal" class="type-desc">Banner hiển thị ở các vị trí thông thường (sidebar, top, bottom, inline)</span>
                    <span id="type-description-modal" class="type-desc hidden">Modal sẽ hiển thị popup khi người dùng truy cập trang, cần click để đóng</span>
                    <span id="type-description-sticky" class="type-desc hidden">Sticky banner sẽ cố định ở đầu trang, có nút đóng</span>
                </p>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content Type Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung Banner *</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="banner_type" value="image" checked onchange="toggleBannerType()" class="mr-2">
                        <span>Hình ảnh</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="banner_type" value="code" onchange="toggleBannerType()" class="mr-2">
                        <span>Code (Google Ads, etc.)</span>
                    </label>
                </div>
            </div>

            <!-- Image Upload -->
            <div id="image-section">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh</label>
                <input type="file" 
                       id="image" 
                       name="image" 
                       accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1a5f2f] file:text-white hover:file:bg-[#144a25]">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div id="code-section" style="display: none;">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code Quảng Cáo</label>
                <textarea id="code" 
                          name="code" 
                          rows="6"
                          class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f] font-mono text-sm">{{ old('code') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Dán code HTML/JavaScript quảng cáo (Google Ads, etc.)</p>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Link -->
            <div>
                <label for="link" class="block text-sm font-medium text-gray-700 mb-2">Link (URL)</label>
                <input type="url" 
                       id="link" 
                       name="link" 
                       value="{{ old('link') }}"
                       placeholder="https://example.com"
                       class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                @error('link')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alt Text -->
            <div>
                <label for="alt" class="block text-sm font-medium text-gray-700 mb-2">Alt Text</label>
                <input type="text" 
                       id="alt" 
                       name="alt" 
                       value="{{ old('alt', 'Advertisement') }}"
                       class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                @error('alt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Size and Position -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Kích thước *</label>
                    <select id="size" 
                            name="size" 
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                            required>
                        <option value="small" {{ old('size') === 'small' ? 'selected' : '' }}>Small (300x100)</option>
                        <option value="medium" {{ old('size', 'medium') === 'medium' ? 'selected' : '' }}>Medium (728x90)</option>
                        <option value="large" {{ old('size') === 'large' ? 'selected' : '' }}>Large (970x250)</option>
                        <option value="full-width" {{ old('size') === 'full-width' ? 'selected' : '' }}>Full Width</option>
                        <option value="sidebar" {{ old('size') === 'sidebar' ? 'selected' : '' }}>Sidebar (300x250)</option>
                        <option value="square" {{ old('size') === 'square' ? 'selected' : '' }}>Square (300x300)</option>
                        <option value="rectangle" {{ old('size') === 'rectangle' ? 'selected' : '' }}>Rectangle (300x250)</option>
                    </select>
                    @error('size')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700 mb-2">Vị trí *</label>
                    <select id="position" 
                            name="position" 
                            class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                            required>
                        <option value="top" {{ old('position') === 'top' ? 'selected' : '' }}>Top</option>
                        <option value="sidebar" {{ old('position') === 'sidebar' ? 'selected' : '' }}>Sidebar (Chung)</option>
                        <option value="sidebar-left" {{ old('position') === 'sidebar-left' ? 'selected' : '' }}>Sidebar Left</option>
                        <option value="sidebar-right" {{ old('position', 'sidebar-right') === 'sidebar-right' ? 'selected' : '' }}>Sidebar Right</option>
                        <option value="bottom" {{ old('position') === 'bottom' ? 'selected' : '' }}>Bottom</option>
                        <option value="inline" {{ old('position') === 'inline' ? 'selected' : '' }}>Inline</option>
                        <option value="sticky" {{ old('position') === 'sticky' ? 'selected' : '' }}>Sticky</option>
                    </select>
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Target -->
            <div>
                <label for="target" class="block text-sm font-medium text-gray-700 mb-2">Target</label>
                <select id="target" 
                        name="target" 
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                    <option value="_blank" {{ old('target', '_blank') === '_blank' ? 'selected' : '' }}>Mở tab mới</option>
                    <option value="_self" {{ old('target') === '_self' ? 'selected' : '' }}>Cùng tab</option>
                </select>
            </div>

            <!-- Order -->
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự hiển thị</label>
                <input type="number" 
                       id="order" 
                       name="order" 
                       value="{{ old('order', 0) }}"
                       min="0"
                       class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                <p class="mt-1 text-xs text-gray-500">Số nhỏ hơn sẽ hiển thị trước</p>
                @error('order')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="rounded border-gray-300 text-[#1a5f2f] focus:ring-[#1a5f2f]">
                    <span class="ml-2 text-sm text-gray-700">Đang hoạt động</span>
                </label>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3"
                          class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="bg-[#1a5f2f] hover:bg-[#144a25] text-white px-4 py-2 rounded text-sm sm:text-base">
                    Tạo Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm sm:text-base text-center">
                    Hủy
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function toggleBannerType() {
    const bannerType = document.querySelector('input[name="banner_type"]:checked').value;
    const imageSection = document.getElementById('image-section');
    const codeSection = document.getElementById('code-section');
    
    if (bannerType === 'image') {
        imageSection.style.display = 'block';
        codeSection.style.display = 'none';
        document.getElementById('image').required = false;
        document.getElementById('code').required = false;
    } else {
        imageSection.style.display = 'none';
        codeSection.style.display = 'block';
        document.getElementById('image').required = false;
        document.getElementById('code').required = false;
    }
}

function updateFormByType() {
    const type = document.getElementById('type').value;
    const descriptions = document.querySelectorAll('.type-desc');
    descriptions.forEach(desc => desc.classList.add('hidden'));
    
    if (type === 'normal') {
        document.getElementById('type-description-normal').classList.remove('hidden');
    } else if (type === 'modal') {
        document.getElementById('type-description-modal').classList.remove('hidden');
    } else if (type === 'sticky') {
        document.getElementById('type-description-sticky').classList.remove('hidden');
    }
}
</script>
@endsection

