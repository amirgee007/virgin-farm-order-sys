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

    $('#item_no').on('input', function () {
        const itemNo = $(this).val().trim();

        if (!itemNo) {
            $('#product_name').val('');
            $('#search-status').text('Waiting for input...');
            return;
        }

        $.get('/api/products/by-item-no/' + itemNo, function (data) {
            if (data && data.product_text) {
                $('#product_name').val(data.product_text);
                $('#search-status').text('Product found');
                $('#stems').focus();
            } else {
                $('#product_name').val('');
                $('#search-status').text('Not found');
            }
        }).fail(() => {
            $('#product_name').val('');
            $('#search-status').text('Error or not found');
        });
    });

    $('#add-product').click(function () {
        const itemNo = $('#item_no').val().trim().toUpperCase();
        const name = $('#product_name').val().trim();
        const stems = $('#stems').val().trim();

        if (!itemNo || !name || !stems) {
            toastr.error('Please enter a valid item number and stem count.');
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

        $('#item_no').val('').focus();
        $('#product_name').val('');
        $('#stems').val('');
        $('#search-status').text('Waiting for input...');
    });

    $(document).on('click', '.remove-row', function () {
        const itemNo = $(this).closest('tr').find('input[type="hidden"]').val().trim().toUpperCase();
        addedItemNos.delete(itemNo);
        $(this).closest('tr').remove();
    });

    $('#group-form').on('keydown', function (e) {
        if (e.key === 'Enter' && !$(e.target).is('textarea') && !$(e.target).is('button')) {
            e.preventDefault();
        }
    });
});
