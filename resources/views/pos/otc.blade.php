<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New OTC Sale') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg p-6" x-data="otcRegister()">
                <form method="POST" action="{{ route('pos.otc.store') }}">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700">Add Medicine</label>
                        <div class="mt-1 flex gap-2">
                            <select x-model="selectedMedicine" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Select a medicine...</option>
                                @foreach($medicines as $medicine)
                                    <option value="{{ $medicine->id }}" data-price="{{ $medicine->unit_price }}" data-name="{{ $medicine->generic_name }} ({{ $medicine->name }})">
                                        {{ $medicine->generic_name }} - ${{ number_format($medicine->unit_price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" @click="addItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">Add</button>
                        </div>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200 mb-6">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Medicine</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900" x-text="item.name"></td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right" x-text="'$' + item.price.toFixed(2)"></td>
                                    <td class="px-4 py-2 text-center">
                                        <input type="hidden" :name="'items[' + index + '][medicine_id]'" :value="item.id">
                                        <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.qty" min="1" class="w-20 border-gray-300 rounded-md shadow-sm text-center">
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right font-medium" x-text="'$' + (item.price * item.qty).toFixed(2)"></td>
                                    <td class="px-4 py-2 text-center">
                                        <button type="button" @click="removeItem(index)" class="text-red-600 hover:text-red-900">&times;</button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0">
                                <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">No items added.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-bold">Total:</td>
                                <td class="px-4 py-2 text-right font-bold text-lg" x-text="'$' + totalAmount.toFixed(2)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="mt-4 border-t pt-4">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
                        <select id="payment_method" name="payment_method" class="mt-1 block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                        </select>
                        <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                        <x-input-error :messages="$errors->get('items')" class="mt-2" />
                        @if (session('error'))
                            <div class="mt-2 text-sm text-red-600">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button x-bind:disabled="items.length === 0">
                            {{ __('Complete OTC Sale') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('otcRegister', () => ({
                selectedMedicine: '',
                items: [],
                
                addItem() {
                    if (!this.selectedMedicine) return;
                    
                    const select = document.querySelector('select[x-model="selectedMedicine"]');
                    const option = select.options[select.selectedIndex];
                    
                    const id = this.selectedMedicine;
                    const name = option.dataset.name;
                    const price = parseFloat(option.dataset.price);

                    const existingIndex = this.items.findIndex(i => i.id == id);
                    if (existingIndex > -1) {
                        this.items[existingIndex].qty += 1;
                    } else {
                        this.items.push({
                            id: id,
                            name: name,
                            price: price,
                            qty: 1
                        });
                    }
                    this.selectedMedicine = '';
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },

                get totalAmount() {
                    return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>
