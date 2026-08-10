<?php
    $payment_methods = [
        [
            'id' => 'tosla',
            'name' => 'Tosla',
            'method' => 'POST',
            'route' => '/tosla/payment',
            'active' => 0,
            'logo' => asset('assets/icons/check-mark.svg'),
        ],
    ];

    foreach ($methods as $method) {
        if ($method->name === 'tosla') {
            $payment_methods[0]['active'] = $method->active;
            break;
        }
    }
?>

<h6 class="text-xl text-gray-600 font-medium mb-6" >
    {{__('Available Payment methods')}}
</h6>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($payment_methods as $item)
        @if ($item['active'])
        <div 
            id="{{$item['id']}}" 
            data-info="{{json_encode($item)}}"
            class="method payment_method p-6 rounded-lg cursor-pointer outline outline-1 hover:outline-2 hover:outline-blue-500"
        >
            <div class="flex items-center justify-between h-full">
                <p class="text-lg font-medium">{{$item['name']}}</p>
                <img 
                    src="{{$item['logo']}}" 
                    width="44" 
                    alt="fastai-uilib"
                >
            </div>
        </div>
        @endif
    @endforeach
</div>