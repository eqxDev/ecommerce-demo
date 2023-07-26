<div>
    <!-- Filters -->
    <section aria-labelledby="filter-heading">
        <h2 id="filter-heading" class="sr-only">Filters</h2>

        <div class="border-b border-gray-200 bg-white pb-4">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div x-data="{open: false, selected: @entangle('sort').live}" class="relative inline-block text-left">
                    <div>
                        <button type="button" @click="open = !open"
                                class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900"
                                id="menu-button" aria-expanded="false" aria-haspopup="true">
                            Sort
                            <svg
                                class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </div>

                    <div x-cloak
                         x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute left-0 z-10 mt-2 w-40 origin-top-left rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                        <div class="py-1" role="none">
                            <button type="button" @click="selected = 'low-high'; open = false;" x-bind:class="selected == 'low-high' ? 'font-medium text-gray-900' : 'text-gray-500'" class="block px-4 py-2 text-sm"
                               role="menuitem" tabindex="-1" id="menu-item-0">Price: Low to High</button>
                            <button type="button" @click="selected = 'high-low'; open = false;" x-bind:class="selected == 'high-low' ? 'font-medium text-gray-900' : 'text-gray-500'" class=" block px-4 py-2 text-sm" role="menuitem"
                               tabindex="-1" id="menu-item-4">Price: High to Low</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product grid -->
    <section aria-labelledby="products-heading"
             class="mx-auto max-w-2xl px-4 pb-16 pt-12 sm:px-6 sm:pb-24 sm:pt-16 lg:max-w-7xl lg:px-8">
        <h2 id="products-heading" class="sr-only">Products</h2>

        <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
            @foreach($products as $product)
                <livewire:product-card wire:key="{{ $product->id }}" :product="$product"/>
            @endforeach
        </div>
       <div class="mt-12 w-full">
           {{count($products) > 0 ? $products->links() : ''}}
       </div>
    </section>
</div>
