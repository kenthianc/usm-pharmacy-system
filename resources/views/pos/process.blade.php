<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Process Prescription: ') }} {{ $prescription->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6">
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Patient Details</h3>
                    <p class="text-sm text-gray-600">Name: {{ $prescription->patient->name }} </p>
                    <p class="text-sm text-gray-600">Notes: {{ $prescription->notes ?? 'None' }}</p>
                </div>

                <form method="POST" action="{{ route('pos.dispense', $prescription) }}">
                    @csrf
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Items & FEFO Batch Suggestions</h3>
                    <div class="space-y-6">
                        @foreach($prescription->items as $item)
                            <div class="border rounded-md p-4 bg-gray-50">
                                <div class="flex justify-between mb-2">
                                    <span class="font-semibold">{{ $item->medicine->generic_name }} ({{ $item->medicine->name }})</span>
                                    <span>Prescribed Qty: <strong class="text-indigo-600">{{ $item->quantity }}</strong></span>
                                </div>
                                
                                @if(empty($suggestions[$item->medicine_id]))
                                    <div class="text-red-600 text-sm">Out of stock or expired! Cannot fulfill this item.</div>
                                @else
                                    <table class="min-w-full divide-y divide-gray-200 mt-2">
                                        <thead>
                                            <tr>
                                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Batch Number</th>
                                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Available</th>
                                                <th class="text-right text-xs font-medium text-gray-500 uppercase">Dispense Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($suggestions[$item->medicine_id] as $suggestion)
                                                <tr>
                                                    <td class="py-2 text-sm text-gray-900">{{ $suggestion['batch_no'] }}</td>
                                                    <td class="py-2 text-sm text-gray-900 @if(now()->diffInDays($suggestion['expiry_date'], false) < 90) text-orange-600 font-bold @endif">
                                                        {{ \Carbon\Carbon::parse($suggestion['expiry_date'])->format('Y-m-d') }}
                                                    </td>
                                                    <td class="py-2 text-sm text-gray-900">{{ $suggestion['available'] }}</td>
                                                    <td class="py-2 text-right">
                                                        <input type="number" 
                                                            name="allocations[{{ $item->medicine_id }}][{{ $suggestion['batch_id'] }}]" 
                                                            value="{{ $suggestion['quantity'] }}" 
                                                            min="0" 
                                                            max="{{ $suggestion['available'] }}"
                                                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-24 text-right">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 border-t pt-6">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="insurance">Insurance</option>
                        </select>
                        <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>
                            {{ __('Confirm & Dispense') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
