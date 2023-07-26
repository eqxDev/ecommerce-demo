<div class="bg-white">
    <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
        <div class="max-w-xl">
            <h1 class="text-base font-medium text-indigo-600">Thank you!</h1>
            <p class="mt-2 text-4xl font-bold tracking-tight sm:text-5xl">It's on the way!</p>
            <p class="mt-2 text-base text-gray-500">Your order #{{$order->id}} has shipped and will be with you soon.</p>
        </div>

        <div class="mt-10 border-t border-gray-200">
            <h2 class="sr-only">Your order</h2>

            <h3 class="sr-only">Items</h3>
        @foreach($items as $item)
                <div class="flex space-x-6 border-b border-gray-200 py-10">
                    <img src="{{$item['variant']['product']['image_url']}}" alt="{{$item['variant']['product']['name']}}" class="h-20 w-20 flex-none rounded-lg bg-gray-100 object-cover object-center sm:h-40 sm:w-40">
                    <div class="flex flex-auto flex-col">
                        <div>
                            <h4 class="font-medium text-gray-900">
                                <a href="{{route('product.view', $item['variant']['product']['id'])}}" wire:navigate>
                                    {{$item['variant']['product']['name']}} &bull; {{$item['variant']['size']['name']}} &bull; {{$item['variant']['colour']['name']}}
                                </a>
                            </h4>
                            <p class="mt-2 text-sm text-gray-600">
                                {{$item['variant']['product']['description']}}
                            </p>
                        </div>
                        <div class="mt-6 flex flex-1 items-end">
                            <dl class="flex space-x-4 divide-x divide-gray-200 text-sm sm:space-x-6">
                                <div class="flex">
                                    <dt class="font-medium text-gray-900">Quantity</dt>
                                    <dd class="ml-2 text-gray-700">{{$item['quantity']}}</dd>
                                </div>
                                <div class="flex pl-4 sm:pl-6">
                                    <dt class="font-medium text-gray-900">Price</dt>
                                    <dd class="ml-2 text-gray-700">&pound;{{$item['price']}}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
        @endforeach

            <div class="sm:ml-40 sm:pl-6">
                <h3 class="sr-only">Your information</h3>

                <h4 class="sr-only">Addresses</h4>
                <dl class="grid grid-cols-2 gap-x-6 py-10 text-sm">
                    <div>
                        <dt class="font-medium text-gray-900">Shipping address</dt>
                        <dd class="mt-2 text-gray-700">
                            <address class="not-italic">
                                <span class="block">{{$order_data['shipping_name']}}</span>
                                <span class="block">{{$order_data['shipping_address']}}</span>
                                @if(!empty($order_data['shipping_address2']))
                                    <span class="block">{{$order_data['shipping_address2']}}</span>
                                @endif
                                <span class="block">{{$order_data['shipping_city']}}, {{$order_data['shipping_postcode']}}</span>
                            </address>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900">Billing address</dt>
                        <dd class="mt-2 text-gray-700">
                            <address class="not-italic">
                                <span class="block">{{$order_data['billing_name']}}</span>
                                <span class="block">{{$order_data['billing_address']}}</span>
                                @if(!empty($order_data['billing_address2']))
                                <span class="block">{{$order_data['billing_address2']}}</span>
                                @endif
                                <span class="block">{{$order_data['billing_city']}}, {{$order_data['billing_postcode']}}</span>
                            </address>
                        </dd>
                    </div>
                </dl>

                <h3 class="sr-only">Summary</h3>

                <dl class="space-y-6 border-t border-gray-200 pt-10 text-sm">
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-900">Subtotal</dt>
                        <dd class="text-gray-700">&pound;{{$subtotal}}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-900">VAT</dt>
                        <dd class="text-gray-700">&pound;{{$vat}}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-900">Total</dt>
                        <dd class="text-gray-900">&pound;{{$order->total}}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
