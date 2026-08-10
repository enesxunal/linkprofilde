@extends('layouts.dashboard.dashboard')

@section('content')
<div class="container py-4">
    <div class="row mt-5 justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header text-center">
                    {{__('Email adresinizi doğrulayın')}}
                </div>

                <div class="card-body p-4 text-center">
                    @if (session()->has('message'))
                        <div class="alert alert-success text-center" role="alert">
                            <p>{{__('E-posta adresinize yeni bir doğrulama bağlantısı gönderildi')}}</p>
                        </div>
                    @endif

                    <p>
                        {{__('Devam etmeden önce, doğrulama bağlantısı için e-postanızı kontrol etmek yerine lütfen aşağıdaki düğmeye tıklayın.')}}
                    </p>
                    <form class="mt-4" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary text-white">
                            {{__('başka bir tane istemek için burayı tıklayın')}}
                        </button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
