<?php
error_reporting(E_ALL);

echo "<h2>TCP/IP Connection</h2>\n";

/* Get the port for the WWW service. */
$service_port = '1337';

/* Get the IP address for the target host. */
$address = '127.0.0.1';

/* Create a TCP/IP socket. */
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
} else {
    echo "OK.\n";
}

echo "Attempting to connect to '$address' on port '$service_port'...";
$result = socket_connect($socket, $address, $service_port);
if ($result === false) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
} else {
    echo "OK.\n";
}

$in = "joinRoom:{\"authorizationToken\": \"q59pXT/16gklUzC0ItGpw6BkG+lSqoMV0SOTC0WxyTfR/k+5f/odaYQuWsM4FRF7+gFqM6R+xKv/shhaKR7Iux+cwV8gvKvZnLEB3vc65n7BqI2+qznvu/Useo2tb1dj2hnQDUzGot1Ry6SClkDFKuUR4CFOqTsS0CNAdKRvUPwBXAxypshw3clLMkcQV3kQ8b/GHXvrWnsxeuStNqccZN+rAF28wRWYtc9bLs6ekgxCd6SvxXGkb3bbQ0wGt+YJ\"}";

socket_write($socket, $in, strlen($in));
echo "Writing $in";
socket_write($socket, "\n", strlen($in));
echo "OK.\n";

echo "Reading response:\n\n";
while ($out = socket_read($socket, 2048)) {
    echo $out;
}

echo "Closing socket...";
socket_close($socket);
echo "OK.\n\n";
?>