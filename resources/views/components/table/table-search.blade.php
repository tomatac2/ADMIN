<div class="top-part-right">
    <div class="search-form d-flex align-items-center gap-2 m-0">
        <input type="text" name="s" id="search-input" class="form-control search-input" value="{{ isset(request()->s) ? request()->s : '' }}">
        <button type="submit" class="btn btn-outline">{{ __('static.search') }}</button>
        <button type="button" class="btn btn-primary" id="clear" style="display: none">{{ __('static.clear') }}</button>
        <i class="ri-search-line" icon-name="search-normal-2"></i>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(window.location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Clear the input field when the user navigates back in the browser
        $(window).on('popstate', function() {
            if (!getUrlParameter('s')) {
                $('#search-input').val('');
            }
        });

        $('#clear').click(function () {
            $('#search-input').val('');
            window.location.href = window.location.pathname;
        });

        if ($('#search-input').val().trim() !== '') {
            $('#clear').show();
        }
    });
</script>
@endpush
