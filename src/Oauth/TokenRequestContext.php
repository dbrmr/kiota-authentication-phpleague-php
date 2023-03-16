<?php
/**
 * Copyright (c) Microsoft Corporation.  All Rights Reserved.
 * Licensed under the MIT License.  See License in the project root
 * for license information.
 */


namespace Microsoft\Kiota\Authentication\Oauth;

use Http\Promise\Promise;

/**
 * Class TokenRequestContext
 * @package Microsoft\Kiota\Authentication
 * @copyright 2022 Microsoft Corporation
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://developer.microsoft.com/graph
 */
abstract class TokenRequestContext
{
    /**
     * Should return Promise that resolves with a new TokenRequestContext object to be used for a new token request
     * @var null|callable(string $claims): Promise
     */
    private $caeRedirectCallback = null;

    /**
     * Whether this client should add claims to inform API that it can handle claims challenges.
     * Disabled by default since different Identity Providers can be used with this lib. Not only Azure which
     * understands the cp1 claim
     * https://learn.microsoft.com/en-us/azure/active-directory/develop/claims-challenge?tabs=dotnet
     * @var bool
     */
    private bool $caeEnabled = false;

    /**
     * Return dictionary with OAuth 2.0 request parameters to be passed to PHP League's OAuth provider
     *
     * @return array<string, string>
     */
    abstract public function getParams(): array;

    /**
     * Returns subset of parameters to be used for refresh_token requests
     *
     * @param string $refreshToken refresh token in currently cached token
     * @return array<string, string>
     */
    abstract public function getRefreshTokenParams(string $refreshToken): array;

    /**
     * @return string Grant type
     */
    abstract public function getGrantType(): string;

    /**
     * Return the tenantId
     * @return string
     */
    abstract public function getTenantId(): string;

    /**
     * Return the identity of the user, this is used to cache the access token
     * @return string
     */
    abstract public function getIdentity(): string;

    /**
     * Whether the client should be enabled for Continuous Access Evaluation
     * https://learn.microsoft.com/en-us/azure/active-directory/conditional-access/concept-continuous-access-evaluation
     * Currently only works with Microsoft Identity
     * @return bool
     */
    public function isCAEEnabled(): bool
    {
        return $this->caeEnabled;
    }

    /**
     * Returns a callback that can be called to redirect the logged-in user to the Microsoft Identity login page
     * when this lib is unable to refresh the token using CAE claims.
     * If this callback returns a Promise that resolves to a new token request context with the new authentication
     * code/assertion then a new token is requested.
     * @return null|callable(string $claims): Promise
     */
    public function getCAERedirectCallback(): ?callable
    {
        return $this->caeRedirectCallback;
    }

    /**
     * @param bool $caeEnabled
     */
    public function setCAEEnabled(bool $caeEnabled): void
    {
        $this->caeEnabled = $caeEnabled;
    }

    /**
     * @param null|callable(string $claims): Promise $callback
     */
    public function setCAERedirectCallback(?callable $callback = null): void
    {
        $this->caeRedirectCallback = $callback;
    }
}
