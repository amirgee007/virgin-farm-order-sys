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
                            <th>Discount</th>
                            <th>Max Usage</th>
                            <th>Valid From</th>
                            <th>Valid Until</th>
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
                                    <label>Max Usage</label>
                                    <input type="number" class="form-control" id="max_usage" name="max_usage">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Discount Amount</label>
                                    <input type="number" class="form-control" id="discount_amount" name="discount_amount">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Discount Percentage</label>
                                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage">
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
                            <div class="col-md-12">
                                <div class="form-group">
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
                dataType: "json", // Ensure the response is treated as JSON
                success: function (data) {
                    console.log("Received data:", data);

                    if (!data || !Array.isArray(data)) {
                        console.error("Invalid data format:", data);
                        return;
                    }

                    $("#promoTableBody").empty(); // Clear existing table data

                    data.forEach(promo => {
                        $("#promoTableBody").append(`
                    <tr>
                        <td>${promo.code}</td>
                        <td>${promo.discount_amount || promo.discount_percentage + '%'}</td>
                        <td>${promo.max_usage}</td>
                        <td>${promo.valid_from || '-'}</td>
                        <td>${promo.valid_until || '-'}</td>
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
            $("#promoModal").modal('show');
        }

        function savePromoCode() {
            let id = $("#promoId").val();
            let url = id ? `/promo-codes/update/${id}` : "/promo-codes/store";
            let method = id ? 'POST' : 'POST';

            $.ajax({
                url: url,
                method: method,
                data: $("#promoForm").serialize(),
                success: function (response) {
                    $("#promoModal").modal('hide');
                    fetchPromoCodes();
                    alert(response.message);
                },
                error: function (error) {
                    alert("Something went wrong!");
                }
            });
        }

        function editPromoCode(id) {
            $.get(`/promo-codes/${id}/edit`, function (promo) {
                $("#promoId").val(promo.id);
                $("#code").val(promo.code);
                $("#discount_amount").val(promo.discount_amount);
                $("#discount_percentage").val(promo.discount_percentage);
                $("#max_usage").val(promo.max_usage);
                $("#valid_from").val(promo.valid_from);
                $("#valid_until").val(promo.valid_until);
                // Set Active/Inactive radio button
                if (promo.is_active == 1) {
                    $("#active").prop("checked", true);
                } else {
                    $("#inactive").prop("checked", true);
                }
                $("#promoModal").modal('show');
            });
        }

        function deletePromoCode(id) {
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
