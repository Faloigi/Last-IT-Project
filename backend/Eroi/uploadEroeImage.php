<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Content-Type: application/json');

$targetDir = __DIR__ . '/../../Frontend/public/images/heroes/';
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'Nessun file inviato']);
    exit;
}

$file = $_FILES['image'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'Formato non supportato']);
    exit;
}

// Se vuoi sovrascrivere sempre lo stesso file per ogni eroe, usa il nome passato via POST (se presente)
$nomeEroe = isset($_POST['nomeEroe']) ? $_POST['nomeEroe'] : null;
if ($nomeEroe) {
    $filename = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $nomeEroe)) . '.' . $ext;
} else {
    $filename = uniqid('eroe_', true) . '.' . $ext;
}
$targetFile = $targetDir . $filename;

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode(['success' => true, 'filename' => $filename]);
} else {
    echo json_encode(['success' => false, 'error' => 'Errore nel salvataggio']);
} 