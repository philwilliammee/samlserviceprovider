<div>
    <noscript>
        <p><strong>Note:</strong>Since your browser does not support JavaScript, you must press the button below once to proceed.</p>
    </noscript>
    <form method="post" action="{{$destination}}">
        <input type="hidden" name="SAMLRequest" value="{{$saml_request}}" />
        <input type="hidden" name="RelayState" value="{{$relay_state}}" />
        <noscript>
            <button type="submit" class="btn submit">SAML Login</button>
        </noscript>

    </form>
    <script>
        document.forms[0].submit();

    </script>
</div>
