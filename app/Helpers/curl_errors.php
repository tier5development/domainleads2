<?php


function custom_curl_errors()
{
	$custom_curl_errors = array();

		$custom_curl_errors['Client error'] = "403 Forbidden Response - there are two possible causes. It could be due to a removal of file permission, or restriction of access based on the IP address of the user";

		$custom_curl_errors['Server error'] = "503 server error - server currently unable to handle the HTTP request due to a temporary overloading or maintenance of the server.";

		$custom_curl_errors['cURL error 5'] = "Couldn't resolve proxy. The given proxy host could not be resolved";

		$custom_curl_errors['cURL error 6'] = "Couldn't resolve host. The given remote host was not resolved";

		$custom_curl_errors['cURL error 7']= "Failed to connect() to host or proxy.";

		$custom_curl_errors['cURL error 8']="The server sent data libcurl couldn't parse. This error code is used for more than just FTP and is aliased as CURLE_WEIRD_SERVER_REPLY since 7.51.0.";

		$custom_curl_errors['cURL error 9']="We were denied access to the resource given in the URL. For FTP, this occurs while trying to change to the remote directories";
		
		$custom_curl_errors['cURL error 10']="While waiting for the server to connect back when an active FTP session is used, an error code was sent over the control connection or similar";

		$custom_curl_errors['cURL error 11']="After having sent the FTP password to the server, libcurl expects a proper reply. This error code indicates that an unexpected code was returned";
		$custom_curl_errors['cURL error 12']="During an active FTP session while waiting for the server to connect, the CURLOPT_ACCEPTTIMEOUT_MS (or the internal default) timeout expired";
		$custom_curl_errors['cURL error 13']="libcurl failed to get a sensible result back from the server as a response to either a PASV or a EPSV command. The server is flawed";

		$custom_curl_errors['cURL error 15']="An internal failure to lookup the host used for the new connection";

		$custom_curl_errors['cURL error 16']="A problem was detected in the HTTP2 framing layer. This is somewhat generic and can be one out of several problems, see the error buffer for details";

		$custom_curl_errors['cURL error 18']="A file transfer was shorter or larger than expected. This happens when the server first reports an expected transfer size, and then delivers data that doesn't match the previously given size";

		$custom_curl_errors['cURL error 19']="This was either a weird reply to a 'RETR' command or a zero byte transfer complete";
		
		$custom_curl_errors['cURL error 21']="When sending custom 'QUOTE' commands to the remote server, one of the commands returned an error code that was 400 or higher (for FTP) or otherwise indicated unsuccessful completion of the command";

		
		$custom_curl_errors['cURL error 22']="This is returned if CURLOPT_FAILONERROR is set TRUE and the HTTP server returns an error code that is >= 400";

		$custom_curl_errors['cURL error 23']="An error occurred when writing received data to a local file, or an error was returned to libcurl from a write callback";

		$custom_curl_errors['cURL error 26']="There was a problem reading a local file or an error returned by the read callback";
		
		$custom_curl_errors['cURL error 27']="A memory allocation request failed. This is serious badness and things are severely screwed up if this ever occurs";
		
		$custom_curl_errors['cURL error 28']= "Operation timeout. The specified time-out period was reached according to the conditions";
		
		$custom_curl_errors['cURL error 30']="The FTP PORT command returned error. This mostly happens when you haven't specified a good enough address for libcurl to use";
		$custom_curl_errors['cURL error 31']="The FTP REST command returned error. This should never happen if the server is sane";
		$custom_curl_errors['cURL error 33']="The server does not support or accept range requests";

		$custom_curl_errors['cURL error 34']="This is an odd error that mainly occurs due to internal confusion";

		$custom_curl_errors['cURL error 35']="A problem occurred somewhere in the SSL/TLS handshake. You really want the error buffer and read the message there as it pinpoints the problem slightly more. Could be certificates (file formats, paths, permissions), passwords, and others";

		$custom_curl_errors['cURL error 36']="The download could not be resumed because the specified offset was out of the file boundary";

		$custom_curl_errors['cURL error 37']="Permission issue for file path";

		$custom_curl_errors['cURL error 38']="LDAP cannot bind. LDAP bind operation failed";

		$custom_curl_errors['cURL error 39']="LDAP search failed";

		$custom_curl_errors['cURL error 41']="Function not found. A required zlib function was not found";

		$custom_curl_errors['cURL error 42']="Aborted by callback. A callback returned 'abort' to libcurl";

		$custom_curl_errors['cURL error 43']="Internal error. A function was called with a bad parameter";

		$custom_curl_errors['cURL error 45']="Interface error. A specified outgoing interface could not be used. Set which interface to use for outgoing connections' source IP address with CURLOPT_INTERFACE";

		$custom_curl_errors['cURL error 47']="Too many redirects";

		$custom_curl_errors['cURL error 48']="An option passed to libcurl is not recognized/known. Refer to the appropriate documentation. This is most likely a problem in the program that uses libcurl. The error buffer might contain more specific information about which exact option it concerns";

		$custom_curl_errors['cURL error 49']="A telnet option string was Illegally formatted";

		$custom_curl_errors['cURL error 51']="The remote server's SSL certificate or SSH md5 fingerprint was deemed not OK";

		$custom_curl_errors['cURL error 52']="Nothing was returned from the server, and under the circumstances, getting nothing is considered an error";

		$custom_curl_errors['cURL error 53']="The specified crypto engine wasn't found";

		$custom_curl_errors['cURL error 54']="Failed setting the selected SSL crypto engine as default";

		$custom_curl_errors['cURL error 55']="Failed sending network data";

		$custom_curl_errors['cURL error 56']="Failure with receiving network data";

		$custom_curl_errors['cURL error 58']="problem with the local client certificate";

		$custom_curl_errors['cURL error 59']="Couldn't use specified cipher";

		$custom_curl_errors['cURL error 61']="Unrecognized transfer encoding";

		$custom_curl_errors['cURL error 62']="Invalid LDAP URL";

		$custom_curl_errors['cURL error 63']="Maximum file size exceeded";


		$custom_curl_errors['cURL error 68']="File not found on TFTP server";

		$custom_curl_errors['cURL error 69']="Permission problem on TFTP server";

		$custom_curl_errors['cURL error 70']="Out of disk space on the server";

		$custom_curl_errors['cURL error 74']="This error should never be returned by a properly functioning TFTP server";

		$custom_curl_errors['cURL error 75']="Character conversion failed";

		$custom_curl_errors['cURL error 76']="Caller must register conversion callbacks";

		$custom_curl_errors['cURL error 77']="Problem with reading the SSL CA cert (path? access rights?)";

		$custom_curl_errors['cURL error 79']="An unspecified error occurred during the SSH session";

		$custom_curl_errors['cURL error 80']="Failed to shut down the SSL connection";

		$custom_curl_errors['cURL error 81']="Socket is not ready for send/recv wait till it's ready and try again";

		$custom_curl_errors['cURL error 82']="Failed to load CRL file";

		$custom_curl_errors['cURL error 83']="Curl SSL Issuer error.Issuer check failed";

		return $custom_curl_errors;
}

?>