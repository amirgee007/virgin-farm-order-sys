@extends('layouts.app')

@section('page-title', __('Colors Class'))
@section('page-heading', __('Colors Class'))

@section('breadcrumbs')
    <li class="breadcrumb-item active">
        @lang('Colors Class')
    </li>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body mt-0 p-3">
                    <button class="btn btn-primary btn-sm float-right mb-3" data-toggle="modal" data-target="#colorModal" onclick="openCreateModal()">+ Add Color Class</button>

                    <table class="table table-bordered mt-3">
                        <thead>
                        <tr>
                            <th>Sr.</th>
                            <th>Class ID</th>
                            <th>Subclass</th>
                            <th>Description</th>
                            <th>Color</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="colorTableBody">
                        <!-- AJAX Populated -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="colorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Color Class</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="colorForm">
                        @csrf
                        <input type="hidden" id="colorId" name="id">

                        <div class="form-group">
                            <label>Class ID</label>
                            <input type="number" class="form-control" name="class_id" id="class_id" required>
                        </div>

                        <div class="form-group">
                            <label>Subclass</label>
                            <input type="text" class="form-control" name="sub_class" id="sub_class" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control" name="description" id="description" required>
                        </div>

                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" class="form-control form-control-color" name="color" id="color" required>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="saveColorClass()">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('partials.toaster-js')

    <script>
        function fetchColorClass() {
            $.ajax({
                url: "{{ route('colors_class.list') }}",
                method: "GET",
                success: function (data) {
                    $('#colorTableBody').empty();
                    data.forEach(item => {
                        $('#colorTableBody').append(`
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.class_id}</td>
                            <td>${item.sub_class}</td>
                            <td>${item.description}</td>
                            <td style="background-color: ${item.color};">${item.color}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="editColorClass(${item.id})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteColorClass(${item.id})">Delete</button>
                            </td>
                        </tr>
                    `);
                    });
                }
            });
        }

        function openCreateModal() {
            $('#colorForm')[0].reset();
            $('#colorId').val('');
            $('#colorModal').modal('show');
        }

        function saveColorClass() {
            let id = $('#colorId').val();
            let url = id ? `/colors-class/update/${id}` : `/colors-class/store`;

            $.ajax({
                url: url,
                method: 'POST',
                data: $('#colorForm').serialize(),
                success: function (res) {
                    $('#colorModal').modal('hide');
                    fetchColorClass();
                    toastr.success(res.message);
                },
                error: function (xhr) {
                    if (xhr.responseJSON?.errors) {
                        const messages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        toastr.error(messages);
                    } else {
                        toastr.error('An unexpected error occurred.');
                    }
                }
            });
        }

        function editColorClass(id) {
            $.get(`/colors-class/${id}/edit`, function (data) {
                $('#colorId').val(data.id);
                $('#class_id').val(data.class_id);
                $('#sub_class').val(data.sub_class);
                $('#description').val(data.description);
                $('#color').val(data.color);
                $('#color').css('background-color', data.color);  // ✅ Apply color to background
                $('#colorModal').modal('show');
            });
        }

        function deleteColorClass(id) {
            if (confirm('Are you sure you want to delete this entry?')) {
                $.ajax({
                    url: `/colors-class/delete/${id}`,
                    method: 'DELETE',
                    success: function (res) {
                        fetchColorClass();
                        toastr.success(res.message);
                    }
                });
            }
        }

        $(document).ready(fetchColorClass);

        $('#color').on('input', function () {
            $(this).css('background-color', $(this).val());
        });
    </script>
@stop
