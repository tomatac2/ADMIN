@extends('admin.layouts.master')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/vendors/flatpickr.min.css')}}">
@endpush

@section('title', __('static.zone_management'))
@section('content')
    <div class="row dashboard-default">
        <div class="col-12">
            <div class="default-sorting mt-0">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="welcome-box">
                            <div class="d-flex">
                                <h2>{{ __('static.widgets.hello') }}, {{ getCurrentUser()->name }}</h2>
                                <img src="{{ asset('images/dashboard/hand.gif') }}" alt="">
                            </div>
                            <div class="animation-slides"></div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <form action="{{ route('admin.dashboard.index') }}" method="GET" id="sort-form">
                            <div class="support-title sorting m-0">
                                <div class="select-sorting">
                                    <label for="sort">{{ __('static.sort_by') }}</label>
                                    <div class="select-form">
                                        <select class="select-2 form-control sort" id="sort" name="sort">
                                            <option class="select-placeholder" value="today"
                                                {{ request('sort') == 'today' ? 'selected' : '' }}>
                                                {{ __('static.today') }}
                                            </option>
                                            <option class="select-placeholder" value="this_week"
                                                {{ request('sort') == 'this_week' ? 'selected' : '' }}>
                                                {{ __('static.this_week') }}
                                            </option>
                                            <option class="select-placeholder" value="this_month"
                                                {{ request('sort') == 'this_month' ? 'selected' : '' }}>
                                                {{ __('static.this_month') }}
                                            </option>
                                            <option class="select-placeholder" value="this_year"
                                                {{ request('sort') == 'this_year' || !request('sort') ? 'selected' : '' }}>
                                                {{ __('static.this_year') }}
                                            </option>
                                            <option class="select-placeholder" value="custom"
                                                {{ request('sort') == 'custom' ? 'selected' : '' }}>
                                                {{ __('static.custom_range') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @php
                                $startDate = request('start');
                                $endDate = request('end');
                                $selectedRange = $startDate && $endDate ? "$startDate to $endDate" : '';
                            @endphp

                            <div class="form-group dashboard-datepicker {{ request('sort') == 'custom' ? '' : 'd-none' }}" id="custom-date-range">
                                <input type="text" class="form-control filter-dropdown" id="start_end_date"
                                    name="start_end_date" placeholder="{{ __('static.select_date') }}"
                                    value="{{ $selectedRange }}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ __("static.zone_management") }}</h4>
                <a href="{{ route("admin.states.create") }}" class="btn btn-primary">
                    <i class="ri-add-line"></i> {{ __("static.add_new_zone") }}
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered align-middle">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ __("static.zone_name") }}</th>
                                <th scope="col">{{ __("static.country") }}</th>
                                <th scope="col"> {{ __("static.zone_created") }}</th>
                                <th scope="col" class="text-center">{{ __("static.zone_action") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach ($zones as $zone)
                                <tr>
                                <th scope="row">{{ $zone->id }}</th>
                                <td>{{ $zone->name }}</td>
                                <td>
                                    {{ $zone->country->name }}
                                </td>
                                <td>{{ $zone->created_at }}</td>
                                <td class="d-flex ">
                                     <a href="{{ route("admin.states.show", $zone->id) }}" class="btn btn-sm btn-info text-white me-2" title="{{ __("static.show") }}">
                                       <i class="ri-eye-line"></i>
                                    </a>
                                    <a href="{{ route("admin.states.edit", $zone->id) }}" class="btn btn-sm btn-warning text-white me-2" title="{{ __("static.edit") }}">
                                        <i class="ri-edit-box-line"></i>
                                    </a>
                                   
                                    
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-sm btn-danger" title="{{ __("static.delete") }}"    data-bs-toggle="modal" data-bs-target="#exampleModal{{ $zone->id }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="exampleModal{{ $zone->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __("static.delete_zone") }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.states.destroy',$zone->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-body">
                                                    {{ __( "static.zone_confirm_delete") }} <b>{{ $zone->name }}</b>
                                                </div>
                                                <div class="modal-footer p-3">
                                                    <button type="button" class="btn btn-sm btn-info" data-bs-dismiss="modal">{{ __('static.close') }}</button>
                                                    <button type="submit" class="btn btn-sm btn-danger">{{ __("static.delete_zone") }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
        {{ $zones->links() }}
    </div>
@endsection

@push('scripts')
    <script defer src="{{ asset('js/apex-chart.js') }}"></script>
    <script defer src="{{ asset('js/custom-apexchart.js') }}"></script>
    <script src="{{ asset('js/flatpickr/flatpickr.js')}}"></script>
    <script src="{{ asset('js/flatpickr/rangePlugin.js')}}"></script>

    <script>
        (function ($) {
            "use strict";

            $(document).ready(function () {
                const $sortDropdown = $("#sort");
                const $customDateRange = $("#custom-date-range");
                const $startEndDate = $("#start_end_date");
                const $animationSlides = $(".animation-slides");

                if ($sortDropdown.val() === "custom") {
                    $customDateRange.removeClass("d-none");
                }

                $sortDropdown.on("change", function () {
                    const selectedSort = $(this).val();
                    const urlParams = new URLSearchParams(window.location.search);

                    if (selectedSort === "custom") {
                        $customDateRange.removeClass("d-none");
                    } else {
                        $customDateRange.addClass("d-none");
                        urlParams.delete("start");
                        urlParams.delete("end");
                        urlParams.set("sort", selectedSort);
                        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
                    }
                });

                // Initialize Flatpickr Date Range Picker
                if ($startEndDate.length) {
                    flatpickr("#start_end_date", {
                        mode: "range",
                        dateFormat: "m-d-Y",
                        defaultDate: "{{ $selectedRange }}",
                        onClose: function (selectedDates, dateStr, instance) {
                            if (selectedDates.length === 2) {
                                const startDate = flatpickr.formatDate(selectedDates[0], "m-d-Y");
                                const endDate = flatpickr.formatDate(selectedDates[1], "m-d-Y");
                                const urlParams = new URLSearchParams(window.location.search);
                                urlParams.set("sort", "custom");
                                urlParams.set("start", startDate);
                                urlParams.set("end", endDate);
                                history.pushState(null, null, `${window.location.pathname}?${urlParams.toString()}`);
                                location.reload();
                            }
                        }
                    });
                }

                // Initialize Slick Slider if element exists
                if ($animationSlides.length) {
                    $animationSlides.slick({
                        slidesToShow: 1,
                        vertical: true,
                        autoplay: true,
                        autoplaySpeed: 1200,
                        arrows: false,
                    });

                    const slides = [
                        "<p>{{ __('static.slides.first_slide') }}</p>",
                        "<p>{{ __('static.slides.second_slide') }}</p>",
                        "<p>{{ __('static.slides.third_slide') }}</p>",
                    ];

                    slides.forEach(slide => {
                        $animationSlides.slick("slickAdd", slide);
                    });
                }
            });
        })(jQuery);
    </script>
@endpush
