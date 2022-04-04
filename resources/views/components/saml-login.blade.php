<form method="post" action="{{$destination}}">
    <input type="hidden" name="SAMLRequest" value="{{$saml_request}}" />
    <input type="hidden" name="RelayState" value="{{$relay_state}}" />
    <button type="submit" class="btn submit">SAML Login</button>
</form>
<script>
    document.querySelector('button').click();
</script>
