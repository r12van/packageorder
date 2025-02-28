@extends('layouts.app')

@section('title', 'Order Products')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div id="productListSection">
                <h2>Product List</h2>
                <ul class="list-group" style="max-height: 500px; overflow-y: auto;">
                    @foreach ($products as $product)
                        <li class="list-group-item">
                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                            {{ $product->name }} - ${{ $product->price }} - {{ $product->weight }}g
                        </li>
                    @endforeach
                </ul>
                <button id="placeOrderBtn" class="btn btn-primary mt-3">Place Order</button>
            </div>
        </div>

        <div class="col-md-6">
            <div id="orderResult" class="mt-4" style="display: none;">
                <h3>This order has following packages:</h3>
                <div id="selectedItemsTable"></div>
                <div id="packageTable"></div>
                <button id="reOrderBtn" class="btn btn-secondary mt-3" style="display: none;">Re-order</button>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#placeOrderBtn').click(function() {
                let selectedItems = $('.product-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                $.ajax({
                    url: '{{route('orders.process')}}',
                    type: 'POST',
                    data: {
                        selected_items: selectedItems,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#orderResult').show();

                        // Selected Items Table
                        let selectedTableHtml = '<table class="table table-bordered"><thead><tr><th>Items</th><th>Weight (g)</th><th>Price ($)</th></tr></thead><tbody>';
                        let totalPrice = 0;
                        response.packages.forEach(function(package) {
                            package.items.forEach(function(item) {
                                selectedTableHtml += '<tr><td>' + item.name + '</td><td>' + item.weight + '</td><td>' + item.price + '</td></tr>';
                                totalPrice += parseFloat(item.price);
                            });
                        });
                        selectedTableHtml += '<tr><td colspan="2"><strong>Total</strong></td><td><strong>' + totalPrice + '</strong></td></tr>';
                        selectedTableHtml += '</tbody></table>';
                        $('#selectedItemsTable').html(selectedTableHtml);

                        // Package Items Table
                        let packageTableHtml = '<table class="table table-bordered"><thead><tr><th>Package</th><th>Items</th><th>Weight (g)</th><th>Price ($)</th><th>Courier ($)</th></tr></thead><tbody>';
                        response.packages.forEach(function(package, index) {
                            packageTableHtml += '<tr><td>' + (index + 1) + '</td><td>' + package.items.map(item => item.name).join(', ') + '</td><td>' + package.total_weight + '</td><td>' + package.total_price + '</td><td>' + package.courier_price + '</td></tr>';
                        });
                        packageTableHtml += '</tbody></table>';
                        $('#packageTable').html(packageTableHtml);
                        $('#reOrderBtn').show();

                        // Clear checkboxes
                        $('.product-checkbox').prop('checked', false);
                    },
                    error: function(error) {
                        console.error('Error processing order:', error);
                    }
                });
            });
            // Re-order Button
            $('#reOrderBtn').click(function() {
                $('#orderResult').hide();
                $('#packageTable').html('');
                $('#reOrderBtn').hide();

            });
        });
    </script>
@endpush
