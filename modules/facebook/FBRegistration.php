 <?php 
function parse_signed_request($signed_request, $secret) {
  list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

	// decode the data
	$sig = base64_url_decode($encoded_sig);
	$data = json_decode(base64_url_decode($payload), true);

	if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
		throw new Exception('Unknown algorithm. Expected HMAC-SHA256');
	}

	// check sig
		 $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
	if ($sig !== $expected_sig) {
		throw new Exception('Bad Signed JSON signature!');
	}
	return $data;
}

function base64_url_decode($input) {
	return base64_decode(strtr($input, '-_', '+/'));
} 
 ?>