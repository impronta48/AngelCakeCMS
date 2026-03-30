<?php

declare(strict_types=1);

namespace App\Authenticator;

use Authentication\Authenticator\JwtAuthenticator;
use Psr\Http\Message\ServerRequestInterface;
use Authentication\Authenticator\Result;
use Authentication\Authenticator\ResultInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CookieJwtAuthenticator extends JwtAuthenticator
{
    /**
     * Esegue l'autenticazione JWT anche da cookie.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Authentication\Authenticator\ResultInterface
     */
    public function authenticate(ServerRequestInterface $request): ResultInterface
    {
        // prova il comportamento standard (header, queryParam, ecc.)
        $result = parent::authenticate($request);

        if ($result->isValid()) {
            return $result;
        }

        //  se non c'è token valido, prova a leggere dal cookie
        $cookieName = $this->getConfig('cookie') ?? 'jwt_token';
        $token = $request->getCookieParams()[$cookieName] ?? null;

        if (empty($token)) {
            return new Result(null, Result::FAILURE_CREDENTIALS_MISSING, [
                'message' => 'JWT non trovato né negli header né nel cookie'
            ]);
        }

        // Decodifica il JWT usando firebase/php-jwt
        $secret = $this->getConfig('secretKey') ?? env('JWT_SECRET');
        $algorithm = $this->getConfig('algorithm') ?? 'HS256';

        try {
            // JWT::decode ritorna stdClass -> convertiamo in array
            $decoded = JWT::decode($token, new Key($secret, $algorithm));
            $payload = (array)$decoded;
        } catch (\Throwable $e) {
            return new Result(null, Result::FAILURE_CREDENTIALS_INVALID, [
                'message' => 'Token JWT non valido o scaduto',
                'exception' => $e->getMessage()
            ]);
        }

        // 4) Identifica l'utente tramite l'identifier già caricato nel servizio
        //    getIdentifier() dovrebbe essere disponibile nell'Authenticator base
        $identifier = $this->getIdentifier();
        if (!$identifier) {
            return new Result(null, Result::FAILURE_CREDENTIALS_INVALID, [
                'message' => 'Identifier non configurato nell\'authenticator'
            ]);
        }

        // Identifier->identify si aspetta tipicamente un array di dati; passiamo il payload
        try {
            $user = $identifier->identify($payload);
        } catch (\Throwable $e) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, [
                'message' => 'Errore durante l\'identify',
                'exception' => $e->getMessage()
            ]);
        }

        if (empty($user)) {
            return new Result(null, Result::FAILURE_IDENTITY_NOT_FOUND, [
                'message' => 'Utente non trovato per il token JWT'
            ]);
        }

        // 5) Ritorna risultato di successo
        return new Result($user, Result::SUCCESS);
    }
}
