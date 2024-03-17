<form action="{{route('copy.image.product')}}" method="POST" enctype="multipart/form-data">
    {{csrf_field()}}

    <div class="form-group">
        <label for="formFile" class="form-label">Select Source Image Product</label>
        <select required class="form-control select2" name="source" id="formFile" >
            <option selected value="">Select Source Product</option>
            @foreach($haveImages AS $id => $val)
                <option  value="{{$id}}">{{$val}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group ">
        <label for="sourceFile" class="form-label">Select Target Products</label>
        <select required class="form-control select2" name="targets[]" id="sourceFile" multiple>
            @foreach($noImages AS $id => $val)
                <option value="{{$id}}">{{$val}}</option>
            @endforeach
        </select>
    </div>

    <input type="submit" value="Copy Images" class="btn btn-primary btn-sm float-right">
</form>

<script> $('.select2').select2();</script>
