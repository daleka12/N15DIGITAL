<?php
// Configuración
header('Access-Control-Allow-Origin: *'); // Permitir peticiones desde cualquier origen (CORS)
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

// Manejar peticiones OPTIONS preflight para CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Solo se acepta POST.']);
    exit();
}

// Obtener los datos del cuerpo de la petición
$inputJSON = file_get_contents('php://input');
$inputData = json_decode($inputJSON, true);

// Si php://input está vacío (formularios tradicionales), usar $_POST
if (empty($inputData) && !empty($_POST)) {
    $inputData = $_POST;
}

// Validar que haya datos
if (empty($inputData)) {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibieron datos.']);
    exit();
}

// Añadir fecha y hora de la petición y la IP del cliente (si es necesario)
$inputData['_timestamp'] = date('Y-m-d H:i:s');
$inputData['_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'Desconocida';

// Archivo donde se guardarán los datos
$archivo = 'datos_formularios.json';

// Leer los datos existentes (si el archivo existe)
$datosGuardados = [];
if (file_exists($archivo)) {
    $contenidoExistente = file_get_contents($archivo);
    $datosGuardados = json_decode($contenidoExistente, true);
    if (!is_array($datosGuardados)) {
        $datosGuardados = [];
    }
}

// Agregar los nuevos datos al arreglo
$datosGuardados[] = $inputData;

// Guardar de nuevo en el archivo
$exito = file_put_contents($archivo, json_encode($datosGuardados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

if ($exito) {
    http_response_code(200);
    echo json_encode(['success' => true, 'mensaje' => 'Datos guardados correctamente.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar los datos en el servidor.']);
}
?>
