<?php

namespace PhilWilliammee\SamlServiceProvider\Components;

use Illuminate\View\Component;
use PhilWilliammee\SamlServiceProvider\SamlServiceProvider;

class Login extends Component
{
    public $saml_request;
    public $destination;
    public $relay_state;

    public function __construct(string $sessionId, string $relayState)
    {
        $saml_sp = new SamlServiceProvider();
        $saml_sp->createLogin($sessionId);
        $this->saml_request = $saml_sp->getAuthenticationRequest();
        $this->destination = config('samlserviceprovider.destination');
        $this->relay_state = $relayState;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('samlserviceprovider::components.saml-login');
    }
}
