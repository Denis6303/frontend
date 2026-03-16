<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponseHandler;
use App\Services\ApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

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
        ], true);

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
        ], true);

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
        ], true);

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
        ], true);

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
            ], true);

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
}
