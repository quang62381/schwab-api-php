<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

trait UserPreferenceRequests {

    use RequestTrait;

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function userPreference(): array {
        $suffix   = '/trader/v1/userPreference';
        $response = $this->_request( $suffix );
        return $this->json( $response );
    }
}