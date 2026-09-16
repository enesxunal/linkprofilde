@extends('layouts.auth')

@section('content')
    <div class="rounded-xl bg-white shadow-card max-w-[800px] w-full">
        <div class="px-7 pt-7 pb-4 border-b border-b-gray-200">
            <p class="text18 font-bold text-gray-900">Yeni Şifre Belirleyin</p>
            <p class="mt-1 text-sm text-gray-500">Hesabınız için yeni ve güçlü bir şifre oluşturun.</p>
        </div>

        <form class="p-7" method="POST" action="{{ route('password.store') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            @include('components.input', [
                'id' => 'reset-email',
                'type' => 'email',
                'name' => 'email',
                'value' => $email,
                'label' => 'E-posta Adresi',
                'fullWidth' => true,
                'required' => true,
                'flexLabel' => true,
                'disabled' => false,
                'error' => $errors->first('email'),
                'className' => $errors->first('email') ? '!border-red-500' : '',
                'placeholder' => 'E-posta adresiniz',
            ])

            <div class="py-6">
                @include('components.input', [
                    'id' => '',
                    'type' => 'password',
                    'name' => 'password',
                    'value' => '',
                    'label' => 'Yeni Şifre',
                    'fullWidth' => true,
                    'required' => true,
                    'flexLabel' => true,
                    'disabled' => false,
                    'error' => $errors->first('password'),
                    'className' => $errors->first('password') ? '!border-red-500' : '',
                    'placeholder' => 'Yeni şifrenizi girin',
                ])
            </div>

            @include('components.input', [
                'id' => '',
                'type' => 'password',
                'name' => 'password_confirmation',
                'value' => '',
                'label' => 'Şifre Tekrar',
                'fullWidth' => true,
                'required' => true,
                'flexLabel' => true,
                'disabled' => false,
                'error' => '',
                'className' => '',
                'placeholder' => 'Yeni şifrenizi tekrar girin',
            ])

            <div class="mt-7 md:pl-[164px]">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 active:bg-blue-700 font-medium rounded-md text14 text-white px-4 h-10">
                    {{ __('Şifreyi Güncelle') }}
                </button>
            </div>
        </form>
    </div>

    <script>
        const resetEmail = document.getElementById('reset-email');
        if (resetEmail) {
            resetEmail.setAttribute('readonly', true)
        }
    </script>
@endsection
