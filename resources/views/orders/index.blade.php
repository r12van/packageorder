@extends('layouts.app')

@section('title', 'Order Products')

@section('content')
    <div id="productListSection">
        <h2>Product List</h2>
        <ul class="list-group">
            @foreach ($products as $product)
                <li class="list-group-item">
                    <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                    {{ $product->name }} - ${{ $product->price }} - {{ $product->weight }}g
                </li>
            @endforeach
        </ul>
        <button id="placeOrderBtn" class="btn btn-primary mt-3">Place Order</button>
    </div>

    <div id="orderResult" class="mt-4" style="display: none;">
        <h3>This order has following packages:</h3>
        <div id="packageList"></div>
        <button id="reOrderBtn" class="btn btn-secondary mt-3" style="display: none;">Re-order</button>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let lastSelectedItems = [];

            $('#placeOrderBtn').click(function() {
                let selectedItems = $('.product-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                lastSelectedItems = selectedItems; // Store for re-order

                $.ajax({
                    url: '/process-order',
                    type: 'POST',
                    data: {
                        selected_items: selectedItems,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#productListSection').hide();
                        $('#orderResult').show();

                        let packageHtml = '';
                        response.packages.forEach(function(package, index) {
                            packageHtml += '<div class="card mt-2"><div class="card-body">';
                            packageHtml += '<h4>Package ' + (index + 1) + '</h4>';
                            packageHtml += '<p><strong>Items:</strong> ' + package.items.map(item => item.name).join(', ') + '</p>';
                            packageHtml += '<p><strong>Total weight:</strong> ' + package.total_weight + 'g</p>';
                            packageHtml += '<p><strong>Total price:</strong> $' + package.total_price + '</p>';
                            packageHtml += '<p><strong>Courier price:</strong> $' + package.courier_price + '</p>';
                            packageHtml += '</div></div>';
                        });

                        $('#packageList').html(packageHtml);
                        $('#reOrderBtn').show();

                        // Clear checkboxes
                        $('.product-checkbox').prop('checked', false);
                    },
                    error: function(error) {
                        console.error('Error processing order:', error);
                    }
                });
            });

            $('#reOrderBtn').click(function() {
                //Re-order logic
                $('#productListSection').show();
                $('#orderResult').hide();
                $('#packageList').html('');
                $('#reOrderBtn').hide();

                //Select previous items
                $('.product-checkbox').prop('checked', false);
                lastSelectedItems.forEach(function(itemId){
                    $('.product-checkbox[value="'+itemId+'"]').prop('checked', true);
                });

            });
        });
    </script>
@endpush
