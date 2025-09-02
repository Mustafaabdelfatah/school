<?php

namespace App\Services\Auth;

use App\Exceptions\InactiveUserException;
use App\Exceptions\InvalidEmailAndPasswordCombinationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use LdapRecord\Auth\PasswordRequiredException;
use LdapRecord\Auth\UsernameRequiredException;
use LdapRecord\Models\ActiveDirectory\User as ActiveDirectoryLdapUser;
use LdapRecord\Models\Attributes\Guid;
use LdapRecord\Models\OpenLDAP\User as OpenLdapUser;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginService extends BaseAuthService
{
    /**
     * @param array|null $data
     * @return array
     * @throws InactiveUserException
     * @throws InvalidEmailAndPasswordCombinationException
     */
    public function attempt(?array $data): array
    {
        $user = $this->attemptDefaultLogin($data);

        if (!$user->is_active) {
            throw new InactiveUserException(__('api.account_not_active'), ResponseAlias::HTTP_FORBIDDEN);
        }

        $this->setLastLogin($user);

        return [
            'user' => $user,
            'token' => $user->createToken($this->getGuard())->plainTextToken
        ];
    }

    /**
     * @param $data
     * @return mixed
     * @throws InvalidEmailAndPasswordCombinationException
     */
    public function attemptDefaultLogin($data): mixed
    {
        $user = $this->getModel()
            ->query()
            ->where('email', $data['email'])
            ->first();

        if (!$user || !Hash::check(@$data['password'], $user->password)) {
            throw new InvalidEmailAndPasswordCombinationException(__('api.invalid_email_and_password'), ResponseAlias::HTTP_NOT_ACCEPTABLE);
        }

        return $user;
    }

    /**
     * @param  $user
     * @return bool
     */
    public function setLastLogin($user): bool
    {
        $user->last_login = now();
        $user->save();

        return true;
    }


}
