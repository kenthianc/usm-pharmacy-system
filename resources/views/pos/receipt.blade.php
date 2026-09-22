<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight print:hidden">
            {{ __('Transaction Receipt') }}
        </h2>
    </x-slot>

    <div class="py-12 print:py-0">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-8 print:shadow-none print:p-0">
                <!-- Receipt Header -->
                <div class="text-center mb-8 border-b pb-4">
                    <h1 class="text-2xl font-bold uppercase tracking-wider">USM Pharmacy</h1>
                    <p class="text-sm text-gray-600">University of Southern Mindanao</p>
                    <p class="text-sm text-gray-600">Kabacan, Cotabato</p>
                    <p class="mt-2 text-sm">Receipt #: <strong>{{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}</strong></p>
                    <p class="text-sm">Date: {{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                    <p class="text-sm">Cashier: {{ $transaction->cashier->name }}</p>
                </div>

                @if(!$transaction->isOtc())
                    <div class="mb-6 text-sm grid grid-cols-2 gap-2 bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div>
                            <p><strong>Patient:</strong> {{ $transaction->prescription?->patient?->name }}</p>
                            <p><strong>Patient Type:</strong> {{ $transaction->prescription?->patient?->patient_type ?? 'Regular' }}</p>
                            <p><strong>Rx #:</strong> {{ $transaction->prescription?->prescription_number ?? 'RX-'.str_pad($transaction->prescription_id, 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="text-right">
                            <p><strong>Care Track:</strong> {{ $transaction->order_type === 'inpatient' ? 'In-Patient Ward' : 'Out-Patient Walk-in' }}</p>
                            @if($transaction->room_bed_number)
                                <p><strong>Bed / Room:</strong> {{ $transaction->room_bed_number }}</p>
                            @endif
                            @if($transaction->discount_id_number)
                                <p><strong>Discount ID:</strong> {{ $transaction->discount_id_number }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="mb-6 text-sm flex justify-between bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div>
                            <p><strong>Type:</strong> Over-The-Counter (OTC) Sale</p>
                            <p><strong>Customer:</strong> Walk-in Client</p>
                        </div>
                        @if($transaction->discount_id_number)
                            <div class="text-right">
                                <p><strong>Discount ID:</strong> {{ $transaction->discount_id_number }}</p>
                                <p><strong>Tier:</strong> {{ ucfirst($transaction->discount_type ?? 'Regular') }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Items Table -->
                <table class="w-full text-sm mb-8">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="text-left py-2">Item</th>
                            <th class="text-center py-2">Batch</th>
                            <th class="text-right py-2">Qty</th>
                            <th class="text-right py-2">Price</th>
                            <th class="text-right py-2">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-2">
                                    {{ $item->medicine->generic_name }}<br>
                                    <span class="text-xs text-gray-500">{{ $item->medicine->name }}</span>
                                </td>
                                <td class="py-2 text-center text-xs text-gray-500">{{ $item->batch?->batch_no ?? 'N/A' }}</td>
                                <td class="py-2 text-right">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-2 text-right font-medium">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-300">
                            <td colspan="4" class="text-right py-2 font-bold text-gray-700">Subtotal (Gross):</td>
                            <td class="text-right py-2 font-bold text-gray-900">₱{{ number_format($transaction->subtotal ?? $transaction->total_amount, 2) }}</td>
                        </tr>
                        @if(($transaction->vat_exempt_amount ?? 0) > 0)
                            <tr>
                                <td colspan="4" class="text-right py-1 text-sm text-emerald-700">12% VAT Exemption (RA 9994/10754):</td>
                                <td class="text-right py-1 text-sm font-bold text-emerald-700">-₱{{ number_format($transaction->vat_exempt_amount, 2) }}</td>
                            </tr>
                        @endif
                        @if(($transaction->discount_amount ?? 0) > 0)
                            <tr>
                                <td colspan="4" class="text-right py-1 text-sm text-emerald-700">
                                    {{ $transaction->discount_type === 'senior' ? 'Senior Citizen 20% Discount' : ($transaction->discount_type === 'pwd' ? 'PWD 20% Discount' : ($transaction->discount_type === 'student' ? 'Student 10% Subsidy' : 'Statutory Discount')) }}:
                                </td>
                                <td class="text-right py-1 text-sm font-bold text-emerald-700">-₱{{ number_format($transaction->discount_amount, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="border-t border-gray-300">
                            <td colspan="4" class="text-right py-3 font-bold text-base">NET TOTAL DUE:</td>
                            <td class="text-right py-3 font-bold text-lg text-emerald-900">₱{{ number_format($transaction->net_amount ?? $transaction->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right py-1 text-sm text-gray-600">Payment Tender:</td>
                            <td class="text-right py-1 text-sm font-medium capitalize">
                                @if($transaction->payment_method === 'hospital_bill')
                                    <span class="text-blue-700 font-bold">Charge to Hospital Bill</span>
                                @else
                                    {{ str_replace('_', ' ', $transaction->payment_method) }}
                                @endif
                            </td>
                        </tr>
                        @if($transaction->billing_status === 'billed_to_account')
                            <tr>
                                <td colspan="4" class="text-right py-1 text-sm text-blue-700 font-bold">Hospital Ledger Status:</td>
                                <td class="text-right py-1 text-sm font-bold text-blue-700 uppercase">Billed to Account (Pending Discharge)</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>

                <div class="text-center mt-8 text-sm text-gray-500 print:text-black">
                    <p>Thank you for choosing USM Pharmacy.</p>
                    <p>Please come again.</p>
                </div>

                <div class="mt-8 flex justify-center print:hidden">
                    <button onclick="window.print()" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-6 rounded shadow">
                        Print Receipt
                    </button>
                    <a href="{{ route('pos.index') }}" class="ml-4 text-indigo-600 hover:text-indigo-900 py-2">Back to POS</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
