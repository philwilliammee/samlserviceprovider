<a {{ $attributes }} href="{{route('saml-logout', ['session_id' => session()->getId()])}}">
    {!! $slot !!}
</a>
