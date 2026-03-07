<?php

namespace App\Http\Traits;

use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;

trait ApiResponseHandler
{
    /**
     * Handle API response: on success optionally redirect and flash message;
     * on failure redirect back with errors and old input.
     *
     * @param  array{redirect?: string, message?: string}  $successData
     * @param  array  $errorData  unused, reserved for custom error handling
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function handleApiResponse(Response $response, array $successData = [], array $errorData = []): ?RedirectResponse
    {
        $json = $response->json() ?? [];
        $success = (bool) ($json['success'] ?? false);
        $message = $json['message'] ?? '';

        if ($response->successful() && $success) {
            $flashMessage = $successData['message'] ?? $message ?: __('Operation successful.');
            if (! empty($successData['redirect'])) {
                return redirect()->to($successData['redirect'])->with('success', $flashMessage);
            }
            if (! empty($successData['message']) || $message) {
                return redirect()->back()->with('success', $flashMessage);
            }
            return null;
        }

        $errors = $this->extractErrorsFromApiResponse($json);
        return redirect()->back()
            ->withErrors($errors)
            ->withInput();
    }

    /**
     * Extract validation/error messages from API error response for withErrors().
     *
     * @return array<string, mixed>
     */
    protected function extractErrorsFromApiResponse(array $json): array
    {
        $data = $json['data'] ?? [];
        if (! is_array($data)) {
            $data = [];
        }

        $errors = $data['errors'] ?? null;
        if (is_array($errors)) {
            return $errors;
        }

        $message = $json['message'] ?? $data['form'] ?? $data['messages'] ?? null;
        if (is_string($message)) {
            return ['form' => $message];
        }
        if (is_array($message)) {
            return ['form' => implode(' ', $message)];
        }

        return ['form' => __('An error occurred. Please try again.')];
    }
}
