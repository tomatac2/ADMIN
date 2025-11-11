@php
use App\Enums\RoleEnum;
use Modules\Taxido\Enums\RoleEnum as ModuleRole;
$settings = getSettings();
$roleCredentials = getRoleCredentials();
@endphp
@extends('auth.master')
@section('title', __('static.login'))
@push('css')
    <style>
        /* RTL Direction - Icons on the right */
        [dir="rtl"] .auth-page .auth-main .auth-card .main i {
            left: unset;
            right: calc(10px + (14 - 10) * ((100vw - 320px) / (1920 - 320)));
            padding-right: unset;
            padding-left: calc(30px + (33 - 30) * ((100vw - 320px) / (1920 - 320)));
        }

        [dir="rtl"] .auth-page .auth-main .auth-card .main .position-relative .toggle-password {
            right: unset;
            left: calc(12px + (16 - 12) * ((100vw - 320px) / (1920 - 320)));
        }

        [dir="rtl"] .auth-page .form-group .form-control {
            padding-left: calc(8px + (10 - 8) * ((100vw - 320px) / (1920 - 320)));
            padding-right: calc(48px + (57 - 48) * ((100vw - 320px) / (1920 - 320)));
        }

        [dir="rtl"] .auth-page .form-group .form-control.input-icon {
            padding: calc(8px + (10 - 8) * ((100vw - 320px) / (1920 - 320))) calc(48px + (57 - 48) * ((100vw - 320px) / (1920 - 320)));
        }

        [dir="rtl"] .auth-page .auth-main .auth-card .welcome {
            border-left: unset;
            border-right: 3px solid #222222;
            padding: 4px calc(10px + (16 - 10) * ((100vw - 320px) / (1920 - 320))) 4px 0;
        }

        /* LTR Direction - Icons on the left (default) */
        [dir="ltr"] .auth-page .auth-main .auth-card .main i {
            left: calc(10px + (14 - 10) * ((100vw - 320px) / (1920 - 320)));
            right: unset;
            padding-right: calc(30px + (33 - 30) * ((100vw - 320px) / (1920 - 320)));
            padding-left: unset;
        }

        [dir="ltr"] .auth-page .auth-main .auth-card .main .position-relative .toggle-password {
            right: calc(12px + (16 - 12) * ((100vw - 320px) / (1920 - 320)));
            left: unset;
        }

        [dir="ltr"] .auth-page .form-group .form-control {
            padding-left: calc(48px + (57 - 48) * ((100vw - 320px) / (1920 - 320)));
            padding-right: calc(8px + (10 - 8) * ((100vw - 320px) / (1920 - 320)));
        }

        [dir="ltr"] .auth-page .form-group .form-control.input-icon {
            padding: calc(8px + (10 - 8) * ((100vw - 320px) / (1920 - 320))) calc(48px + (57 - 48) * ((100vw - 320px) / (1920 - 320)));
        }

        [dir="ltr"] .auth-page .auth-main .auth-card .welcome {
            border-left: 3px solid #222222;
            border-right: unset;
            padding: 4px 0 4px calc(10px + (16 - 10) * ((100vw - 320px) / (1920 - 320)));
        }
    </style>
@endpush
@section('content')
    <section class="auth-page">
        @if (env('APP_VERSION'))
            <span class="ms-auto d-flex badge badge-version-primary">{{ __('static.version') }}{{ env('APP_VERSION') }}
            </span>
        @endif
        <div class="container">
            <div class="auth-main">
                <div class="auth-card">
                    <div class="text-center">
                        <div class="language-links text-end mb-3 d-flex justify-content-end gap-2">
                            @foreach (getLanguages() as $lang)
                                @php $isActive = Session::get('locale') === $lang->locale; @endphp
                                <a href="{{ $isActive ? 'javascript:void(0)' : route('lang', $lang->locale) }}"
                                   class="{{ $isActive ? 'opacity-50 pointer-events-none' : '' }}">
                                    <img src="{{ $lang->flag ?? asset('images/flags/default.png') }}"
                                         alt="{{ $lang->locale }}" width="28" height="20" style="border-radius:4px;">
                                </a>
                            @endforeach
                        </div>

                    @if (isset(getSettings()['general']['light_logo_image']))
                            <img class="login-img" src="{{ getSettings()['general']['light_logo_image']?->original_url }}" alt="logo" loading="lazy">
                        @else
                            <h2>{{ env('APP_NAME') }}</h2>
                        @endif
                    </div>
                    <div class="welcome">
                        <h3>{{ __('static.welcome', ['appName' => env('APP_NAME')]) }}</h3>
                        <p>{{ __('static.information') }}</p>
                    </div>
                    <div class="main">
                        <form id="loginForm" action="{{ route('login') }}" method='POST'>
                            @csrf
                            <div class="form-group">
                                <i class="ri-mail-line divider"></i>
                                <div class="position-relative">
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="{{ __('static.enter_email') }}" required>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <i class="ri-lock-line divider"></i>
                                <div class="position-relative">
                                    <input type="password" class="form-control input-icon" id="password" name="password"
                                        placeholder="{{ __('static.enter_password') }}" required>
                                    <i class="ri-eye-line toggle-password"></i>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            @if (Route::has('password.request'))
                                <div class="form-terms form-group">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check p-0">
                                            <input type="checkbox" class="item-checkbox form-check-input me-2"
                                                id="remember" name="remember">
                                            <label for="remember">{{ __('static.remember_me') }}</label>
                                        </div>
                                    </div>
{{--                                    <a href="{{ route('password.request') }}"--}}
{{--                                        class="forgot-pass">{{ __('static.users.lost_your_password') }}</a>--}}
                                </div>
                            @endif
                            <button type="submit" class="btn btn-solid justify-content-center w-100 spinner-btn mt-0">
                                {{ __('static.login') }}
                            </button>
                        </form>
                    </div>
{{--                    @isset($settings['activation']['default_credentials'])--}}
{{--                        @if ((int) $settings['activation']['default_credentials'])--}}
{{--                            <div class="demo-credential">--}}
{{--                                @foreach ($roleCredentials as $role)--}}
{{--                                    <button class="btn btn-solid default-credentials" data-email="{{ $role['email'] }}" data-password="{{ $role['password'] ?? '123456789' }}" >--}}
{{--                                        {{ ucfirst($role['role']) }}--}}
{{--                                    </button>--}}
{{--                                @endforeach--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                    @endisset--}}
                </div>
            </div>
        </div>
    </section>
@endsection

@push('css')
    <style>
        .hover-underline:hover {
            text-decoration: underline;
        }
        .language-links {
            font-size: 14px;
            letter-spacing: 0.5px;
        }
    </style>
@endpush
@push('scripts')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                $('#loginForm').validate({
                    rules: {
                        email: {
                            required: true,
                            email: true,
                        },
                        password: {
                            required: true
                        },
                    }
                });

                $(".default-credentials").click(function() {
                    $("#email").val("");
                    $("#password").val("");
                    var email = $(this).data("email");
                    var password = $(this).data("password");
                    $("#email").val(email);
                    $("#password").val(password);
                });
            });
        })(jQuery);
    </script>
@endpush
