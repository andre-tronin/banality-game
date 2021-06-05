<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private RequestStack $requestStack;
    private TranslatorInterface $translator;

    public function __construct(UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, TranslatorInterface $translator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        // add a custom flash message and redirect to the home page
        if ($this->requestStack->getSession() instanceof Session) {
            $this->requestStack->getSession()->getFlashBag()->add('note', $this->translator->trans('error.game_access_denied'));
        }

        return new RedirectResponse($this->urlGenerator->generate('home'));
    }
}
