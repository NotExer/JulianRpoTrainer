<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    // Sanitize and validate input
    $name = filter_var($data->name ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($data->email ?? '', FILTER_SANITIZE_EMAIL);
    $phone = filter_var($data->phone ?? '', FILTER_SANITIZE_STRING);
    $message = filter_var($data->message ?? '', FILTER_SANITIZE_STRING);

    if (empty($name) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode(["message" => "Por favor completa todos los campos requeridos."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["message" => "Formato de correo inválido."]);
        exit;
    }

    // Email details
    $to = "samurpo20@gmail.com";
    $subject = "Nuevo mensaje de contacto de: $name";
    
    $email_content = "Has recibido un nuevo mensaje de contacto.\n\n";
    $email_content .= "Nombre: $name\n";
    $email_content .= "Email: $email\n";
    $email_content .= "Teléfono: $phone\n\n";
    $email_content .= "Mensaje:\n$message\n";

    $headers = "From: $name <$email>";

    // Send email
    if (mail($to, $subject, $email_content, $headers)) {
        http_response_code(200);
        echo json_encode(["message" => "¡Mensaje enviado con éxito!"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Hubo un error al enviar el mensaje. Inténtalo de nuevo."]);
    }
} else {
    http_response_code(403);
    echo json_encode(["message" => "Acceso denegado."]);
}
?>
