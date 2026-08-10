@extends('installer.main')
@section('content')    
    @include('installer.components.navbar', [
        'step1' => 'fill', 
        'step2' => 'fill', 
        'step3' => 'fill', 
        'step4' => 'fill', 
        'step5' => 'active'
    ])

    <div 
        id="loader" 
        class="hidden rounded-md bg-green-100 text-green-500 font-medium text-center text-sm px-5 py-3 mb-4"
    >
        {{__('Loading...')}}
    </div>

    @include('installer.components.message')

    <h6 class="text-2xl text-gray-900 font-medium mb-10 text-center">
        {{__('You need to verify purchase before installing')}}
    </h6>

    <form action="/setup/verify-purchase" method="POST">
        @csrf

        <div class="mb-6">
            @include('installer.components.input', [
                'id' => 'purchase_key',
                'type' => 'text',
                'name' => 'purchase_key',
                'value' => session('purchase_key'),
                'label' => 'Envato Purchase Key',
                'error' => $errors->first('purchase_key'),
                'placeholder' => 'Enter your purchase key for verify',
            ])
        </div>

        <button 
            type="submit" 
            class="button w-full @if(session('success')) success @endif @if(session('error')) error @endif"
        >
            {{__('Verify Key')}}
        </button>
    </form>

    <form  action="/setup/install" method="POST">
        @csrf

        <div class="w-full flex items-center justify-end mt-12">
            <a href="/setup/step-3" class="btn btn-outline-danger">
                <button 
                    type="button" 
                    class="button !bg-transparent !text-orange-500 border border-orange-500 !font-medium"
                >
                    {{__('Previous Step')}}
                </button>
            </a>

            @if(session('success'))
                <button id="openModal" type="submit" class="button ml-4">
                    {{__('Confirm')}}
                </button>

                <div id="modal" class="hidden">
                    <div class="modal-box md:max-w-md !bg-orange-50 !text-gray-900 !p-4 !rounded-md">
                        <p className="!text-justify !font-medium !mb-6">
                            Your app is currently undergoing an automatic install. This
                            process will take a few minutes. Please don't refresh your
                            page or don't turn off your device. Just stay with this
                            process.
                        </p>
                        <div className="relative w-full bg-gray-200 rounded-full mt-6">
                            <div id="shim-blue"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
