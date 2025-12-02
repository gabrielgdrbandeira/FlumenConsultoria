<?php
/**
 * Arquivo para verificar configurações do PHP
 * DELETE este arquivo após verificar!
 */
echo "<h2>Configurações PHP Importantes para Envio de Email</h2>";
echo "<hr>";

// Verifica extensões necessárias
$extensoes = ['openssl', 'sockets', 'stream'];
echo "<h3>Extensões PHP:</h3>";
foreach ($extensoes as $ext) {
    $status = extension_loaded($ext) ? '✓ Habilitada' : '✗ Desabilitada';
    $color = extension_loaded($ext) ? 'green' : 'red';
    echo "<p style='color: {$color};'><strong>{$ext}:</strong> {$status}</p>";
}

echo "<hr>";
echo "<h3>Configurações Importantes:</h3>";
echo "<p><strong>allow_url_fopen:</strong> " . (ini_get('allow_url_fopen') ? '✓ Habilitado' : '✗ Desabilitado') . "</p>";
echo "<p><strong>OpenSSL:</strong> " . (extension_loaded('openssl') ? '✓ Disponível' : '✗ Indisponível') . "</p>";
echo "<p><strong>Stream Socket:</strong> " . (function_exists('stream_socket_client') ? '✓ Disponível' : '✗ Indisponível') . "</p>";

echo "<hr>";
echo "<h3>Teste de Conexão SSL:</h3>";
$test_host = 'ssl://smtp.task.com.br:465';
$context = stream_context_create([
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

$test_socket = @stream_socket_client($test_host, $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context);
if ($test_socket) {
    echo "<p style='color: green;'>✓ Conexão SSL funcionando!</p>";
    fclose($test_socket);
} else {
    echo "<p style='color: red;'>✗ Erro na conexão SSL: {$errstr} (Código: {$errno})</p>";
    echo "<p><strong>Possíveis soluções:</strong></p>";
    echo "<ul>";
    echo "<li>Habilite 'allow_url_fopen' no painel de controle</li>";
    echo "<li>Verifique se a extensão OpenSSL está habilitada</li>";
    echo "<li>Verifique se a porta 465 não está bloqueada pelo firewall</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>Após verificar, DELETE este arquivo por segurança!</small></p>";
?>

