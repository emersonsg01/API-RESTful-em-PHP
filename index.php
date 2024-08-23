<?php
require 'ParcelGenerator.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if (!isset($data['valor_total'], $data['qtd_parcelas'], $data['data_primeiro_vencimento'], $data['periodicidade'])) {
        echo json_encode(['error' => 'Parâmetros insuficientes']);
        exit;
    }

    $valor_total = $data['valor_total'];
    $qtd_parcelas = $data['qtd_parcelas'];
    $data_primeiro_vencimento = $data['data_primeiro_vencimento'];
    $periodicidade = $data['periodicidade'];
    $valor_entrada = isset($data['valor_entrada']) ? $data['valor_entrada'] : 0;

    $parcelGenerator = new ParcelGenerator($valor_total, $qtd_parcelas, $data_primeiro_vencimento, $periodicidade, $valor_entrada);
    $response = $parcelGenerator->generate();

    // Salvar o carnê no arquivo JSON
    $carnes = json_decode(file_get_contents('data.json'), true);
    $id = count($carnes) + 1;
    $carnes[$id] = $response;
    file_put_contents('data.json', json_encode($carnes));

    echo json_encode(['id' => $id, 'carnê' => $response]);
} elseif ($method === 'GET') {
    if (!isset($_GET['id'])) {
        echo json_encode(['error' => 'Parâmetro id é necessário']);
        exit;
    }

    $id = $_GET['id'];
    $carnes = json_decode(file_get_contents('data.json'), true);

    if (!isset($carnes[$id])) {
        echo json_encode(['error' => 'Carnê não encontrado']);
        exit;
    }

    echo json_encode($carnes[$id]['parcelas']);
}
?>