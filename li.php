<?php

function navigate($ch) {

	// $ch = curl_init();
	$data = array(
	    'email' => '',
	    'senha' => '',
	);


	curl_setopt($ch, CURLOPT_URL, "https://app.lojaintegrada.com.br/painel/login");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36');
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie-name');  //could be empty, but cause problems on some hosts
	curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/mycookie');  //could be empty, but cause problems on some hosts
	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);


	$output = curl_exec($ch);
	$info = curl_getinfo($ch);
	// curl_close($ch);

// echo $info;
// echo $output;

	if ($output === false)
	{
	    // throw new Exception('Curl error: ' . curl_error($crl));
	    print_r('Curl error: ' . curl_error($ch));
	    return null;
	}

	curl_setopt($ch, CURLOPT_URL, "https://app.lojaintegrada.com.br/painel/pedido/111/detalhar");
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "");

  $content = curl_exec( $ch );

	// curl_close($ch);

	return $content;
}


$ch = curl_init();

$output = navigate($ch);

var_dump($output);
echo $output;

curl_close( $ch );

echo "fim!";