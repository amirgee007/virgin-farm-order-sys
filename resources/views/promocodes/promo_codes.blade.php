@extends('layouts.app')

@section('page-title', __('Promo Codes'))
@section('page-heading', __('Promo Codes'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Promo Codes')
    </li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <button class="btn btn-primary btn-sm float-right mb-3" data-toggle="modal" data-target="#promoModal" onclick="openCreateModal()">+ Add Promo Code</button>

                    <table class="table table-bordered mt-3">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Promo Disc. Class</th>
                            <th>Discount</th>
                            <th>Max Usage</th>
                            <th>Valid From</th>
                            <th>Valid Until</th>
                            <th>Min Box Weight</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="promoTableBody">
                        <!-- AJAX Populated -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Promo Code Modal -->
    <div class="modal fade" id="promoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Promo Code</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="promoForm">
                        @csrf
                        <input type="hidden" id="promoId" name="id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Code</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Promo Disc. Class</label>
                                    <input type="text" class="form-control" id="promo_disc_class" name="promo_disc_class">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Discount Percentage</label>
                                    <input type="number" step="0.1" class="form-control" id="discount_percentage" min="0" max="100" name="discount_percentage">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Max Usage</label>
                                    <input type="number" class="form-control" id="max_usage" name="max_usage">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Valid From</label>
                                    <input type="date" class="form-control" id="valid_from" name="valid_from">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Valid Until</label>
                                    <input type="date" class="form-control" id="valid_until" name="valid_until">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Min Box Weight</label>
                                    <input type="number" step="0.1" class="form-control" id="min_box_weight" min="0" name="min_box_weight">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mt-3">
                                    <label>Status</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active" id="active" value="1">
                                        <label class="form-check-label" for="active">Active</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_active" id="inactive" value="0">
                                        <label class="form-check-label" for="inactive">Inactive</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Price Options -->
                        <div class="form-group" style="border: 1px solid red; padding: 10px; border-radius: 5px;">
                            <label>Price Options</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="price_fob" name="price_fob" value="1">
                                <label class="form-check-label" for="price_fob">FOB</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="price_fedex" name="price_fedex" value="1">
                                <label class="form-check-label" for="price_fedex">FedEx</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="price_hawaii" name="price_hawaii" value="1">
                                <label class="form-check-label" for="price_hawaii">HI&AK</label>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success" onclick="savePromoCode()">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('partials.toaster-js')

    <script>
        function fetchPromoCodes() {
            $.ajax({
                url: "{{ route('promo_codes.list') }}",
                method: "GET",
                dataType: "json",
                success: function (data) {
                    $("#promoTableBody").empty();

                    data.forEach(promo => {
                        $("#promoTableBody").append(`
                        <tr>
                            <td>${promo.code}</td>
                            <td>${promo.promo_disc_class || '-'}</td>
                            <td>${promo.discount_percentage + '%'}</td>
                            <td>${promo.max_usage}</td>
                            <td>${promo.valid_from || '-'}</td>
                            <td>${promo.valid_until || '-'}</td>
                            <td>${promo.min_box_weight || '-'}</td>
                            <td>${promo.is_active ? 'Active' : 'Inactive'}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editPromoCode(${promo.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deletePromoCode(${promo.id})">Delete</button>
                            </td>
                        </tr>
                    `);
                    });
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching promo codes:", error);
                }
            });
        }

        function openCreateModal() {
            $("#promoForm")[0].reset();
            $("#promoId").val('');
            $("#price_fob, #price_fedex, #price_hawaii").prop("checked", false);
            $("#promoModal").modal('show');
        }

        function savePromoCode() {
            let id = $("#promoId").val();
            let url = id ? `/promo-codes/update/${id}` : "/promo-codes/store";
            let method = 'POST';

            let formData = $("#promoForm").serializeArray();

            // Add checkbox values manually
            formData.push({ name: "price_fob", value: $("#price_fob").is(":checked") ? 1 : 0 });
            formData.push({ name: "price_fedex", value: $("#price_fedex").is(":checked") ? 1 : 0 });
            formData.push({ name: "price_hawaii", value: $("#price_hawaii").is(":checked") ? 1 : 0 });

            $.ajax({
                url: url,
                method: method,
                data: formData,
                success: function (response) {
                    $("#promoModal").modal('hide');
                    fetchPromoCodes();
                    toastr.success(response.message);
                },
                error: function (error) {
                    toastr.error('Plz fill all inputs with unique code and valid until date should be bigger.');
                }
            });
        }

        function editPromoCode(id) {
            $.get(`/promo-codes/${id}/edit`, function (promo) {
                $("#promoId").val(promo.id);
                $("#code").val(promo.code);
                $("#discount_percentage").val(promo.discount_percentage);
                $("#max_usage").val(promo.max_usage);
                $("#min_box_weight").val(promo.min_box_weight);
                $("#valid_from").val(promo.valid_from);
                $("#valid_until").val(promo.valid_until);
                $("#promo_disc_class").val(promo.promo_disc_class);

                $("#active").prop("checked", promo.is_active == 1);
                $("#inactive").prop("checked", promo.is_active == 0);

                $("#price_fob").prop("checked", promo.price_fob == 1);
                $("#price_fedex").prop("checked", promo.price_fedex == 1);
                $("#price_hawaii").prop("checked", promo.price_hawaii == 1);

                $("#promoModal").modal('show');
            });
        }

        function deletePromoCode(id) {
            if (id <= 1) {
                toastr.error("Error: This promo code cannot be deleted.");
                return;
            }
            if (confirm("Are you sure?")) {
                $.ajax({
                    url: `/promo-codes/delete/${id}`,
                    method: 'DELETE',
                    success: function () {
                        fetchPromoCodes();
                    }
                });
            }
        }

        fetchPromoCodes();
    </script>
@stop
