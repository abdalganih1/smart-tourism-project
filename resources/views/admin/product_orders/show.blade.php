@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Order Details') }}: #{{ $productOrder->id }}
    </h2>
@endsection

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                     {{-- Optional: Action Buttons (e.g., Update Status) --}}
                    <div class="mb-4 flex justify-end">
                         {{-- Add buttons for status updates if you define custom routes/methods in controller --}}
                         {{-- <button class="inline-flex items-center px-4 py-2 bg-indigo-600 ...">Mark as Processing</button> --}}
                         {{-- <button class="inline-flex items-center px-4 py-2 bg-green-600 ...">Mark as Shipped</button> --}}
                         {{-- <button class="inline-flex items-center px-4 py-2 bg-red-600 ...">Cancel Order</button> --}}
                    </div>

                    {{-- Order Summary --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Order Summary') }}</h3>
                         <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                             <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                 <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Order ID') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $productOrder->id }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Order Date') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $productOrder->order_date->format('Y-m-d H:i:s') }}</dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Order Status') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                         <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                             @if($productOrder->order_status === 'Delivered') bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100
                                             @elseif($productOrder->order_status === 'Pending' || $productOrder->order_status === 'Processing') bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100
                                             @elseif($productOrder->order_status === 'Cancelled' || $productOrder->order_status === 'PaymentFailed') bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100
                                             @else bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100
                                             @endif">
                                             {{ $productOrder->order_status }}
                                         </span>
                                     </dd>
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Payment Status') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $productOrder->payment_status ?? '-' }}</dd> {{-- Assuming payment_status exists in your schema --}}
                                 </div>
                                  <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                     <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Total Amount') }}</dt>
                                     <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ number_format($productOrder->total_amount, 2) }}</dd>
                                 </div>
                                  @if ($productOrder->payment_transaction_id)
                                      <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                         <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Transaction ID') }}</dt>
                                         <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $productOrder->payment_transaction_id }}</dd>
                                     </div>
                                  @endif
                             </dl>
                         </div>
                    </div>

                     {{-- Customer Information --}}
                    @if ($productOrder->user)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Customer Information') }}</h3>
                             <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                 <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                      <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('User') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">
                                              {{ $productOrder->user->username }}
                                              @if ($productOrder->user->profile)
                                                  ({{ $productOrder->user->profile->first_name }} {{ $productOrder->user->profile->last_name }})
                                              @endif
                                          </dd>
                                      </div>
                                       <div class="px-4 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                          <dt class="text-sm font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Email') }}</dt>
                                          <dd class="mt-1 text-sm leading-6 text-gray-700 dark:text-gray-300 sm:col-span-2 sm:mt-0">{{ $productOrder->user->email }}</dd>
                                      </div>
                                       {{-- Optional: Display phone numbers if loaded --}}
                                        {{-- @if ($productOrder->user->phoneNumbers) ... @endif --}}
                                 </dl>
                             </div>
                        </div>
                    @endif

                     {{-- Shipping Address --}}
                     @if ($productOrder->shipping_address_line1)
                          <div class="mb-8">
                               <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Shipping Address') }}</h3>
                                <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                    <address class="text-sm leading-6 text-gray-700 dark:text-gray-300">
                                        {{ $productOrder->shipping_address_line1 }}<br>
                                        @if ($productOrder->shipping_address_line2)
                                            {{ $productOrder->shipping_address_line2 }}<br>
                                        @endif
                                        {{ $productOrder->shipping_city }}<br>
                                        {{ $productOrder->shipping_postal_code }}<br>
                                        {{ $productOrder->shipping_country }}
                                    </address>
                                </div>
                          </div>
                     @endif


                    {{-- Order Items --}}
                    @if ($productOrder->items->count() > 0)
                        <div class="mb-8">
                             <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">{{ __('Order Items') }}</h3>
                             <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                                 <div class="overflow-x-auto">
                                     <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                         <thead class="bg-gray-50 dark:bg-gray-700">
                                             <tr>
                                                 <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                     {{ __('Product Name') }}
                                                 </th>
                                                 <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                     {{ __('Quantity') }}
                                                 </th>
                                                 <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                     {{ __('Price at Purchase') }}
                                                 </th>
                                                  <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                     {{ __('Subtotal') }}
                                                 </th>
                                             </tr>
                                         </thead>
                                         <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                              @foreach ($productOrder->items as $item)
                                                 <tr>
                                                     <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                                         {{ $item->product->name ?? 'N/A' }}
                                                     </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                         {{ $item->quantity }}
                                                     </td>
                                                     <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                          {{ number_format($item->price_at_purchase, 2) }}
                                                     </td>
                                                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                         {{ number_format($item->quantity * $item->price_at_purchase, 2) }}
                                                     </td>
                                                 </tr>
                                             @endforeach
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                        </div>
                    @else
                         <div class="mb-8 text-gray-600 dark:text-gray-400">{{ __('No items found for this order.') }}</div>
                    @endif


                    {{-- Back to Index Link --}}
                     <div class="mt-6">
                         <a href="{{ route('admin.product-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                             {{ __('Back to Orders List') }}
                         </a>
                     </div>

                </div>
            </div>
        </div>
    </div>
@endsection