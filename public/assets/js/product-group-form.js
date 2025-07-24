$(document).ready(function () {
    let productIndex = $('.product-row').length;
    let addedItemNos = new Set();

    // Collect item_nos from existing rows (edit mode)
    $('.product-row input[name*="[item_no]"]').each(function () {
        const itemNo = $(this).val().trim().toUpperCase();
        if (itemNo) {
            addedItemNos.add(itemNo);
        }
    });

    // Search input & suggestions
    const $searchInput = $('#search_product');
    const $productName = $('#product_name');
    const $productId = $('#product_id'); // Optional hidden field for actual product ID
    const $suggestionsBox = $('#product-suggestions');

    $searchInput.on('input', function () {
        const query = $(this).val().trim();
        $('#search-status').text('Searching...');

        if (query.length < 3) {
            $suggestionsBox.empty();
            $('#search-status').text('Waiting for input...');
            return;
        }

        $.get('/products/search', { q: query }, function (products) {
            $suggestionsBox.empty();

            if (!products.length) {
                $suggestionsBox.append('<div class="list-group-item disabled">No results found</div>');
                $('#search-status').text('Not found');
                return;
            }

            $('#search-status').text('Select a product');

            $.each(products, function (i, product) {
                const item = $('<a href="#" class="list-group-item list-group-item-action"></a>')
                    .text(product.item_no + ' - ' + product.product_text)
                    .data('item_no', product.item_no)
                    .data('name', product.product_text);

                item.on('click', function (e) {
                    e.preventDefault();

                    const selectedItemNo = $(this).data('item_no');
                    const selectedName = $(this).data('name');

                    $searchInput.val(selectedItemNo);
                    $productName.val(selectedName);
                    $('#search-status').text('Product selected');
                    $('#stems').focus();
                    $suggestionsBox.empty();
                });

                $suggestionsBox.append(item);
            });
        }).fail(function () {
            $('#search-status').text('Error searching');
            $suggestionsBox.empty();
        });
    });

    // Click outside to close suggestions
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#search_product, #product-suggestions').length) {
            $suggestionsBox.empty();
        }
    });

    // Add product to table
    $('#add-product').click(function () {
        const itemNo = $searchInput.val().trim().toUpperCase();
        const name = $productName.val().trim();
        const stems = $('#stems').val().trim();

        if (!itemNo || !name || !stems) {
            toastr.error('Please select a product and enter stem count.');
            return;
        }

        if (addedItemNos.has(itemNo)) {
            toastr.error('Product already added.');
            return;
        }

        const row = `
            <tr class="product-row">
                <td>
                    <input type="hidden" name="products[${productIndex}][item_no]" value="${itemNo}">
                    ${itemNo}
                </td>
                <td>
                    <input type="text" name="products[${productIndex}][product_text_temp]" value="${name}" class="form-control" required>
                </td>
                <td>
                    <input type="number" name="products[${productIndex}][stems]" value="${stems}" class="form-control" required>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-row">✖</button>
                </td>
            </tr>
        `;

        $('#product-list tbody').append(row);
        addedItemNos.add(itemNo);
        productIndex++;

        // Reset fields
        $searchInput.val('').focus();
        $productName.val('');
        $('#stems').val('');
        $('#search-status').text('Waiting for input...');
        $suggestionsBox.empty();
    });

    // Remove row
    $(document).on('click', '.remove-row', function () {
        const itemNo = $(this).closest('tr').find('input[type="hidden"]').val().trim().toUpperCase();
        addedItemNos.delete(itemNo);
        $(this).closest('tr').remove();
    });

    // Prevent Enter key from submitting form unintentionally
    $('#group-form').on('keydown', function (e) {
        if (e.key === 'Enter' && !$(e.target).is('textarea') && !$(e.target).is('button')) {
            e.preventDefault();
        }
    });
});
