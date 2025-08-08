@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <x-card>
                <x-slot:title>
                    @lang('Подтвердите свой адрес электронной почты')
                </x-slot>

                @if (session('status') == 'verification-link-sent')
                    <x-alert type="success">
                        @lang('На ваш адрес электронной почты отправлена новая ссылка для подтверждения.')
                    </x-alert>
                @endif

                @lang('Прежде чем продолжить, проверьте свою электронную почту на наличие проверочной ссылки.')
                @lang('Если вы не получили письмо по электронной почте'),

                <form action="{{ route('verification.send') }}" method="POST" class="d-inline" role="form">
                    @csrf

                    <input type="submit" class="btn btn-link p-0 m-0 align-baseline" value="@lang('нажмите сюда, чтобы запросить другой')">
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
