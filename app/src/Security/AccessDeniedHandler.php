<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private SessionInterface $session;
    private TranslatorInterface $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->translator = $translator;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // add a custom flash message and redirect to the home page
        if ($this->session instanceof Session) {
            $this->session->getFlashBag()->add('note', $this->translator->trans('error.game_access_denied'));
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }
}
