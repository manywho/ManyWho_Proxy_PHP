<?php

// Constants that are useful for routing the requests
$MANYWHO_API_URL = 'https://flow.manywho.com';
$THIS_PATH_NAME = '/manywhoproxy.php';

// Create a new array to store the header information
$request_headers = array( );

// First, we deal with the authorization header
if(!function_exists('apache_request_headers')) {
	// defines apache_request_headers function for IIS.
	function apache_request_headers() {
		$headers = array();
		foreach($_SERVER as $key => $value) {
			if(substr($key, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
			}
		}
		return $headers;
	}
}
$headers = apache_request_headers();

// Go through all of the header information coming into the server and take out everything we don't need - so our header is the server information
// not the browser
foreach ( $headers as $key => $value ) {
	// Grab out each of the http_ headers from the request
	$header_name = strtolower($key);
		
	// Convert the header information to the correct case
	if ($header_name == 'manywhotenant') {
		$header_name = 'ManyWhoTenant';
		$include_header = true;
	} else if ($header_name == 'manywhostate') {
		$header_name = 'ManyWhoState';
		$include_header = true;
	} else if ($header_name == 'authorization') {
		$header_name = 'Authorization';
		$include_header = true;
	} else if ($header_name == 'culture') {
		$header_name = 'Culture';
		$include_header = true;
	} else if ($header_name == 'content-type') {
		$header_name = 'Content-Type';
		$include_header = true;
	}

	if ($include_header == true) {
		// Add the header to the array to send up the stack
		$request_headers[] = "$header_name: $value";
	}
}

// Get the uri of the incoming request
$request_uri = $_SERVER['REQUEST_URI'];

	// Replace this page with the manywho page so we have the correct uri for ManyWho
	$request_uri = str_replace($THIS_PATH_NAME, $MANYWHO_API_URL, $request_uri);

	// The request we make to ManyWho depends on the request method coming in
	$request_method = $_SERVER['REQUEST_METHOD'];

	// Get the request parameters out of the request
	if ( 'GET' == $request_method ) {
		$request_params = $_GET;
	} elseif ( 'POST' == $request_method ) {
		$post = file_get_contents('php://input');
	}

	// Append query string for GET requests
	if ( $request_method == 'GET' && count( $request_params ) > 0 ) {
		$request_uri .= '?' . http_build_query( $request_params );
	}

	// Create the request and send over to ManyWho
	$ch = curl_init( $request_uri );

	// Turn off SSL certificate verification for now
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
	
	curl_setopt( $ch, CURLOPT_HTTPHEADER, $request_headers );   // (re-)send headers
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );	 // return response
	curl_setopt( $ch, CURLOPT_HEADER, true );	   // enabled response headers

	// Add data for POST, PUT or DELETE requests
	if ( 'POST' == $request_method ) {
		$json = json_encode($post);
		
		//for some diff
		curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post );
		curl_setopt($ch, CURLOPT_POST, 1);
	}

	// Dispatch the request and get the response (headers and content)
	$response = curl_exec( $ch );

	// Get the information back from the response
	$header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
	$header = substr($response, 0, $header_size);
	$body = substr($response, $header_size);
	
	// Check to see if there are any errors
	if ($body == false) {
		echo curl_error( $ch );
	}
	
	curl_close( $ch );

if (strpos($request_uri,'/api/') !== false) {
	// Set the header as json
	header('Content-Type: application/json');
} else {
	// Set the header as html
	header('Content-Type: text/html');
}
	
	// Finally, output the content
	print( $body );

?>