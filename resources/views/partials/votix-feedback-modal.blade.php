{{--
    Boîte de dialogue unique pour les retours utilisateur (succès, erreur, info, avertissement).

    Flash session (priorité : error > success > status > info > warning) :
      - success, error, status, info, warning : message texte
      - *_title : titre optionnel (ex. success_title, error_title, status_title, …)

    JavaScript (après chargement de votix-feedback.js) :
      VotixFeedback.show({ type: 'success'|'error'|'info'|'warning', message: '...', title: '...' });
--}}
@php
    $votixFeedbackTitle = static function (string $key, string $default): string {
        $v = session($key);
        return (is_string($v) && $v !== '') ? $v : $default;
    };
    $votixFeedbackFlash = null;
    if (session()->has('error')) {
        $votixFeedbackFlash = [
            'type' => 'error',
            'message' => (string) session('error'),
            'title' => $votixFeedbackTitle('error_title', __('Error')),
        ];
    } elseif (session()->has('success')) {
        $votixFeedbackFlash = [
            'type' => 'success',
            'message' => (string) session('success'),
            'title' => $votixFeedbackTitle('success_title', __('Success')),
        ];
    } elseif (session()->has('status')) {
        $votixFeedbackFlash = [
            'type' => 'success',
            'message' => (string) session('status'),
            'title' => $votixFeedbackTitle('status_title', __('Success')),
        ];
    } elseif (session()->has('info')) {
        $votixFeedbackFlash = [
            'type' => 'info',
            'message' => (string) session('info'),
            'title' => $votixFeedbackTitle('info_title', __('Information')),
        ];
    } elseif (session()->has('warning')) {
        $votixFeedbackFlash = [
            'type' => 'warning',
            'message' => (string) session('warning'),
            'title' => $votixFeedbackTitle('warning_title', __('Warning')),
        ];
    }
@endphp

<style>
    .votix-feedback-modal .modal-content { border-radius: 12px; }
    .votix-feedback-modal__icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        font-size: 1.25rem;
    }
</style>

<div class="modal fade votix-feedback-modal" id="votixFeedbackModal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="votixFeedbackModalTitle">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body p-4">
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3 d-flex align-items-center justify-content-center rounded-circle votix-feedback-modal__icon" data-votix-role="icon-wrap" aria-hidden="true">
                        <i class="fa-solid fa-circle-info" data-votix-role="icon"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <h5 class="mb-1" id="votixFeedbackModalTitle" data-votix-role="title"></h5>
                        <p class="mb-0 text-muted small text-break" data-votix-role="message"></p>
                    </div>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-dark px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($votixFeedbackFlash !== null)
    <script type="application/json" id="votix-feedback-flash">{!! json_encode($votixFeedbackFlash, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endif
