<?php

namespace MichaelDrennen\SchwabAPI\Exceptions;

use Throwable;

class RequestException extends \Exception {

    /**
     * @var string Ex: {"error":"unsupported_token_type","error_description":"400 Bad Request: \"{\"error_description\":\"Bad authorization code: String length must be a multiple of four. \",\"error\":\"invalid_request\"}\""}
     */
    protected string $rawResponseBody = '';


    /**
     * @var string Ex: unsupported_token_type
     */
    protected string $errorLabel;

    /**
     * @var int Ex: 400
     */
    protected int $errorCode;

    /**
     * @var string Ex: Bad Request
     */
    protected string $errorName;


    /**
     * @var string Ex: invalid_request
     */
    protected string $errorTag;

    /**
     * @var string Ex: Bad authorization code: String length must be a multiple of four.
     */
    protected string $errorDescription;


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

        $this->rawResponseBody = $rawResponseBody;


        $this->_parseRawResponseBody();

    }

    protected function _parseRawResponseBody(): void {
        $jsonResponse          = json_decode( $this->rawResponseBody, TRUE );
        $this->errorLabel      = $jsonResponse[ 'error' ];
        $errorDescription      = $jsonResponse[ 'error_description' ];
        $errorDescriptionParts = preg_split( '/:/', $errorDescription, 2 );

        $matches = [];
        preg_match( '/(\d{3}) (.*)/', $errorDescriptionParts[ 0 ], $matches );
        $this->errorCode         = $matches[ 1 ];
        $this->errorName         = $matches[ 2 ];
        $jsonStringWithErrorData = trim( $errorDescriptionParts[ 1 ], ' ' );
        $jsonStringWithErrorData = trim( $jsonStringWithErrorData, '"' );
        $errorData               = json_decode( $jsonStringWithErrorData, TRUE );
        $errorData               = array_map( 'trim', $errorData );
        $this->errorDescription  = $errorData[ 'error_description' ];
        $this->errorTag          = $errorData[ 'error' ];
    }
}