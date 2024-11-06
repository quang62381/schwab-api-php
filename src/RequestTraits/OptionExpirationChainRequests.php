<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;


trait OptionExpirationChainRequests {

    use RequestTrait;


    /**
     * Get Option Expiration (Series) information for an optionable symbol.
     * Does not include individual options contracts for the underlying.
     *
     * @param string $symbol
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function expirationChain( string $symbol ): array {
        $suffix                      = '/marketdata/v1/expirationchain';
        $queryParameters             = [];
        $queryParameters[ 'symbol' ] = $symbol;

        if ( $queryParameters ):
            $suffix .= '?' . http_build_query( $queryParameters );
        endif;

        return $this->_request( $suffix );
    }


}