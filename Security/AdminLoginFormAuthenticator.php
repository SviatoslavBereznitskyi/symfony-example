<?php

declare(strict_types=1);

namespace App\Security;

use App\Auth\Service\PasswordHasher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class AdminLoginFormAuthenticator
 */
class AdminLoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var CsrfTokenManagerInterface
     */
    private CsrfTokenManagerInterface $csrfTokenManager;

    /**
     * @var PasswordHasher
     */
    private PasswordHasher $hasher;


    /**
     * AdminLoginFormAuthenticator constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param PasswordHasher $hasher
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        PasswordHasher $hasher
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->hasher = $hasher;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'admin.login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     *
     * @return array
     * @psalm-return array<string, string>
     */
    public function getCredentials(Request $request): array
    {
        $credentials = [
            'email' => (string)$request->request->get('email'),
            'password' => (string)$request->request->get('password'),
            'csrf_token' => (string)$request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        /** @var array $credentials */
        $token = new CsrfToken('authenticate', (string)$credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException('');
        }

        /** @var UserInterface|null $user */
        $user = $userProvider->loadUserByUsername((string)$credentials['email']);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Username could not be found.');
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        /** @var array $credentials */
        return $this->hasher->validate((string)$credentials['password'], (string)$user->getPassword());
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('admin.home'));
    }

    /**
     * @return string
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate('admin.login');
    }
}
