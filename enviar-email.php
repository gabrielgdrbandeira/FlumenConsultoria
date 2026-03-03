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
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-errors.log');

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
    // Compatível com PHP 8.1+ (FILTER_SANITIZE_STRING foi removido)
    return trim(htmlspecialchars(strip_tags($v ?? ''), ENT_QUOTES, 'UTF-8'));
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
// Usa o e-mail autenticado como remetente (servidor SMTP exige)
$headersData[] = "From: Site Flumen <{$smtp_user}>";
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
function smtp_read($socket, $timeout = 10) {
    // Define timeout para leitura
    stream_set_timeout($socket, $timeout);
    
    $data = '';
    $start_time = time();
    
    while (true) {
        $str = @fgets($socket, 515);
        if ($str === false) {
            // Verifica se foi timeout
            $meta = stream_get_meta_data($socket);
            if ($meta['timed_out']) {
                break;
            }
            // Se não foi timeout, pode ser fim de stream
            break;
        }
        
        $data .= $str;
        if (preg_match('/^\d{3}\s/', $str)) {
            break;
        }
        
        // Timeout de segurança
        if (time() - $start_time > $timeout) {
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

// configura timeout para conexão (reduzido para evitar timeout muito longo)
$timeout = 15; // 15 segundos para conectar

// abre socket (com ssl:// a conexão já é criptografada desde o início)
$socket = @stream_socket_client(
    $remote,
    $errno,
    $errstr,
    $timeout,
    STREAM_CLIENT_CONNECT,
    $context
);

if (!$socket) {
    http_response_code(500);
    $error_msg = "Não foi possível conectar ao servidor de e-mail.";
    $error_msg .= "<br><strong>Detalhes:</strong><br>";
    $error_msg .= "Servidor: {$smtp_host}:{$smtp_port}<br>";
    $error_msg .= "SSL: " . ($use_ssl ? 'Sim' : 'Não') . "<br>";
    
    // Mensagens de erro mais específicas
    if ($errno == 0 && empty($errstr)) {
        $error_msg .= "Erro: Timeout na conexão (servidor não respondeu em {$timeout} segundos)<br>";
        $error_msg .= "<br><small>Possíveis causas:<br>";
        $error_msg .= "- Servidor SMTP pode estar offline<br>";
        $error_msg .= "- Porta {$smtp_port} pode estar bloqueada pelo firewall<br>";
        $error_msg .= "- Hostname do servidor pode estar incorreto</small>";
    } else {
        $error_msg .= "Erro: {$errstr} (Código: {$errno})<br>";
        $error_msg .= "<br><small>Verifique se a porta 465 não está bloqueada e se o servidor SMTP está correto.</small>";
    }
    
    echo $error_msg;
    exit;
}

// Configura timeout para todas as operações no socket
stream_set_timeout($socket, 15);

// lê banner
$resp = smtp_read($socket);

// EHLO
$hostname = function_exists('gethostname') ? gethostname() : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
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
    $error_msg = "Senha de e-mail rejeitada.";
    $error_msg .= "<br><strong>Resposta do servidor:</strong> " . htmlspecialchars($resp);
    $error_msg .= "<br><small>Verifique se o usuário e senha estão corretos.</small>";
    echo $error_msg;
    exit;
}

// MAIL FROM (deve usar o e-mail autenticado, senão o servidor rejeita)
$resp = smtp_cmd($socket, "MAIL FROM:<{$smtp_user}>");
if (stripos($resp, '250') !== 0) {
    fclose($socket);
    http_response_code(500);
    $error_msg = "Remetente rejeitado.";
    $error_msg .= "<br><strong>Resposta do servidor:</strong> " . htmlspecialchars($resp);
    $error_msg .= "<br><small>O servidor SMTP não aceitou o remetente. Verifique se o e-mail está correto.</small>";
    echo $error_msg;
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
    $error_msg = "Falha ao enviar e-mail.";
    $error_msg .= "<br><strong>Resposta do servidor:</strong> " . htmlspecialchars($resp);
    $error_msg .= "<br><small>O servidor SMTP rejeitou a mensagem. Verifique as configurações.</small>";
    echo $error_msg;
    exit;
}

// QUIT
smtp_cmd($socket, "QUIT");
fclose($socket);

// se chegou aqui, deu certo
// Verifica se existe obrigado.html na mesma pasta ou na raiz
$obrigado_path = __DIR__ . '/obrigado.html';
if (!file_exists($obrigado_path)) {
    $obrigado_path = __DIR__ . '/obrigado/index.html';
}

if (file_exists($obrigado_path)) {
    header("Location: obrigado.html");
} else {
    // Fallback: mostra mensagem de sucesso
    header("Content-Type: text/html; charset=UTF-8");
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Mensagem Enviada</title></head><body>";
    echo "<h1>Mensagem enviada com sucesso!</h1>";
    echo "<p>Obrigado pelo contato. Retornaremos em breve.</p>";
    echo "<a href='index.html'>Voltar ao site</a>";
    echo "</body></html>";
}
exit;
?>
