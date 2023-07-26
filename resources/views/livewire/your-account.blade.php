<div class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:pb-24">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="max-w-xl">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Order history</h1>
            <p class="mt-2 text-sm text-gray-500">Check the status of recent orders, manage returns, and download invoices.</p>
        </div>

        <div class="mt-16">
            <h2 class="sr-only">Recent orders</h2>

            <div class="space-y-20">

                @foreach($orders as $order)
                    <div>
                        <h3 class="sr-only">Order placed on <time datetime="{{$order->created_at->format('Y-m-d')}}">{{$order->created_at->format('F d, Y')}}</time></h3>

                        <div class="rounded-lg bg-gray-50 px-4 py-6 sm:flex sm:items-center sm:justify-between sm:space-x-6 sm:px-6 lg:space-x-8">
                            <dl class="flex-auto space-y-6 divide-y divide-gray-200 text-sm text-gray-600 sm:grid sm:grid-cols-4 sm:gap-x-6 sm:space-y-0 sm:divide-y-0 lg:w-1/2 lg:flex-none lg:gap-x-8">
                                <div class="flex justify-between sm:block">
                                    <dt class="font-medium text-gray-900">Date placed</dt>
                                    <dd class="sm:mt-1">
                                        <time datetime="{{$order->created_at->format('Y-m-d')}}">{{$order->created_at->format('F d, Y')}}</time>
                                    </dd>
                                </div>
                                <div class="flex justify-between pt-6 sm:block sm:pt-0">
                                    <dt class="font-medium text-gray-900">Order number</dt>
                                    <dd class="sm:mt-1">{{$order->id}}</dd>
                                </div>
                                <div class="flex justify-between pt-6 sm:block sm:pt-0">
                                    <dt class="font-medium text-gray-900">Status</dt>
                                    <dd class="sm:mt-1">{{ucwords($order->status)}}</dd>
                                </div>
                                <div class="flex justify-between pt-6 font-medium text-gray-900 sm:block sm:pt-0">
                                    <dt class="font-medium text-gray-900">Total amount</dt>
                                    <dd class="sm:mt-1">&pound;{{$order->total}}</dd>
                                </div>
                            </dl>
                        </div>

                        <table class="mt-4 w-full text-gray-500 sm:mt-6">
                            <caption class="sr-only">
                                Products
                            </caption>
                            <thead class="sr-only text-left text-sm text-gray-500 sm:not-sr-only">
                            <tr>
                                <th scope="col" class="py-3 pr-8 font-normal sm:w-2/5 lg:w-1/3">Product</th>
                                <th scope="col" class="w-1/5 py-3 pr-8 font-normal">Quantity</th>
                                <th scope="col" class="hidden w-1/5 py-3 pr-8 font-normal sm:table-cell">Price</th>
                                <th scope="col" class="w-0 py-3 text-right font-normal">Info</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 border-b border-gray-200 text-sm sm:border-t">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-6 pr-8">
                                        <div class="flex items-center">
                                            <img src="{{$item->productVariant->product->image_url}}" alt="{{$item->productVariant->product->name}}" class="mr-6 h-16 w-16 rounded object-cover object-center">
                                            <div>
                                                <div class="font-medium text-gray-900">{{$item->productVariant->product->name}}</div>
                                                <div class="text-xs text-gray-900">Size: {{$item->productVariant->size->name}}</div>
                                                <div class="text-xs text-gray-900">Colour: {{$item->productVariant->colour->name}}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden py-6 pr-8 sm:table-cell">{{$item->quantity}}</td>
                                    <td class="py-6 pr-8">
                                        <div class="mt-1 sm:hidden">Qty: {{$item->quantity}}</div>
                                        &pound;{{$item->price}}</td>
                                    <td class="whitespace-nowrap py-6 text-right font-medium">
                                        <a href="{{route('product.view', $item->productVariant->product_id)}}" wire:navigate class="text-indigo-600">View<span class="hidden lg:inline"> Product</span><span class="sr-only">, Machined Pen and Pencil Set</span></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
{{$orders->links()}}
            </div>
        </div>
    </div>
</div>
