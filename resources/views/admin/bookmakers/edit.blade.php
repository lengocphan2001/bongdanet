@extends('admin.layouts.app')

@section('title', 'Sửa Nhà Cái')

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Sửa Nhà Cái</h1>

    <form method="POST" action="{{ route('admin.bookmakers.update', $bookmaker) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-4 sm:space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên Nhà Cái *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $bookmaker->name) }}"
                       class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current Image -->
            @if($bookmaker->image)
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ảnh hiện tại</label>
                <img src="{{ Storage::url($bookmaker->image) }}" 
                     alt="{{ $bookmaker->name }}" 
                     class="w-32 h-32 object-contain border border-gray-300 rounded">
            </div>
            @endif

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Ảnh/GIF Logo Nhà Cái {{ $bookmaker->image ? '(Để trống nếu không thay đổi)' : '*' }}</label>
                <input type="file" 
                       id="image" 
                       name="image" 
                       accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1a5f2f] file:text-white hover:file:bg-[#144a25]"
                       {{ !$bookmaker->image ? 'required' : '' }}>
                <p class="mt-1 text-xs text-gray-500">Hỗ trợ: JPEG, PNG, JPG, GIF, WEBP (tối đa 2MB)</p>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Link -->
            <div>
                <label for="link" class="block text-sm font-medium text-gray-700 mb-2">Link Nhà Cái *</label>
                <input type="url" 
                       id="link" 
                       name="link" 
                       value="{{ old('link', $bookmaker->link) }}"
                       placeholder="https://example.com"
                       class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                       required>
                @error('link')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target -->
            <div>
                <label for="target" class="block text-sm font-medium text-gray-700 mb-2">Target</label>
                <select id="target" 
                        name="target" 
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                    <option value="_blank" {{ old('target', $bookmaker->target) === '_blank' ? 'selected' : '' }}>Mở tab mới</option>
                    <option value="_self" {{ old('target', $bookmaker->target) === '_self' ? 'selected' : '' }}>Cùng tab</option>
                </select>
            </div>

            <!-- Order -->
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự hiển thị</label>
                <input type="number" 
                       id="order" 
                       name="order" 
                       value="{{ old('order', $bookmaker->order) }}"
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
                           {{ old('is_active', $bookmaker->is_active) ? 'checked' : '' }}
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
                          class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">{{ old('notes', $bookmaker->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="bg-[#1a5f2f] hover:bg-[#144a25] text-white px-4 py-2 rounded text-sm sm:text-base">
                    Cập nhật Nhà Cái
                </button>
                <a href="{{ route('admin.bookmakers.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm sm:text-base text-center">
                    Hủy
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

