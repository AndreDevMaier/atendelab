<?php

class TiposAtendimentos
{
    private PDO $pdo;

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function __construct()
    {

        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function buscarAtendimento(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->jsonResponse(['erro' => 'ID invalido.'], 400);
            return;
        }

        $sql = 'SELECT id, nome, descricao, status
                FROM tipos_atendimentos
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $tipos_atendimentos = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tipos_atendimentos) {
            $this->jsonResponse(['erro' => 'Tipo de atendimentos não encontrado.'], 404);
            return;
        }
        $this->jsonResponse($tipos_atendimentos, 200);
    }

    public function criarTipoAtendimento(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '') {
            $this->jsonResponse(['erro' => 'Nome do atendimento é obrigatorio'], 400);
            return;
        }

        try {
            $sql = 'INSERT INTO tipos_atendimentos(nome, descricao, status)
                    VALUES (:nome, :descricao, :status)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            $this->jsonResponse(['mensagem' => 'Tipo de Atendimento cadastrado com sucesso.'], 201);
        } catch (PDOException $e) {
            $this->jsonResponse(['erro' => 'Erro ao cadastrar Tipo de Atendimento'], 500);
            return;
        }
    }

     public function listarTipoAtendimento(): void
    {
        $sql = 'SELECT id, nome, descricao, status
                FROM tipos_atendimentos
                ORDER BY id DESC';

        $stmt = $this->pdo->query($sql);
        $tipos_atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->jsonResponse($tipos_atendimentos);
    }

    public function atualizarAtendimento(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $status = $_POST['status'] ?? 'ativo';

        if (!$id) {
            $this->jsonResponse(['erro' => 'ID é obrigatorio.'], 400);
            return;
        }
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $this->jsonResponse(['erro' => 'Status invalido.'], 400);
            return;
        }

        try {
            $sql = 'UPDATE tipos_atendimentos
                    SET nome = :nome,
                        descricao = :descricao,
                        status = :status
                    WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->jsonResponse(['mensagem' => 'Tipo de atendimento atualizado com sucesso']);
        } catch (PDOException $e) {
            $this->jsonResponse(['erro' => 'Erro ao atualizar tipo de atendimento.'], 500);
            return;
        }
    }

    public function excluirAtendimento(): void
    {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $this->jsonResponse(['erro' => 'ID inválido'], 400);
            return;
        }

        try {
            $sql = 'DELETE FROM tipos_atendimentos WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $this->jsonResponse(['mensagem' => 'Tipo de atendimento excluido com sucesso']);
        } catch (PDOException $e) {
            $this->jsonResponse(['erro' => 'Erro ao deletar tipo de atendimento'], 500);
            return;
        }
    }
}