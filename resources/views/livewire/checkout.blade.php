<div class="bg-white">
    <!-- Background color split screen for large screens -->
    <div class="relative mx-auto grid max-w-7xl grid-cols-1 gap-x-16 lg:grid-cols-2 lg:px-8 xl:gap-x-48">
        <h1 class="sr-only">Order information</h1>

        <section aria-labelledby="summary-heading"
                 class="bg-gray-50 px-4 pb-10 pt-16 sm:px-6 lg:col-start-2 lg:row-start-1 lg:bg-transparent lg:px-0 lg:pb-16">
            <div class="mx-auto max-w-lg lg:max-w-none">
                <h2 id="summary-heading" class="text-lg font-medium text-gray-900">Order summary</h2>

                <ul role="list" class="divide-y divide-gray-200 text-sm font-medium text-gray-900">
                    @foreach($items as $item)
                        <li class="flex items-start space-x-4 py-6">
                            <img src="{{$item['variant']['product']['image_url']}}"
                                 alt="{{$item['variant']['product']['name']}}"
                                 class="h-20 w-20 flex-none rounded-md object-cover object-center">
                            <div class="flex-auto space-y-1">
                                <h3>{{$item['variant']['product']['name']}}</h3>
                                <p class="text-gray-500">Size: {{$item['variant']['size']['name']}}</p>
                                <p class="text-gray-500">Colour: {{$item['variant']['colour']['name']}}</p>
                            </div>
                            <div>
                                <p class="flex-none text-xs">Qty: {{$item['quantity']}}</p>
                                <p class="flex-none text-base font-medium">
                                    &pound;{{$item['variant']['product']['price']}}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <dl class="hidden space-y-6 border-t border-gray-200 pt-6 text-sm font-medium text-gray-900 lg:block">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-600">Subtotal</dt>
                        <dd>&pound;{{$subtotal}}</dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <dt class="text-gray-600">VAT</dt>
                        <dd>&pound;{{$vat}}</dd>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                        <dt class="text-base">Total</dt>
                        <dd class="text-base">&pound;{{$total}}</dd>
                    </div>
                </dl>

                <div x-data="{open: false}"
                     class="fixed inset-x-0 bottom-0 flex flex-col-reverse text-sm font-medium text-gray-900 lg:hidden">
                    <div class="relative z-10 border-t border-gray-200 bg-white px-4 sm:px-6">
                        <div class="mx-auto max-w-lg">
                            <button @click="open = !open" type="button"
                                    class="flex w-full items-center py-6 font-medium" aria-expanded="false">
                                <span class="mr-auto text-base">Total</span>
                                <span class="mr-2 text-base">&pound;{{$total}}</span>
                                <svg class="h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor"
                                     aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M14.77 12.79a.75.75 0 01-1.06-.02L10 8.832 6.29 12.77a.75.75 0 11-1.08-1.04l4.25-4.5a.75.75 0 011.08 0l4.25 4.5a.75.75 0 01-.02 1.06z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <div x-cloak x-show="open"
                             x-transition:enter="transition-opacity ease-linear duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition-opacity ease-linear duration-300"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-black bg-opacity-25" aria-hidden="true"></div>
                        <div x-cloak
                             x-show="open"
                             x-transition:enter="transition ease-in-out duration-300 transform"
                             x-transition:enter-start="translate-y-full"
                             x-transition:enter-end="translate-y-0"
                             x-transition:leave="transition ease-in-out duration-300 transform"
                             x-transition:leave-start="translate-y-0"
                             x-transition:leave-end="translate-y-full"
                             class="relative bg-white px-4 py-6 sm:px-6">
                            <dl class="mx-auto max-w-lg space-y-6">
                                <div class="flex items-center justify-between">
                                    <dt class="text-gray-600">Subtotal</dt>
                                    <dd>&pound;{{$subtotal}}</dd>
                                </div>

                                <div class="flex items-center justify-between">
                                    <dt class="text-gray-600">VAT</dt>
                                    <dd>&pound;{{$vat}}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <form wire:submit="checkout" class="px-4 pb-36 pt-16 sm:px-6 lg:col-start-1 lg:row-start-1 lg:px-0 lg:pb-16">
            <div class="mx-auto max-w-lg lg:max-w-none">

                <section aria-labelledby="billing-heading" class="mt-10">
                    <h2 id="billing-heading" class="text-lg font-medium text-gray-900">Billing address</h2>

                    <div class="mt-6 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                        <div class="sm:col-span-3">
                            <label for="billing_name" class="block text-sm font-medium text-gray-700">Name</label>
                            <div class="mt-1">
                                <input type="text" id="billing_name" name="billing_name" wire:model="order_data.billing_name"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.billing_name')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="billing_address" class="block text-sm font-medium text-gray-700">Address</label>
                            <div class="mt-1">
                                <input type="text" id="billing_address" name="billing_address" wire:model="order_data.billing_address"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.billing_address')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="billing_address2" class="block text-sm font-medium text-gray-700">Address Line 2</label>
                            <div class="mt-1">
                                <input type="text" id="billing_address2" name="billing_address2" wire:model="order_data.billing_address2"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.billing_address2')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="billing_city" class="block text-sm font-medium text-gray-700">City</label>
                            <div class="mt-1">
                                <input type="text" id="billing_city" name="billing_city" wire:model="order_data.billing_city"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.billing_city')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="billing_county" class="block text-sm font-medium text-gray-700">County</label>
                            <div class="mt-1">
                                <input type="text" id="billing_county" name="billing_county" wire:model="order_data.billing_county"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.billing_county')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="billing_postcode" class="block text-sm font-medium text-gray-700">Postal code</label>
                            <div class="mt-1">
                                <input type="text" id="billing_postcode" name="billing_postcode" wire:model="order_data.billing_postcode"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.billing_postcode')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <section x-data="{open: @entangle('order_data.shipping_copy')}" aria-labelledby="shipping-heading" class="mt-10">
                    <h2 id="shipping-heading" class="text-lg font-medium text-gray-900">Shipping information</h2>

                    <div class="mt-6 flex items-center">
                        <input id="shipping_copy" name="shipping_copy" type="checkbox" checked wire:model="order_data.shipping_copy"
                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <div class="ml-2">
                            <label for="shipping_copy" class="text-sm font-medium text-gray-900">Same as billing
                                information</label>
                        </div>
                        @error('order_data.shipping_copy')
                        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div x-show="!open" class="mt-6 grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                        <div class="sm:col-span-3">
                            <label for="shipping_name" class="block text-sm font-medium text-gray-700">Name</label>
                            <div class="mt-1">
                                <input type="text" id="shipping_name" name="shipping_name" wire:model="order_data.shipping_name"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.shipping_name')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700">Address</label>
                            <div class="mt-1">
                                <input type="text" id="shipping_address" name="shipping_address" wire:model="order_data.shipping_address"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.shipping_address')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="sm:col-span-3">
                            <label for="shipping_address2" class="block text-sm font-medium text-gray-700">Address Line 2</label>
                            <div class="mt-1">
                                <input type="text" id="shipping_address2" name="shipping_address2" wire:model="order_data.shipping_address2"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.shipping_address2')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_city" class="block text-sm font-medium text-gray-700">City</label>
                            <div class="mt-1">
                                <input type="text" id="shipping_city" name="shipping_city" wire:model="order_data.shipping_city"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.shipping_city')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_county" class="block text-sm font-medium text-gray-700">County</label>
                            <div class="mt-1">
                                <input type="text" id="shipping_county" name="shipping_county" wire:model="order_data.shipping_county"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.shipping_county')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="shipping_postcode" class="block text-sm font-medium text-gray-700">Postal code</label>
                            <div class="mt-1">
                                <input type="text" id="shipping_postcode" name="shipping_postcode" wire:model="order_data.shipping_postcode"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            @error('order_data.shipping_postcode')
                            <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </section>

                <div class="mt-10 border-t border-gray-200 pt-6 sm:flex sm:items-center sm:justify-between">
                    <button type="submit"
                            class="w-full rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50 sm:order-last sm:ml-6 sm:w-auto">
                        Purchase
                    </button>
                    <p class="mt-4 text-center text-sm text-gray-500 sm:mt-0 sm:text-left">Checkout with PayPal.</p>
                </div>
            </div>
        </form>
    </div>
</div>
