@extends('admin.layouts.master')
@section('title', __('static.languages.translate'))
@section('content')
    <div class="row">
        <div class="m-auto col-xl-10 col-xxl-8">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <div class="contentbox-subtitle">
                            <h3>{{ __('static.languages.translate') }}</h3>
                        </div>
                    </div>
                    <form class="" action="{{ route('admin.language.translate.update', ['id' => request()->id, 'file' => $file]) }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="form-group row">
                            <label class="col-3" for="locale">{{ __('static.languages.select_translate_file') }}</label>
                            <div class="col-9">
                                <select class="form-select select-2" name="file" id="file-select" onchange="updateURL()">
                                    data-placeholder="{{ __('Select Locale') }}">
                                    <option></option>
                                    @foreach ($allFiles as $fileName)
                                        <option value="{{ $fileName }}"
                                            @if ($fileName === @$file) selected @endif>
                                            {{ ucfirst($fileName) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('locale')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-3" for="search">{{ __('static.search') }}</label>
                            <div class="col-9">
                                <input type="text" class="form-control" id="translation-search" 
                                       placeholder="{{ __('static.languages.search_placeholder') }}"
                                       value="{{ $searchTerm ?? '' }}">
                                <small class="form-text text-muted">
                                    {{ __('static.languages.search_hint') }}
                                </small>
                                <div id="search-loader" class="spinner-border spinner-border-sm text-primary mt-2" role="status" style="display: none;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group title-panel">
                            <div class="row">
                                <label class="col-3 mb-0">
                                    {{ __('static.key') }}
                                </label>
                                <label class="col-9 mb-0">
                                    {{ __('static.value') }}
                                </label>
                            </div>
                        </div>
                        <div class="table-responsive language-table custom-scroll">
                            @if($translations->isEmpty() && !empty($searchTerm))
                                <div class="alert alert-info text-center my-3">
                                    <i class="ri-search-line"></i> {{ __('static.languages.no_results_found') }}
                                    <br>
                                    <small>{{ __('static.languages.search_term') }}: <strong>{{ $searchTerm }}</strong></small>
                                    <br>
                                    <a href="{{ route('admin.language.translate', ['id' => request()->id, 'file' => $file]) }}" class="btn btn-sm btn-outline-primary mt-2">
                                        {{ __('static.languages.clear_search') }}
                                    </a>
                                </div>
                            @else
                                @foreach ($translations as $key => $value)
                                    @include('admin.language.trans-fields', ['key' => $key, 'value' => $value])
                                @endforeach
                            @endif
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="submit-btn">
                                        <button type="submit" name="save" class="btn btn-solid">
                                            {{ __('static.save') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pagination">
                            {{ $translations->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        "use strict";

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('file-select').addEventListener('change', updateURL);
            
            // Real-time search with debouncing
            const searchInput = document.getElementById('translation-search');
            const searchLoader = document.getElementById('search-loader');
            let searchTimeout = null;
            
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim();
                
                // Show loader
                searchLoader.style.display = 'inline-block';
                
                // Clear previous timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Set new timeout for debouncing (300ms delay)
                searchTimeout = setTimeout(function() {
                    performSearch(searchTerm);
                }, 300);
            });
            
            function performSearch(searchTerm) {
                const file = document.getElementById('file-select').value || '{{ $file }}';
                const languageId = '{{ request()->id }}';
                
                // Build URL with search parameter
                let url = `{{ route('admin.language.translate', ['id' => 'LANG_ID']) }}`
                    .replace('LANG_ID', languageId);
                
                // Add file parameter
                url += `?file=${encodeURIComponent(file)}`;
                
                // Add search parameter if not empty
                if (searchTerm !== '') {
                    url += `&search=${encodeURIComponent(searchTerm)}`;
                }
                
                // Redirect to URL with search parameters
                window.location.href = url;
            }
        });

        function updateURL() {
            const file = document.getElementById('file-select').value;
            const url = `{{ route('admin.language.translate', ['id' => 'ID', 'file' => 'FILE']) }}`
                .replace('ID', `{{ request()?->id }}`)
                .replace('FILE', file);

            window.location.href = url;
        }
    </script>
@endpush
