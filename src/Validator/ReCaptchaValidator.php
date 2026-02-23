<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ReCaptchaValidator extends ConstraintValidator
{
    private $httpClient;
    private $reCaptchaSecret;
    private $requestStack;

    public function __construct(HttpClientInterface $httpClient, string $reCaptchaSecret, RequestStack $requestStack)
    {
        $this->httpClient = $httpClient;
        $this->reCaptchaSecret = $reCaptchaSecret;
        $this->requestStack = $requestStack;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\ReCaptcha */

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }
        $reCaptchaResponse = $request->request->get('g-recaptcha-response');

        if (!$reCaptchaResponse) {
            $this->context->buildViolation($constraint->message)->addViolation();
            return;
        }

        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->reCaptchaSecret,
                'response' => $reCaptchaResponse,
                'remoteip' => $request->getClientIp(),
            ],
        ]);

        $result = $response->toArray();

        if (!$result['success']) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
