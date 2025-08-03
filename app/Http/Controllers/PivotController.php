<?php
class PivotController {
    private $file = __DIR__ . '/../../storage/data/pivots.json';

    public function getAll($userId) {
        $pivots = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        $userPivots = array_filter($pivots, fn($p) => $p['userId'] === $userId);
        echo json_encode(array_values($userPivots));
    }

    public function getOne($id, $userId) {
        $pivots = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        foreach ($pivots as $pivot) {
            if ($pivot['id'] === $id && $pivot['userId'] === $userId) {
                echo json_encode($pivot);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['message' => 'Pivot not found']);
    }

    public function create($data, $userId) {
        if (!isset($data['description'], $data['flowRate'], $data['minApplicationDepth'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing fields']);
            return;
        }

        $pivot = [
            'id' => uniqid(),
            'description' => $data['description'],
            'flowRate' => (float)$data['flowRate'],
            'minApplicationDepth' => (float)$data['minApplicationDepth'],
            'userId' => $userId
        ];
        $pivots = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        $pivots[] = $pivot;
        file_put_contents($this->file, json_encode($pivots));
        http_response_code(201);
        echo json_encode(['message' => 'Pivot created', 'pivot' => $pivot]);
    }

    public function delete($id, $userId) {
        $pivots = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        $newList = array_filter($pivots, fn($p) => $p['id'] !== $id || $p['userId'] !== $userId);
        file_put_contents($this->file, json_encode(array_values($newList)));
        echo json_encode(['message' => 'Pivot deleted']);
    }
}
