<?php

require_once __DIR__ . '/../models/Student.php';

class StudentController
{
    private Student $student;

    public function __construct()
    {
        $this->student = new Student();
    }

    public function index(): void
    {
        $students = $this->student->getAll();
        $this->sendJson($students);
    }

    public function show(int $id): void
    {
        $student = $this->student->getById($id);
        if (!$student) {
            $this->sendJson(['error' => 'Étudiant introuvable'], 404);
            return;
        }
        $this->sendJson($student);
    }

    public function store(array $data): void
    {
        if (!$this->validate($data)) {
            $this->sendJson(['error' => 'Données invalides ou incomplètes'], 422);
            return;
        }
        $id = $this->student->create($data);
        $student = $this->student->getById($id);
        $this->sendJson($student, 201);
    }

    public function update(int $id, array $data): void
    {
        if (!$this->validate($data)) {
            $this->sendJson(['error' => 'Données invalides ou incomplètes'], 422);
            return;
        }
        if (!$this->student->getById($id)) {
            $this->sendJson(['error' => 'Étudiant introuvable'], 404);
            return;
        }
        $this->student->update($id, $data);
        $this->sendJson($this->student->getById($id));
    }

    public function delete(int $id): void
    {
        if (!$this->student->getById($id)) {
            $this->sendJson(['error' => 'Étudiant introuvable'], 404);
            return;
        }
        $this->student->delete($id);
        $this->sendJson(['message' => 'Étudiant supprimé']);
    }

    private function validate(array $data): bool
    {
        return isset($data['first_name'], $data['last_name'], $data['email'])
            && filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }

    private function sendJson($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
