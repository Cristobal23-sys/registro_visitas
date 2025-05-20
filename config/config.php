<?php
// Configuración general de la aplicación

// Zona horaria (Santiago, Chile)
date_default_timezone_set('America/Santiago');

// URL base de la aplicación
define('BASE_URL', 'http://localhost/registro_visitas'); // Ajusta si es necesario

// Configuración de correo electrónico para zhykoluna@gmail.com
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587); // Puerto para TLS
define('MAIL_USERNAME', 'zhykoluna@gmail.com');
define('MAIL_PASSWORD', 'dfcr dbjj hqws yfvx');
define('MAIL_FROM', 'zhykoluna@gmail.com'); // El remitente del correo
define('MAIL_FROM_NAME', 'Sistema de Registro de Visitas'); // El nombre que se mostrará como remitente

// Cargar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Asegúrate de que esta ruta al autoload de Composer sea correcta

// Función para enviar correos
function enviarCorreo($destinatario, $asunto, $mensaje) {
    $mail = new PHPMailer(true); // Pasar true para habilitar excepciones

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true; // Habilitar autenticación SMTP
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Habilitar encriptación TLS explícita
        $mail->Port = MAIL_PORT;

        // Configuración de codificación (recomendado)
        $mail->CharSet = 'UTF-8';

        // Remitente y Destinatarios
        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($destinatario); // Añadir un destinatario

        // Contenido del correo
        $mail->isHTML(true); // Establecer formato de correo a HTML
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo a {$destinatario}: " . $mail->ErrorInfo);
        return false;
    }
}

?>