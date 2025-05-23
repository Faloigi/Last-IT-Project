<?php
header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$targetDir = __DIR__ . '/../../Frontend/public/images/clans/';
if (!isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'Nessun file inviato']);
    exit;
}
$file = $_FILES['image'];
$nomeClan = $_POST['nomeClan'] ?? '';
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = ($nomeClan ? preg_replace('/[^a-zA-Z0-9_]/', '', $nomeClan) . '_' : '') . uniqid() . '.' . $ext;
$targetFile = $targetDir . $filename;

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    echo json_encode(['success' => true, 'filename' => $filename]);
} else {
    echo json_encode(['success' => false, 'error' => 'Errore salvataggio file']);
} 