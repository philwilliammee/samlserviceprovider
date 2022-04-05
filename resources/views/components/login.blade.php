<a href="{{route('saml-login', ['session_id' => session()->getId(), 'redirect_url' => $redirect ]) }}">
    {!! $slot !!}
</a>
