$(document).ready(function () {
    let productIndex = $('.product-row').length;
    let addedItemNos = new Set();

    // Collect preloaded item numbers (edit mode)
    $('.product-row input[name*="[item_no]"]').each(function () {
        const existingItemNo = $(this).val().trim().toUpperCase();
        addedItemNos.add(existingItemNo);
    });

    // Search product by item_no
    $('#item_no').on('input', function () {
        const itemNo = $(this).val().trim().toUpperCase();

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

    // Add product row
    $('#add-product').click(function () {
        const itemNo = $('#item_no').val().trim().toUpperCase();
        const name = $('#product_name').val().trim();
        const stems = $('#stems').val().trim();

        if (!itemNo || !name || !stems) {
            alert('Please enter a valid item number and stem count.');
            return;
        }

        if (addedItemNos.has(itemNo)) {
            alert('Product already added.');
            return;
        }

        const row = `
            <tr class="product-row">
                <td>
                    <input type="hidden" name="products[${productIndex}][item_no]" value="${itemNo}">
                    ${itemNo}
                </td>
                <td>${name}</td>
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

        // Reset form
        $('#item_no').val('').focus();
        $('#product_name').val('');
        $('#stems').val('');
        $('#search-status').text('Waiting for input...');
    });

    // Remove row
    $(document).on('click', '.remove-row', function () {
        const itemNo = $(this).closest('tr').find('input[type="hidden"]').val().trim().toUpperCase();
        addedItemNos.delete(itemNo);
        $(this).closest('tr').remove();
    });

    // Prevent Enter key from submitting the form accidentally
    $('#group-form').on('keydown', function (e) {
        if (e.key === 'Enter' && !$(e.target).is('textarea') && !$(e.target).is('button')) {
            e.preventDefault();
            return false;
        }
    });
});
