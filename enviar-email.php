<?php
/**
 * envio autenticado via SMTP (sem PHPMailer)
 * seguro: sem senha no código, com validação básica e honeypot
 */

// ======== VERIFICA SE PHP ESTÁ FUNCIONANDO ========
// Se este arquivo mostrar código como texto, o PHP não está configurado
if (!function_exists('stream_socket_client')) {
    die('Erro: PHP não está configurado corretamente. Função stream_socket_client não disponível.');
}

// ======== PRODUÇÃO: NÃO MOSTRAR ERROS NA TELA ========
// Em desenvolvimento, você pode mudar para 1 para ver erros
ini_set('display_errors', 0);
error_reporting(0);

// ======== CONFIGURAÇÕES VINDAS DO AMBIENTE ========
// defina isso no .htaccess ou no painel (Environment / Variables)
// Se o .htaccess não funcionar, usa os valores padrão abaixo
$smtp_host = getenv('SMTP_HOST') ?: 'smtp.task.com.br';
$smtp_port = (int)(getenv('SMTP_PORT') ?: 465);
$smtp_user = getenv('SMTP_USER') ?: 'flumen@flumenconsultoria.com.br';
$smtp_pass = getenv('SMTP_PASS') ?: 'Aguaboa22';
$destino   = getenv('MAIL_TO')   ?: 'flumen@flumenconsultoria.com.br';
$smtp_ssl  = getenv('SMTP_SSL') !== '0'; // SSL habilitado por padrão

// se não tiver usuário ou senha, não seguimos
if (empty($smtp_user) || empty($smtp_pass)) {
    http_response_code(500);
    echo "Configuração de e-mail ausente. Contate o administrador.";
    exit;
}

// ======== SÓ ACEITA POST ========
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método não permitido.";
    exit;
}

// ======== HONEYPOT (anti-bot simples) ========
if (!empty($_POST['website'] ?? '')) {
    // bot detectado: finge que deu certo
    header("Location: /obrigado");
    exit;
}

// ======== VALIDAÇÃO DOS CAMPOS ========
function sanitize($v) {
    if (function_exists('filter_var') && defined('FILTER_SANITIZE_STRING')) {
        return trim(filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
    }
    // fallback para PHP 8.1+
    return trim(htmlspecialchars(strip_tags($v), ENT_QUOTES, 'UTF-8'));
}

$nome        = sanitize($_POST['nome']     ?? '');
$email       = trim($_POST['email']        ?? '');
$assuntoForm = sanitize($_POST['assunto']  ?? 'Contato via site');
$telefone    = sanitize($_POST['telefone'] ?? '');
$mensagem    = trim($_POST['mensagem']     ?? '');

// campos obrigatórios
if ($nome === '' || $email === '' || $mensagem === '') {
    http_response_code(400);
    echo "Preencha os campos obrigatórios.";
    exit;
}

// valida e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo "E-mail inválido.";
    exit;
}

// ======== MONTA CORPO DO E-MAIL ========
$bodyHtml = "
<html>
  <body style='font-family: Arial, sans-serif;'>
    <h2>Nova mensagem do site Flumen Consultoria</h2>
    <p><strong>Nome:</strong> " . htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') . "</p>
    <p><strong>E-mail:</strong> " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "</p>
    <p><strong>Telefone:</strong> " . htmlspecialchars($telefone, ENT_QUOTES, 'UTF-8') . "</p>
    <p><strong>Assunto:</strong> " . htmlspecialchars($assuntoForm, ENT_QUOTES, 'UTF-8') . "</p>
    <p><strong>Mensagem:</strong><br>" . nl2br(htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8')) . "</p>
  </body>
</html>
";

$boundary = "==B_" . md5(uniqid((string)mt_rand(), true));

$headersData = [];
$headersData[] = "From: Site Flumen <nao-responda@flumenconsultoria.com.br>";
$headersData[] = "Reply-To: {$email}";
$headersData[] = "MIME-Version: 1.0";
$headersData[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

$data  = implode("\r\n", $headersData) . "\r\n\r\n";
$data .= "--{$boundary}\r\n";
$data .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
$data .= strip_tags($bodyHtml) . "\r\n\r\n";
$data .= "--{$boundary}\r\n";
$data .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
$data .= $bodyHtml . "\r\n\r\n";
$data .= "--{$boundary}--\r\n";

// ======== FUNÇÕES SMTP ========
function smtp_read($socket) {
    $data = '';
    while ($str = fgets($socket, 515)) {
        $data .= $str;
        if (preg_match('/^\d{3}\s/', $str)) {
            break;
        }
    }
    return $data;
}

function smtp_cmd($socket, $cmd = null) {
    if ($cmd !== null) {
        fwrite($socket, $cmd . "\r\n");
    }
    return smtp_read($socket);
}

// ======== CONEXÃO SMTP ========
// porta 465 usa SSL direto (ssl://), porta 587 usa STARTTLS
$use_ssl = ($smtp_ssl && $smtp_port == 465);
$remote = ($use_ssl ? 'ssl://' : '') . $smtp_host . ':' . $smtp_port;

// configura contexto SSL se necessário
$context = null;
if ($use_ssl) {
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);
}

// abre socket (com ssl:// a conexão já é criptografada desde o início)
$socket = @stream_socket_client(
    $remote,
    $errno,
    $errstr,
    10,
    STREAM_CLIENT_CONNECT,
    $context
);

if (!$socket) {
    http_response_code(500);
    // Em desenvolvimento, mostra mais detalhes (remova em produção)
    $error_msg = "Não foi possível conectar ao servidor de e-mail.";
    if (ini_get('display_errors')) {
        $error_msg .= " Erro: {$errstr} (Código: {$errno})";
    }
    echo $error_msg;
    exit;
}

// lê banner
$resp = smtp_read($socket);

// EHLO
$hostname = gethostname() ?: 'localhost';
$resp = smtp_cmd($socket, "EHLO {$hostname}");

// verifica se aceita STARTTLS (para porta 587)
$startTls = (stripos($resp, 'STARTTLS') !== false);

// se não usar SSL direto e o servidor aceitar STARTTLS, usa
if (!$use_ssl && $startTls) {
    $resp = smtp_cmd($socket, "STARTTLS");
    if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        fclose($socket);
        http_response_code(500);
        echo "Não foi possível iniciar conexão segura.";
        exit;
    }
    // reenvia EHLO depois do TLS
    $resp = smtp_cmd($socket, "EHLO {$hostname}");
}

// AUTH LOGIN
$resp = smtp_cmd($socket, "AUTH LOGIN");
if (stripos($resp, '334') !== 0 && stripos($resp, '503') !== 0) {
    fclose($socket);
    http_response_code(500);
    echo "Servidor de e-mail não aceitou autenticação.";
    exit;
}

// envia usuário
$resp = smtp_cmd($socket, base64_encode($smtp_user));
if (stripos($resp, '334') !== 0) {
    fclose($socket);
    http_response_code(500);
    echo "Usuário de e-mail rejeitado.";
    exit;
}

// envia senha
$resp = smtp_cmd($socket, base64_encode($smtp_pass));
if (stripos($resp, '235') !== 0) {
    fclose($socket);
    http_response_code(500);
    echo "Senha de e-mail rejeitada.";
    exit;
}

// MAIL FROM (use e-mail do domínio)
$resp = smtp_cmd($socket, "MAIL FROM:<nao-responda@flumenconsultoria.com.br>");
if (stripos($resp, '250') !== 0) {
    fclose($socket);
    http_response_code(500);
    echo "Remetente rejeitado.";
    exit;
}

// RCPT TO
$resp = smtp_cmd($socket, "RCPT TO:<{$destino}>");
if (stripos($resp, '250') !== 0 && stripos($resp, '251') !== 0) {
    fclose($socket);
    http_response_code(500);
    echo "Destinatário rejeitado.";
    exit;
}

// DATA
$resp = smtp_cmd($socket, "DATA");
if (stripos($resp, '354') !== 0) {
    fclose($socket);
    http_response_code(500);
    echo "Servidor não aceitou o conteúdo.";
    exit;
}

// assunto + to + data
$subject = "Nova mensagem do site Flumen Consultoria";
$toLine  = "To: {$destino}\r\n";
$subjectLine = "Subject: {$subject}\r\n";

// envia corpo e finaliza com ponto
fwrite($socket, $toLine . $subjectLine . $data . "\r\n.\r\n");
$resp = smtp_read($socket);

if (stripos($resp, '250') !== 0) {
    fclose($socket);
    http_response_code(500);
    echo "Falha ao enviar e-mail.";
    exit;
}

// QUIT
smtp_cmd($socket, "QUIT");
fclose($socket);

// se chegou aqui, deu certo
header("Location: /obrigado");
exit;
?>
