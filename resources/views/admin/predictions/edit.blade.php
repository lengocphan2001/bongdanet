@extends('admin.layouts.app')

@section('title', 'Sửa Nhận định')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="bg-white rounded-lg shadow-md p-3 sm:p-6">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 mb-4 sm:mb-6">Sửa Nhận định</h1>

    <form method="POST" action="{{ route('admin.predictions.update', $prediction) }}" enctype="multipart/form-data" onsubmit="return submitForm()">
        @csrf
        @method('PUT')

        <div class="mb-6">
            <label for="match_select" class="block text-sm font-medium text-gray-700 mb-2">Chọn trận đấu *</label>
            <div class="relative">
                <input type="text" 
                       id="match_search" 
                       placeholder="Tìm kiếm trận đấu..." 
                       class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 mb-2 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                <select id="match_select" 
                        name="match_id" 
                        size="10"
                        class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                        required>
                    <option value="">-- Chọn trận đấu --</option>
                    @foreach($upcomingMatches ?? [] as $match)
                        <option value="{{ $match['id'] }}" 
                                {{ old('match_id', $prediction->match_id) == $match['id'] ? 'selected' : '' }}
                                data-league-id="{{ $match['league_id'] }}"
                                data-home-team="{{ $match['home_team'] }}"
                                data-away-team="{{ $match['away_team'] }}"
                                data-league-name="{{ $match['league_name'] }}"
                                data-country-name="{{ $match['country_name'] }}"
                                data-match-time="{{ $match['match_time'] }}"
                                data-search-text="{{ strtolower($match['home_team'] . ' ' . $match['away_team'] . ' ' . $match['league_name'] . ' ' . $match['country_name']) }}">
                            {{ $match['home_team'] }} vs {{ $match['away_team'] }} - {{ $match['league_name'] }} ({{ $match['country_name'] }}) - {{ $match['match_time_display'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('match_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Left Column -->
            <div class="space-y-4">

                <div>
                    <label for="match_api_id" class="block text-sm font-medium text-gray-700">Match API ID</label>
                    <input type="text" 
                           id="match_api_id" 
                           name="match_api_id" 
                           value="{{ old('match_api_id', $prediction->match_api_id) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                </div>

                <div>
                    <label for="home_team" class="block text-sm font-medium text-gray-700">Đội nhà</label>
                    <input type="text" 
                           id="home_team" 
                           name="home_team" 
                           value="{{ old('home_team', $prediction->home_team) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                </div>

                <div>
                    <label for="away_team" class="block text-sm font-medium text-gray-700">Đội khách</label>
                    <input type="text" 
                           id="away_team" 
                           name="away_team" 
                           value="{{ old('away_team', $prediction->away_team) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                </div>

                <div>
                    <label for="league_id" class="block text-sm font-medium text-gray-700">League ID</label>
                    <input type="number" 
                           id="league_id" 
                           name="league_id" 
                           value="{{ old('league_id', $prediction->league_id) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                </div>

                <div>
                    <label for="league_name" class="block text-sm font-medium text-gray-700">Giải đấu</label>
                    <input type="text" 
                           id="league_name" 
                           name="league_name" 
                           value="{{ old('league_name', $prediction->league_name) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                </div>

                <div>
                    <label for="match_time" class="block text-sm font-medium text-gray-700">Thời gian trận đấu</label>
                    <input type="datetime-local" 
                           id="match_time" 
                           name="match_time" 
                           value="{{ old('match_time', $prediction->match_time ? $prediction->match_time->format('Y-m-d\TH:i') : '') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $prediction->title) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="thumbnail" class="block text-sm font-medium text-gray-700">Ảnh thumbnail</label>
                    @if($prediction->thumbnail)
                        <div class="mb-2">
                            <img src="{{ Storage::url($prediction->thumbnail) }}" alt="Thumbnail" class="w-32 h-24 object-cover rounded border border-gray-300">
                        </div>
                    @endif
                    <input type="file" 
                           id="thumbnail" 
                           name="thumbnail" 
                           accept="image/*"
                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1a5f2f] file:text-white hover:file:bg-[#144a25]">
                    @error('thumbnail')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Định dạng: JPEG, PNG, JPG, GIF. Tối đa 2MB</p>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái *</label>
                    <select id="status" 
                            name="status" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                            required>
                        <option value="draft" {{ old('status', $prediction->status) === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="published" {{ old('status', $prediction->status) === 'published' ? 'selected' : '' }}>Xuất bản</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Nội dung chính *</label>
            <textarea id="content" 
                      name="content" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]"
                      required>{{ old('content', $prediction->content) }}</textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-6">
            <label for="analysis" class="block text-sm font-medium text-gray-700 mb-2">Phân tích chi tiết</label>
            <textarea id="analysis" 
                      name="analysis" 
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-[#1a5f2f] focus:border-[#1a5f2f]">{{ old('analysis', $prediction->analysis) }}</textarea>
        </div>

        <div class="mt-6 flex gap-4">
            <button type="submit" class="bg-[#1a5f2f] hover:bg-[#144a25] text-white px-6 py-2 rounded">
                Cập nhật
            </button>
            <a href="{{ route('admin.predictions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                Hủy
            </a>
        </div>
    </form>
</div>

<!-- TinyMCE CDN -->
<script src="https://cdn.tiny.cloud/1/xhvi99zf95ueinybzalp9vwc7yaolsr1rxibrza2dzwb9c8e/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE for content
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic underline strikethrough | forecolor backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist | outdent indent | ' +
            'removeformat | link image | code | fullscreen',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        images_upload_url: '{{ route("admin.upload.image") }}',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;
                xhr.open('POST', '{{ route("admin.upload.image") }}');
                
                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };
                
                xhr.onload = function () {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }
                    
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                    
                    var json = JSON.parse(xhr.responseText);
                    
                    if (!json) {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    
                    // Check for error first
                    if (json.error) {
                        reject(json.error.message || 'Upload failed');
                        return;
                    }
                    
                    // TinyMCE expects 'location' or 'url'
                    var imageUrl = json.location || json.url;
                    if (!imageUrl || typeof imageUrl != 'string') {
                        reject('Invalid response: missing image URL');
                        return;
                    }
                    
                    resolve(imageUrl);
                };
                
                xhr.onerror = function () {
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };
                
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', '{{ csrf_token() }}');
                
                xhr.send(formData);
            });
        },
        automatic_uploads: true,
        file_picker_types: 'image',
        paste_data_images: true
    });

    // Initialize TinyMCE for analysis
    tinymce.init({
        selector: '#analysis',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | ' +
            'bold italic underline strikethrough | forecolor backcolor | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist | outdent indent | ' +
            'removeformat | link image | code | fullscreen',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
        images_upload_url: '{{ route("admin.upload.image") }}',
        images_upload_handler: function (blobInfo, progress) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.withCredentials = true;
                xhr.open('POST', '{{ route("admin.upload.image") }}');
                
                xhr.upload.onprogress = function (e) {
                    progress(e.loaded / e.total * 100);
                };
                
                xhr.onload = function () {
                    if (xhr.status === 403) {
                        reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                        return;
                    }
                    
                    if (xhr.status < 200 || xhr.status >= 300) {
                        reject('HTTP Error: ' + xhr.status);
                        return;
                    }
                    
                    var json = JSON.parse(xhr.responseText);
                    
                    if (!json) {
                        reject('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    
                    // Check for error first
                    if (json.error) {
                        reject(json.error.message || 'Upload failed');
                        return;
                    }
                    
                    // TinyMCE expects 'location' or 'url'
                    var imageUrl = json.location || json.url;
                    if (!imageUrl || typeof imageUrl != 'string') {
                        reject('Invalid response: missing image URL');
                        return;
                    }
                    
                    resolve(imageUrl);
                };
                
                xhr.onerror = function () {
                    reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
                };
                
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('_token', '{{ csrf_token() }}');
                
                xhr.send(formData);
            });
        },
        automatic_uploads: true,
        file_picker_types: 'image',
        paste_data_images: true
    });

    // Form submit handler to sync TinyMCE content
    window.submitForm = function() {
        // Sync TinyMCE content to textarea before form submit
        if (tinymce.get('content')) {
            tinymce.get('content').save();
        }
        if (tinymce.get('analysis')) {
            tinymce.get('analysis').save();
        }
        return true;
    };

    // Handle match search
    const matchSearch = document.getElementById('match_search');
    const matchSelect = document.getElementById('match_select');
    
    if (matchSearch && matchSelect) {
        matchSearch.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const options = matchSelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                    return;
                }
                
                const searchData = option.getAttribute('data-search-text') || '';
                if (searchData.includes(searchText)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
    }
    
    // Handle match selection
    if (matchSelect) {
        matchSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                // Fill in match details
                const matchApiIdField = document.getElementById('match_api_id');
                const leagueIdField = document.getElementById('league_id');
                const homeTeamField = document.getElementById('home_team');
                const awayTeamField = document.getElementById('away_team');
                const leagueNameField = document.getElementById('league_name');
                const matchTimeField = document.getElementById('match_time');
                
                if (matchApiIdField) matchApiIdField.value = selectedOption.value;
                if (leagueIdField) leagueIdField.value = selectedOption.dataset.leagueId || '';
                if (homeTeamField) homeTeamField.value = selectedOption.dataset.homeTeam || '';
                if (awayTeamField) awayTeamField.value = selectedOption.dataset.awayTeam || '';
                if (leagueNameField) leagueNameField.value = selectedOption.dataset.leagueName || '';
                
                // Format match_time for datetime-local input
                const matchTime = selectedOption.dataset.matchTime;
                if (matchTime && matchTimeField) {
                    const date = new Date(matchTime);
                    const formatted = date.toISOString().slice(0, 16);
                    matchTimeField.value = formatted;
                }
            }
        });
    }
});
</script>
@endsection

