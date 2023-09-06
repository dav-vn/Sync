<?php

namespace Sync\Api;

use Exception;
use Illuminate\Support\Carbon;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Sync\Models\Access;

/**
 * Class TokensRefreshProducer
 *
 * @package Sync\Api
 */
class TokensRefreshService extends AuthService
{
    /** @var AuthService сервис авторизации */
    private AuthService $authService;

    /**
     * TokensRefreshProducer конструктор
     * */
    public function __construct()
    {
        $this->authService = new AuthService;
        parent::__construct('local');
    }

    /**
     * Проверка токенов, которые истекают в заданое время
     *
     * @param int $time
     * @return array
     */
    public function verifyTokensExpiration(int $time): array
    {
        $result = [];
        $currentTime = Carbon::now()->timestamp;
        $tokens = Access::all()->toArray();
        foreach ($tokens as $token) {
            $tokenUpdated = Carbon::parse($token['updated_at'])->timestamp;
            $tokenExpire = $tokenUpdated + ($time * 60 ** 2);
            if ($currentTime >= $tokenExpire) {
                $result[] = [
                    $token['amo_id'],
                ];
            }
        }

        return $result;
    }

    /**
     * Обновление токена
     *
     * @param int $userId
     * @return void
     * @throws Exception
     */
    public function refreshTokensExpiration(int $userId)
    {
        $accessToken = $this->authService->readToken($userId);

        $this->apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['base_domain'])
            ->onAccessTokenRefresh(
                function (AccessTokenInterface $accessToken, string $baseDomain) use ($userId) {
                $this->authService->saveToken($userId, [
                    'accessToken' => $accessToken->getToken(),
                    'refreshToken' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires(),
                    'baseDomain' => $baseDomain,
                ]);
            });
    }
}



