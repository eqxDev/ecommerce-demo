<div class="bg-white">
    <div class="pb-16 pt-6 sm:pb-24">
        <div class="mx-auto mt-8 max-w-2xl px-4 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="lg:grid lg:auto-rows-min lg:grid-cols-12 lg:gap-x-8">
                <div class="lg:col-span-5 lg:col-start-8">
                    <div class="flex justify-between">
                        <h1 class="text-xl font-medium text-gray-900">{{$product->name}}</h1>
                        <p class="text-xl font-medium text-gray-900">
                            @if($size_id == null || $colour_id == null)
                                from &pound;{{$min_variant_price}}
                            @else
                            &pound;{{$variant_price}}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Image gallery -->
                <div class="mt-8 lg:col-span-7 lg:col-start-1 lg:row-span-3 lg:row-start-1 lg:mt-0">
                    <h2 class="sr-only">Images</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 lg:grid-rows-3 lg:gap-8">
                        <img src="{{$product->image_url}}" alt="{{$product->name}}"
                             class="lg:col-span-2 lg:row-span-2 rounded-lg">
                    </div>
                </div>

                <div class="mt-8 lg:col-span-5">
                    <form>
                        <!-- Colour picker -->
                        <div>
                            <h2 class="text-sm font-medium text-gray-900">Colour</h2>

                            <fieldset class="mt-2" x-data="{selected: @entangle('colour_id')}">
                                <legend class="sr-only">Choose a colour</legend>
                                <div class="flex items-center space-x-3">
                                    @foreach($colour_options as $option)
                                        <label wire:key="colour-{{$option['colour_id']}}"
                                               @if($option['disabled'] == false) wire:click="setColour({{$option['colour_id']}})"  @endif
                                               x-bind:class="selected == {{$option['colour_id']}} ? 'ring ring-offset-1' : 'ring-0'"
                                               style="--tw-ring-color: {{$option['hex']}};"
                                               class="@if($option['disabled']) opacity-25 cursor-not-allowed @else cursor-pointer @endif relative -m-0.5 flex items-center justify-center rounded-full p-0.5 focus:outline-none">
                                            <input type="radio" name="colour-choice" value="{{$option['colour_id']}}"
                                                   class="sr-only" aria-labelledby="colour-choice-0-label">
                                            <span id="colour-choice-0-label" class="sr-only">{{$option['name']}}</span>
                                            <span aria-hidden="true" style="background-color: {{$option['hex']}}"
                                                  class="h-8 w-8 rounded-full border border-black border-opacity-10"></span>
                                        </label>
                                    @endforeach

                                </div>
                            </fieldset>
                        </div>

                        <!-- Size picker -->
                        <div class="mt-8">
                            <div class="flex items-center justify-between">
                                <h2 class="text-sm font-medium text-gray-900">Size</h2>
                            </div>

                            <fieldset class="mt-2" x-data="{selected: @entangle('size_id')}">
                                <legend class="sr-only">Choose a size</legend>
                                <div class="grid grid-cols-3 gap-3 sm:grid-cols-6">
                                    @foreach($size_options as $option)
                                        <label wire:key="size-{{$option['size_id']}}"
                                               @if($option['disabled'] == false) wire:click="setSize({{$option['size_id']}})" @endif
                                               x-bind:class="selected == {{$option['size_id']}} ? 'border-transparent bg-indigo-600 text-white hover:bg-indigo-700' : 'border-gray-200 bg-white text-gray-900 hover:bg-gray-50'"
                                            class="@if($option['disabled']) opacity-25 cursor-not-allowed @else cursor-pointer @endif flex items-center justify-center rounded-md border py-3 px-3 text-sm font-medium uppercase sm:flex-1 focus:outline-none">
                                            <input type="radio" name="size-choice" value="XXS" class="sr-only"
                                                   aria-labelledby="size-choice-0-label">
                                            <span id="size-choice-0-label">{{$option['name']}}</span>
                                        </label>


                                    @endforeach
                                </div>
                            </fieldset>
                        </div>

                        <button type="button" wire:click="addToCart()" @if($variantNotAvailable || ($size_id == null || $colour_id == null)) disabled @endif
                                class=" @if($variantNotAvailable || ($size_id == null || $colour_id == null)) opacity-25 cursor-not-allowed @endif mt-8 flex w-full items-center justify-center rounded-md border border-transparent bg-indigo-600 px-8 py-3 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Add to cart
                        </button>
                    </form>

                    <!-- Product details -->
                    <div class="mt-10">
                        <h2 class="text-sm font-medium text-gray-900">Description</h2>

                        <div class="prose prose-sm mt-4 text-gray-500">
                            <p>{{$product->description}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
