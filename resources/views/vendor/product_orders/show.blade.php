@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Order Details') }} (ID: {{ $productOrder->id }})
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Order ID') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $productOrder->id }}</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Customer') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $productOrder->user->username ?? 'N/A' }}</p> {{-- Assuming 'user' relation loaded --}}
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Order Date') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $productOrder->order_date->format('Y-m-d H:i') }}</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total Amount') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($productOrder->total_amount, 2) }}</p>
                         </div>
                          <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Status') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $productOrder->order_status }}</p>
                         </div>
                         <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Payment Status') }}</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $productOrder->payment_status ?? 'N/A' }}</p> {{-- Assuming Payment Status column is used/needed --}}
                         </div>
                         <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Shipping Address') }}</p>
                             <div class="mt-1 text-gray-900 dark:text-gray-100">
                                {{ $productOrder->shipping_address_line1 ?? '' }}<br>
                                {{ $productOrder->shipping_address_line2 ?? '' }}<br>
                                {{ $productOrder->shipping_city ?? '' }}, {{ $productOrder->shipping_postal_code ?? '' }}<br>
                                {{ $productOrder->shipping_country ?? '' }}
                             </div>
                         </div>
                     </div>

                    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100 mb-4">{{ __('Items in This Order') }}</h3>
                    <div class="overflow-x-auto">
                         <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price at Purchase') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($productOrder->items as $item) {{-- Loop through all items in the order --}}
                                     {{-- You might filter items here if you only want to show the vendor's items --}}
                                     @php
                                         $vendor = Auth::user();
                                         $vendorProductIds = $vendor->products->pluck('id');
                                     @endphp
                                    @if ($vendorProductIds->contains($item->product_id)) {{-- Show only items belonging to this vendor --}}
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->product->name ?? 'N/A' }}</td> {{-- Assuming 'product' relation loaded --}}
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($item->price_at_purchase, 2) }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('vendor.product-orders.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">{{ __('Back to Orders List') }}</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection