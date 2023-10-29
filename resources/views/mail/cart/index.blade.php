@component('mail::message')

    # @lang('Hello!')

    @lang('New user was just registered on :app website.', ['app' => setting('app_name')])


    @lang('To view the user details just visit the link below.')


    @lang('Regards'),<br>
    {{ setting('app_name') }}

@endcomponent
