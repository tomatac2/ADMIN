@extends('admin.layouts.master')
@section('title', __('static.faq_categories.add_faq_category'))
@section('content')
<div class="faq-create">
    <form id="faqForm" action="{{ route('admin.faq-category.store') }}" method="POST" enctype="multipart/form-data">
        <div class="row g-xl-4 g-3">
            @method('POST')
            @csrf
            @include('admin.faq_category.fields')
        </div>
    </form>
</div>
@endsection
