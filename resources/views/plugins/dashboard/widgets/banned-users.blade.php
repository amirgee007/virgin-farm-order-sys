<div class="card widget">
    <div class="card-body">
        <a href="{{route('users.index').'?status=Banned'}}" target="_blank"
           title="@lang('Click to see banned users')" data-toggle="tooltip" data-placement="bottom" >
            <div class="row">
                <div class="p-3 text-danger flex-1">
                    <i class="fa fa-user-slash fa-3x"></i>
                </div>

                <div class="pr-3">
                    <h2 class="text-right">{{ number_format($count) }}</h2>
                    <div class="text-muted">@lang('Banned')</div>
                </div>
            </div>
        </a>
    </div>
</div>
