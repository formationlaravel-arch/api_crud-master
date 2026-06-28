<?php

require_once __DIR__ . '/../lib/Database.php';

class Student
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO students (first_name, last_name, email, created_at) VALUES (:first_name, :last_name, :email, NOW())'
        );
        $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT id, first_name, last_name, email, created_at FROM students');
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, first_name, last_name, email, created_at FROM students WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();
        return $student ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE students SET first_name = :first_name, last_name = :last_name, email = :email WHERE id = :id'
        );
        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':email' => $data['email'],
            ':id' => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM students WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
