@extends('admin.layouts.master')
@section('title', __('static.faq_categories.edit_faq_category'))
@section('content')
<div class="faq-main">
    <form id="faqForm" action="{{ route('admin.faq-category.update', $faqCategory->id) }}" method="POST" enctype="multipart/form-data">
        <div class="row g-xl-4 g-3">
            @method('PUT')
            @csrf
            @include('admin.faq_category.fields')
        </div>
    </form>
</div>
@endsection
