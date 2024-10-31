<?php

namespace MichaelDrennen\SchwabAPI\Exceptions;

use Throwable;

class RequestTokenException extends \Exception {

    protected string $rawResponseBody     = '';
    protected array  $rawResponse         = [];
    protected string $rawError            = '';
    protected string $rawErrorDescription = '';


    /**
     * @var string Ex: 400
     */
    protected string $errorCode = '';
    /**
     * @var string Ex: Bad Request
     */
    protected mixed $errorCodeDescription = '';


    /**
     * @param string          $message
     * @param int             $code
     * @param \Throwable|null $previous
     * @param string          $rawResponseBody Ex: {"error":"unsupported_token_type","error_description":"400 Bad Request: \"{\"error_description\":\"Bad authorization code: String length must be a multiple of four. \",\"error\":\"invalid_request\"}\""}
     */
    public function __construct( string     $message = "",
                                 int        $code = 0,
                                 ?Throwable $previous = NULL,
                                 string     $rawResponseBody = '' ) {
        parent::__construct( $message, $code, $previous );

        $this->rawResponseBody     = $rawResponseBody;
        $this->rawResponse         = json_decode( $rawResponseBody, TRUE );
        $this->rawError            = $this->rawResponse[ 'error' ];
        $this->rawErrorDescription = $this->rawResponse[ 'error_description' ];

        $this->_parseErrorDescription();

    }

    protected function _parseErrorDescription(): void {
        $errorDescriptionParts = preg_split( '/:/', $this->rawErrorDescription, 2 );

        $errorDescriptionParts = array_map( 'trim', $errorDescriptionParts );

        $errorCodeParts             = preg_split( '/\s+/', $errorDescriptionParts[ 0 ], 2 );
        $this->errorCode            = $errorCodeParts[ 0 ];
        $this->errorCodeDescription = $errorCodeParts[ 1 ];
    }
}