@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Orders For Your Products') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     @if (session('success'))
                         <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Order ID') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Customer') }}</th>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Items Count (Your Products)') }}</th> {{-- Count only THIS vendor's items --}}
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Amount') }}</th> {{-- Note: This is order total, not just vendor's items total --}}
                                     <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($orders as $order)
                                    {{-- Calculate how many items in this order belong to this vendor --}}
                                    @php
                                         $vendor = Auth::user();
                                         $vendorProductIds = $vendor->products->pluck('id');
                                         $vendorItemsInThisOrder = $order->items->filter(function ($item) use ($vendorProductIds) {
                                             return $vendorProductIds->contains($item->product_id);
                                         });
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->id }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap">{{ $order->user->username ?? 'N/A' }}</td> {{-- Assuming 'user' relation is loaded --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $order->order_date->format('Y-m-d H:i') }}</td> {{-- Format date --}}
                                         <td class="px-6 py-4 whitespace-nowrap">{{ $vendorItemsInThisOrder->count() }}</td> {{-- Display count --}}
                                        <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->total_amount, 2) }}</td>
                                         <td class="px-6 py-4 whitespace-nowrap">
                                             <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100"> {{-- Customize status colors --}}
                                                {{ $order->order_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                             <a href="{{ route('vendor.product-orders.show', $order) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600 mr-3">{{ __('View Details') }}</a>
                                             {{-- Optional: Add link/button to update status if allowed --}}
                                            {{-- <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-3">Update Status</a> --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">{{ __('No orders found for your products.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                     <div class="mt-4">
                        {{ $orders->links() }} {{-- Display pagination links --}}
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection