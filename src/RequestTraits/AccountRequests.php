<?php

namespace MichaelDrennen\SchwabAPI\RequestTraits;

trait AccountRequests {

    use RequestTrait;


    /**
     * @return array
     * print_r() of $data
     * Array ( [0] => Array ( [accountNumber] => 27834695236 [hashValue] => 397465203847059238764059762304 ) [1] => Array ( [accountNumber] => 08347502745 [hashValue] => A34502983740527304857203947580A535A67529CC ) )
     * The 'hashValue' is the 'encrypted' accountNumber that is used in all other REQUESTS to the Schwab API system.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function accountNumbers(): array {
        $suffix   = '/trader/v1/accounts/accountNumbers';
        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * All the linked account information for the user logged in.
     * The balances on these accounts are displayed by default, however
     * the positions on these accounts will be displayed based on the "positions" flag.
     *
     * @param bool $positions Setting to TRUE adds a query parameter to the endpoint that tells Schwab to return POSITION data as well.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function accounts( bool $positions = FALSE ): array {
        $suffix = '/trader/v1/accounts';
        if ( $positions ):
            $suffix .= '?fields=positions';
        endif;
        $response = $this->_request( $suffix );
        return $this->json( $response );
    }


    /**
     * @param string $hashValueOfAccountNumber This hash value is returned by the '/accounts/accountNumbers' endpoint. Ex: E49D5746FD010E582E364C28E9D6A763D972C3A0C0C90170878260D0A6C65453
     * @param array  $fields                   In the Schwab docs, the only 'field' mentioned is 'positions' like in the accounts() method above. However, they elude to there being more.
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function account( string $hashValueOfAccountNumber, array $fields = [] ): array {
        $suffix = '/trader/v1/accounts/' . $hashValueOfAccountNumber;

        if ( $fields ):
            $suffix .= '?' . http_build_query( $fields );
        endif;

        $response = $this->_request( $suffix );
        return $this->json( $response );
    }
}