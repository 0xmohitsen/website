<?php

namespace App\Controller\Pages\SignIn;

use OTPHP\InternalClock;
use OTPHP\TOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateController extends AbstractController
{
    public function index(Request $request): Response
    {
        $otp = TOTP::createFromSecret($_ENV['TOTP_SECRET'], new InternalClock());
        $otp->setDigits(9);

        if ($otp->verify($request->request->get('password'), null, 5)) {
            $cookie = Cookie::create('username', $request->request->get('username'));
            $response = new RedirectResponse($this->generateUrl('pages_play_online'));
            $response->headers->setCookie($cookie);
            return $response;
        }

        $response = new RedirectResponse($this->generateUrl('pages_sign_in'));
        $response->headers->clearCookie('username');
        return $response;
    }
}
