<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:59
 */

namespace NOUT\Bundle\SessionManagerBundle\Security\Authentication\Handler;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class LoginSuccessHandler extends DefaultAuthenticationSuccessHandler {

    protected $router;

    public function __construct( HttpUtils $httpUtils, array $options, $router ) {
        $this->router = $router;
        parent::__construct( $httpUtils, $options );
    }

    public function onAuthenticationSuccess( Request $request, TokenInterface $token ) {
        $key = '_security.secured_area.target_path';

        if($request->getSession()->has($key)) {
            $url = $request->getSession()->get($key);
            $request->getSession()->remove($key);
            return new RedirectResponse($url);
        }
        else {
            return parent::onAuthenticationSuccess($request, $token);
        }
    }
}