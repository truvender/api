<?php

namespace App\Http\Traits;

trait Jwt
{

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return IlluminateHttpJsonResponse
     */
    protected function respondWithToken($token, $expOverride = null)
    {

        $exp = auth()->factory()->getTTL() * 60;

        if ($expOverride) {
            $exp = auth()->factory()->setTTL($expOverride);
        }

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $exp
        ];
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }
}
