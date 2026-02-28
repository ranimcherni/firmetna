<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ReCaptchaValidator extends ConstraintValidator
{
    private $httpClient;
    private $reCaptchaSecret;
    private $requestStack;
    private $logger;

    public function __construct(HttpClientInterface $httpClient, string $reCaptchaSecret, RequestStack $requestStack, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->reCaptchaSecret = $reCaptchaSecret;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\ReCaptcha */

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        // Prefer the form field value if provided, otherwise fall back to the standard
        // Google field name 'g-recaptcha-response' that the widget injects.
        $reCaptchaResponse = null;
        if (is_string($value) && trim($value) !== '') {
            $reCaptchaResponse = $value;
        }

        if (!$reCaptchaResponse) {
            $reCaptchaResponse = $request->request->get('g-recaptcha-response');
        }

        if (!$reCaptchaResponse) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        try {
            $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => $this->reCaptchaSecret,
                    'response' => $reCaptchaResponse,
                    'remoteip' => $request->getClientIp(),
                ],
            ]);

            $result = $response->toArray();
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->error('reCAPTCHA verification request failed', ['exception' => $e->getMessage()]);
            }
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        if ($this->logger) {
            $this->logger->info('reCAPTCHA verification response', [
                'success' => $result['success'] ?? false,
                'score' => $result['score'] ?? null,
                'action' => $result['action'] ?? null,
                'error-codes' => $result['error-codes'] ?? null,
            ]);
        }

        if (!isset($result['success']) || $result['success'] !== true) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
