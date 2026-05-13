<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseHandler;
use App\Services\ApiService;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    use ApiResponseHandler;

    protected array $apiJsonHeaders = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];

    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * POST /api/v1/auth/login
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $response = $this->apiService->makeApiRequest('POST', 'auth/login', [
            'json' => $validated,
            'headers' => $this->apiJsonHeaders,
        ], false);

        if (! $response->successful()) {
            $message = $response->json('message') ?? __('Invalid credentials.');
            return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => $message]);
        }

        $data = $response->json('data');
        $token = is_array($data) ? ($data['access_token'] ?? null) : null;
        $user = is_array($data) ? ($data['user'] ?? null) : null;
        if (! $token) {
            $token = $response->json('token');
            $user = $response->json('user');
        }
        if ($token) {
            $this->apiService->setUserTokens($token, '', 3600);
            if (is_array($user)) {
                Session::put(config('votix_api.session_user_key'), $user);
            }
        }

        $locale = $request->route('locale', 'fr');
        $successMessage = $response->json('message') ?: __('Welcome back.');
        return redirect()->route('home', ['locale' => $locale])->with('success', $successMessage);
    }

    /**
     * POST /api/v1/auth/register
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $response = $this->apiService->makeApiRequest('POST', 'auth/register', [
            'json' => [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ],
            'headers' => $this->apiJsonHeaders,
        ], false);

        if (! $response->successful()) {
            $message = $response->json('message');
            $errors = $response->json('errors');
            if (is_array($errors)) {
                throw ValidationException::withMessages($errors);
            }
            return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => $message ?? __('Registration failed.')]);
        }

        $data = $response->json('data');
        $token = is_array($data) ? ($data['access_token'] ?? null) : null;
        $user = is_array($data) ? ($data['user'] ?? null) : null;
        if (! $token) {
            $token = $response->json('token');
            $user = $response->json('user');
        }
        if ($token) {
            $this->apiService->setUserTokens($token, '', 3600);
            if (is_array($user)) {
                Session::put(config('votix_api.session_user_key'), $user);
            }
        }

        $locale = $request->route('locale', 'fr');
        $successMessage = $response->json('message') ?: __('A verification email has been sent. Please check your inbox.');
        return redirect()->route('home', ['locale' => $locale])->with('success', $successMessage);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): RedirectResponse
    {
        $token = $this->apiService->getUserToken();
        $message = __('Logged out.');
        if ($token) {
            $response = $this->apiService->makeApiRequest('POST', 'auth/logout', [
                'headers' => array_merge($this->apiJsonHeaders, ['Authorization' => 'Bearer ' . $token]),
            ], false);
            if ($response->successful() && $response->json('message')) {
                $message = $response->json('message');
            }
        }
        $this->apiService->clearUserTokens();
        Session::forget(config('votix_api.session_user_key'));

        $locale = $request->route('locale', 'fr');
        return redirect()->route('home', ['locale' => $locale])->with('success', $message);
    }

    /**
     * POST /api/v1/auth/forgot-password
     */
    public function sendForgotPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => 'required|email']);

        $response = $this->apiService->makeApiRequest('POST', 'auth/forgot-password', [
            'json' => $validated,
            'headers' => $this->apiJsonHeaders,
        ], false);

        if (! $response->successful()) {
            $message = $response->json('message') ?? __('We can\'t find a user with that email address.');
            return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => $message]);
        }

        return redirect()->back()->with('status', $response->json('message') ?? __('We have emailed your password reset link.'));
    }

    /**
     * GET reset-password form (token & email from query)
     */
    public function showResetPassword(Request $request): View
    {
        $token = $request->query('token');
        $email = $request->query('email');

        return view('pages.auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * POST /api/v1/auth/reset-password
     */
    public function submitResetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $response = $this->apiService->makeApiRequest('POST', 'auth/reset-password', [
            'json' => $validated,
            'headers' => $this->apiJsonHeaders,
        ], false);

        if (! $response->successful()) {
            $message = $response->json('message') ?? __('This password reset link is invalid or has expired.');
            return redirect()->back()->withInput($request->only('email'))->withErrors(['email' => $message]);
        }

        $locale = $request->route('locale', 'fr');
        return redirect()->route('login', ['locale' => $locale])
            ->with('success', $response->json('message') ?? __('Your password has been reset.'));
    }

    /**
     * GET email-verified
     *
     * - When coming from the backend verification redirect, a ?login_ticket=... is present.
     * - The frontend exchanges this ticket for an access_token and user data,
     *   then redirects to the localized home as an authenticated user.
     * - If the ticket is invalid/expired or missing, a simple info page is shown.
     */
    public function showEmailVerified(Request $request)
    {
        $loginTicket = $request->query('login_ticket');

        if ($loginTicket) {
            $response = $this->apiService->makeApiRequest('POST', 'auth/exchange-ticket', [
                'json' => ['login_ticket' => $loginTicket],
                'headers' => $this->apiJsonHeaders,
            ], false);

            if ($response->successful() && ($response->json('success') ?? false)) {
                $data = $response->json('data') ?? [];
                $token = $data['access_token'] ?? null;
                $user = $data['user'] ?? null;

                // Compute TTL from access_expires_at if provided
                $expiresIn = 3600;
                if (! empty($data['access_expires_at'])) {
                    try {
                        $expiresAt = now()->parse($data['access_expires_at']);
                        $diff = $expiresAt->diffInSeconds(now(), false);
                        if ($diff > 60) {
                            $expiresIn = $diff;
                        }
                    } catch (\Throwable $e) {
                        // Fallback to default TTL
                    }
                }

                if ($token) {
                    $this->apiService->setUserTokens($token, '', $expiresIn);
                    if (is_array($user)) {
                        Session::put(config('votix_api.session_user_key'), $user);
                    }
                }

                $locale = $request->route('locale', config('app.locale', 'fr'));
                $message = $response->json('message') ?: __('Email verified');

                return redirect()->route('home', ['locale' => $locale])->with('success', $message);
            }

            // Ticket invalid or expired: show info page with error message
            $errorMessage = $response->json('message') ?? __('This link may have expired or already been used.');

            return view('pages.auth.email-verified', [
                'verified' => false,
                'errorMessage' => $errorMessage,
            ]);
        }

        // No ticket: just show generic info page (for manual visits)
        return view('pages.auth.email-verified', [
            'verified' => false,
            'errorMessage' => null,
        ]);
    }

    /**
     * POST /api/v1/auth/email/resend
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        $response = $this->apiService->makeApiRequest('POST', 'auth/email/resend', [
            'headers' => $this->apiJsonHeaders,
        ], false);

        if (! $response->successful()) {
            $code = $response->status();
            $message = $response->json('message');
            if ($code === 400) {
                return redirect()->back()->with('status', $message ?? __('Email already verified.'));
            }
            return redirect()->back()->withErrors(['form' => $message ?? __('Failed to send verification email.')]);
        }

        return redirect()->back()->with('status', $response->json('message') ?? __('Verification link sent.'));
    }

    public function socialRedirect(Request $request, string $locale, string $provider): RedirectResponse
    {
        if (! in_array($provider, ['google', 'tiktok'], true)) {
            return redirect()->route('login', ['locale' => $locale])
                ->withErrors(['form' => __('Unsupported social provider.')]);
        }

        if ($provider === 'google') {
            $redirectUri = $this->resolveSocialRedirectUri($request, $locale, 'google');
            return Socialite::driver('google')
                ->redirectUrl($redirectUri)
                ->scopes(['openid', 'email', 'profile'])
                ->redirect();
        }

        $state = bin2hex(random_bytes(24));
        $codeVerifier = $this->generatePkceCodeVerifier();
        $codeChallenge = $this->pkceCodeChallengeFromVerifier($codeVerifier);
        Session::put('social_oauth_state_tiktok', $state);
        Session::put('social_oauth_code_verifier_tiktok', $codeVerifier);
        Session::put('social_oauth_locale', $locale);

        $clientId = (string) config('services.tiktok.client_id', '');
        $redirectUri = $this->resolveSocialRedirectUri($request, $locale, 'tiktok');
        if ($clientId === '' || $redirectUri === '') {
            return redirect()->route('login', ['locale' => $locale])
                ->withErrors(['form' => __('TikTok login is not configured.')]);
        }

        $query = http_build_query([
            'client_key' => $clientId,
            'response_type' => 'code',
            'scope' => 'user.info.basic,user.info.profile',
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect()->away('https://www.tiktok.com/v2/auth/authorize/?' . $query);
    }

    public function socialCallback(Request $request, string $locale, string $provider): RedirectResponse
    {
        if (! in_array($provider, ['google', 'tiktok'], true)) {
            return redirect()->route('login', ['locale' => $locale])
                ->withErrors(['form' => __('Unsupported social provider.')]);
        }

        try {
            $socialData = $provider === 'google'
                ? $this->resolveGoogleProfile($request, $locale)
                : $this->resolveTikTokProfile($request, $locale);
        } catch (\Throwable $e) {
            Log::error('Social callback profile resolution failed', [
                'provider' => $provider,
                'locale' => $locale,
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'code' => $e->getCode(),
                'line' => $e->getLine(),
            ]);

            $userMessage = __('Social login failed. Please try again.');
            if (config('app.debug')) {
                $userMessage .= ' ' . $e->getMessage();
            }

            return redirect()->route('login', ['locale' => $locale])
                ->withErrors(['form' => $userMessage]);
        }

        if (empty($socialData['provider_id'])) {
            return redirect()->route('login', ['locale' => $locale])
                ->withErrors(['form' => __('Unable to identify your social account.')]);
        }

        $response = $this->apiService->makeApiRequest('POST', 'auth/social-login', [
            'json' => [
                'provider' => $provider,
                'provider_id' => $socialData['provider_id'],
                'email' => $socialData['email'] ?? null,
                'first_name' => $socialData['first_name'] ?? null,
                'last_name' => $socialData['last_name'] ?? null,
                'name' => $socialData['name'] ?? null,
            ],
            'headers' => $this->apiJsonHeaders,
        ], false);

        if (! $response->successful()) {
            $message = $response->json('message') ?? __('Social login failed. Please try again.');
            return redirect()->route('login', ['locale' => $locale])
                ->withErrors(['form' => $message]);
        }

        $data = $response->json('data');
        $token = is_array($data) ? ($data['access_token'] ?? null) : null;
        $user = is_array($data) ? ($data['user'] ?? null) : null;
        if (! $token) {
            return redirect()->route('login', ['locale' => $locale])
                ->withErrors(['form' => __('Social login failed. Missing token.')]);
        }

        $this->apiService->setUserTokens($token, '', 3600);
        if (is_array($user)) {
            Session::put(config('votix_api.session_user_key'), $user);
        }

        return redirect()->route('home', ['locale' => $locale])
            ->with('success', __('Welcome back.'));
    }

    /**
     * @return array{provider_id:string,email:?string,first_name:?string,last_name:?string,name:?string}
     */
    protected function resolveGoogleProfile(Request $request, string $locale): array
    {
        $driver = Socialite::driver('google')
            ->redirectUrl($this->resolveSocialRedirectUri($request, $locale, 'google'))
            ->stateless();

        // Windows local env can miss CA bundle chain for cURL (error 60).
        // Keep strict SSL verification outside local only.
        if (app()->environment('local')) {
            $driver->setHttpClient(new GuzzleClient([
                'verify' => false,
            ]));
        }

        $socialUser = $driver->user();
        $fullName = trim((string) ($socialUser->getName() ?? ''));
        $parts = $fullName !== '' ? preg_split('/\s+/', $fullName) : [];
        $first = is_array($parts) ? ((string) ($parts[0] ?? '')) : '';
        $last = is_array($parts) ? ((string) implode(' ', array_slice($parts, 1))) : '';

        return [
            'provider_id' => (string) $socialUser->getId(),
            'email' => $socialUser->getEmail(),
            'first_name' => $first !== '' ? $first : null,
            'last_name' => $last !== '' ? $last : null,
            'name' => $fullName !== '' ? $fullName : null,
        ];
    }

    /**
     * @return array{provider_id:string,email:?string,first_name:?string,last_name:?string,name:?string}
     */
    protected function resolveTikTokProfile(Request $request, string $locale): array
    {
        $state = (string) $request->query('state', '');
        $expectedState = (string) Session::pull('social_oauth_state_tiktok', '');
        $codeVerifier = (string) Session::pull('social_oauth_code_verifier_tiktok', '');
        if ($state === '' || $expectedState === '' || ! hash_equals($expectedState, $state)) {
            throw ValidationException::withMessages(['state' => __('Invalid OAuth state.')]);
        }
        if ($codeVerifier === '') {
            throw ValidationException::withMessages(['code_verifier' => __('Missing PKCE code verifier.')]);
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            throw ValidationException::withMessages(['code' => __('Missing authorization code.')]);
        }

        $clientKey = (string) config('services.tiktok.client_id', '');
        $clientSecret = (string) config('services.tiktok.client_secret', '');
        $redirectUri = $this->resolveSocialRedirectUri($request, $locale, 'tiktok');
        if ($clientKey === '' || $clientSecret === '' || $redirectUri === '') {
            throw ValidationException::withMessages(['provider' => __('TikTok login is not configured.')]);
        }

        $tokenRes = Http::asForm()
            ->timeout(config('votix_api.timeout', 30))
            ->post('https://open.tiktokapis.com/v2/oauth/token/', [
                'client_key' => $clientKey,
                'client_secret' => $clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
                'code_verifier' => $codeVerifier,
            ]);
        if (! $tokenRes->successful()) {
            throw ValidationException::withMessages(['provider' => __('Unable to retrieve TikTok access token.')]);
        }

        $accessToken = $tokenRes->json('access_token') ?: $tokenRes->json('data.access_token');
        if (! is_string($accessToken) || $accessToken === '') {
            throw ValidationException::withMessages(['provider' => __('Invalid TikTok token response.')]);
        }

        $profileRes = Http::timeout(config('votix_api.timeout', 30))
            ->withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])
            ->post('https://open.tiktokapis.com/v2/user/info/', [
                'fields' => ['open_id', 'display_name', 'username', 'avatar_url'],
            ]);
        if (! $profileRes->successful()) {
            throw ValidationException::withMessages(['provider' => __('Unable to retrieve TikTok profile.')]);
        }

        $user = $profileRes->json('data.user') ?? $profileRes->json('user') ?? [];
        if (! is_array($user)) {
            $user = [];
        }

        $displayName = trim((string) ($user['display_name'] ?? $user['username'] ?? ''));
        return [
            'provider_id' => (string) ($user['open_id'] ?? ''),
            'email' => null,
            'first_name' => null,
            'last_name' => null,
            'name' => $displayName !== '' ? $displayName : null,
        ];
    }

    protected function resolveSocialRedirectUri(Request $request, string $locale, string $provider): string
    {
        $configured = trim((string) config("services.{$provider}.redirect", ''));
        if ($configured !== '') {
            return $configured;
        }

        $relativeCallback = route('auth.social.callback', [
            'locale' => $locale,
            'provider' => $provider,
        ], false);

        return rtrim($request->getSchemeAndHttpHost(), '/') . $relativeCallback;
    }

    protected function generatePkceCodeVerifier(): string
    {
        $bytes = random_bytes(64);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }

    protected function pkceCodeChallengeFromVerifier(string $codeVerifier): string
    {
        $hash = hash('sha256', $codeVerifier, true);

        return rtrim(strtr(base64_encode($hash), '+/', '-_'), '=');
    }
}
