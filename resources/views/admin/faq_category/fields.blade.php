<div class="row g-xl-4 g-3">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="accordion-sec">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ isset($faq) ? __('static.faq_categories.edit_faq_category') : __('static.faq_categories.add_faq_category') }}
                            ({{ request('locale', app()->getLocale()) }})
                        </h3>
                    </div>
                    @isset($faq)
                        <div class="form-group row">
                            <label class="col-md-2" for="name">{{ __('static.language.languages') }}</label>
                            <div class="col-md-10">
                                <ul class="language-list">
                                    @forelse (getLanguages() as $lang)
                                        <li>
                                            <a href="{{ route('admin.faq.edit', ['faq' => $faqCategory->id, 'locale' => $lang->locale]) }}"
                                                class="language-switcher {{ request('locale') === $lang->locale ? 'active' : '' }}"
                                                target="_blank"><img
                                                    src="{{ @$lang?->flag ?? asset('admin/images/No-image-found.jpg') }}"
                                                    alt="">
                                                {{ @$lang?->name }} ({{ @$lang?->locale }})<i
                                                    class="ri-arrow-right-up-line"></i></a>
                                        </li>
                                    @empty
                                        <li>
                                            <a href="{{ route('admin.faq-category.edit', ['faqCategory' => $faqCategory->id, 'locale' => Session::get('locale', 'en')]) }}"
                                            class="language-switcher active" target="blank"><img
                                            src="{{ asset('admin/images/flags/LR.png') }}" alt="">English<i
                                            class="ri-arrow-right-up-line"></i></a>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endisset
                    <input type="hidden" name="locale" value="{{ request('locale') }}">
                    <form id="faqForm" method="POST"
                        action="{{ isset($faqCategory) ? route('admin.faq-category.update', $faqCategory->id) : route('admin.faq-category.store') }}">
                        @csrf
                        @if (isset($faqCategory))
                            @method('PUT')
                        @endif
                        <div class="accordion" id="accordionExample">
                            @if (isset($faqCategory))
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading0">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                            {{ __('static.faq_categories.faq_prefix') }} #1
                                            <i class="ri-arrow-up-s-line"></i>
                                        </button>
                                    </h2>
                                    <div id="collapse0" class="accordion-collapse collapse show"
                                        aria-labelledby="heading0" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="form-group row">
                                                <label class="col-md-2" for="name0">{{ __('static.faq_categories.name') }}
                                                    <span>
                                                        *</span></label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="text" name="name"
                                                        id="name0"
                                                        placeholder="{{ __('static.faq_categories.enter_name') }}({{ request('locale', app()->getLocale()) }})"
                                                        value="{{ isset($faqCategory->name) ? $faqCategory->getTranslation('name', request('locale', app()->getLocale())) : old('name') }}"
                                                        required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Initial FAQ section -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading0">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapse0" aria-expanded="true" aria-controls="collapse0">
                                            {{ __('static.faq_categories.faq_prefix') }} #1
                                            <i class="ri-arrow-up-s-line"></i>
                                        </button>
                                    </h2>
                                    <div id="collapse0" class="accordion-collapse collapse show"
                                        aria-labelledby="heading0" data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="form-group row">
                                                <label class="col-md-2" for="name0">{{ __('static.faq_categories.name') }}
                                                    <span>
                                                        *</span></label>
                                                <div class="col-md-10">
                                                    <input class="form-control" type="text" name="faqCategories[0][name]"
                                                        id="name0"
                                                        placeholder="{{ __('static.faq_categories.enter_name') }}({{ request('locale', app()->getLocale()) }})"
                                                        required>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-12">
                                                    <div class="submit-btn">
                                                        <button type="button"
                                                            class="btn remove-faq">{{ __('static.faq_categories.delete') }}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="form-group-row">
                                <div class="submit-btn">
                                    <button type="submit" name="save" class="btn btn-solid spinner-btn">
                                        <i class="ri-save-line text-white lh-1"></i>{{ __('static.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function($) {
            "use strict";
            (function($) {
                "use strict";
                $(document).ready(function() {

                    $("#faqForm").validate({
                        ignore: [],
                        rules: {

                            "faqs[][title]": "required",
                            "faqs[][description]": "required"
                        }
                    });

                    let faqIndex = 1;
                    let faqStates = {};

                    function addFaqSection() {
                        var inputGroup = $('.accordion-item').first().clone();

                        var newId = 'collapse' + faqIndex;
                        var newHeadingId = 'heading' + faqIndex;

                        inputGroup.find('.accordion-button')
                            .attr('data-bs-target', '#' + newId)
                            .attr('aria-controls', newId)
                            .text("{{ __('static.faqs.faq_prefix') }} #" + (faqIndex + 1));


                        inputGroup.find('.accordion-button i.ri-arrow-up-s-line').remove();
                        var newIcon = $('<i>').addClass('ri-arrow-up-s-line');
                        inputGroup.find('.accordion-button').append(newIcon);

                        inputGroup.find('.accordion-collapse')
                            .attr('id', newId)
                            .attr('aria-labelledby', newHeadingId)
                            .collapse('show');

                        inputGroup.find('input[name^="faqs"]').each(function() {
                            var oldName = $(this).attr('name');
                            var newName = oldName.replace(/\[\d+\]/, '[' + faqIndex + ']');
                            $(this).attr('name', newName).attr('id', 'title' + faqIndex).val('');
                        });

                        inputGroup.find('textarea[name^="faqs"]').each(function() {
                            var oldName = $(this).attr('name');
                            var newName = oldName.replace(/\[\d+\]/, '[' + faqIndex + ']');
                            $(this).attr('name', newName).attr('id', 'description' + faqIndex).val(
                                '');
                        });

                        inputGroup.find('.is-invalid').removeClass('is-invalid');

                        $('#accordionExample').append(inputGroup);

                        faqStates[newId] = {
                            open: true
                        };

                        faqIndex++;

                        toggleDeleteButton();
                    }

                    function toggleDeleteButton() {
                        $('.remove-faq').each(function() {
                            if ($('.accordion-item').length > 1) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    }

                    function openFirstInvalidFaq() {
                        $('.accordion-item').each(function() {
                            if ($(this).find('.is-invalid').length > 0) {
                                const targetId = $(this).find('.accordion-button').attr(
                                    'data-bs-target');
                                $(targetId).collapse('show');
                                return false;
                            }
                        });
                    }

                    $('#add-faq').on('click', function() {
                        var allInputsFilled = true;

                        $('.accordion-item').each(function() {
                            var title = $(this).find('input[name^="faqs"][name$="[title]"]')
                                .val()?.trim();
                            var description = $(this).find(
                                    'textarea[name^="faqs"][name$="[description]"]').val()
                                ?.trim();
                            if (title === '') {
                                allInputsFilled = false;
                                $(this).find('input[name^="faqs"][name$="[title]"]')
                                    .addClass('is-invalid');
                            } else {
                                $(this).find('input[name^="faqs"][name$="[title]"]')
                                    .removeClass('is-invalid');
                            }
                            if (description === '') {
                                allInputsFilled = false;
                                $(this).find(
                                        'textarea[name^="faqs"][name$="[description]"]')
                                    .addClass('is-invalid');
                            } else {
                                $(this).find(
                                        'textarea[name^="faqs"][name$="[description]"]')
                                    .removeClass('is-invalid');
                            }
                        });

                        if (!allInputsFilled) {
                            openFirstInvalidFaq();
                            return;
                        }

                        $('.accordion-collapse').collapse('hide');
                        addFaqSection();
                    });

                    $(document).on('click', '.remove-faq', function() {
                        if ($('.accordion-item').length > 1) {
                            var targetCollapse = $(this).closest('.accordion-item').find(
                                '.accordion-collapse').attr('id');
                            faqStates[targetCollapse] = {
                                open: false
                            };
                            $(this).closest('.accordion-item').remove();
                            toggleDeleteButton();
                        }
                    });

                    toggleDeleteButton();

                    $('#accordionExample').on('click', '.accordion-button', function() {
                        var targetCollapse = $(this).attr('data-bs-target');
                        var $collapse = $(targetCollapse);

                        if ($collapse.hasClass('show')) {
                            $collapse.collapse('hide');
                            faqStates[targetCollapse] = {
                                open: false
                            };
                        } else {
                            $collapse.collapse('show');
                            faqStates[targetCollapse] = {
                                open: true
                            };
                        }
                    });

                    function restoreFaqStates() {
                        $.each(faqStates, function(id, state) {
                            if (state.open) {
                                $('#' + id).collapse('show');
                            } else {
                                $('#' + id).collapse('hide');
                            }
                        });
                    }

                    restoreFaqStates();
                });
            })(jQuery);

        })(jQuery);
    </script>
@endpush
