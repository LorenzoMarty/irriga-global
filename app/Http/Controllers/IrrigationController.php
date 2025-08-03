<?php
class IrrigationController {
    private $file = __DIR__ . '/../../storage/data/irrigations.json';
    private $pivotFile = __DIR__ . '/../../storage/data/pivots.json';

    public function getAll($userId) {
        $irrigations = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        $filtered = array_filter($irrigations, fn($i) => $i['userId'] === $userId);
        echo json_encode(array_values($filtered));
    }

    public function getOne($id, $userId) {
        $irrigations = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        foreach ($irrigations as $i) {
            if ($i['id'] === $id && $i['userId'] === $userId) {
                echo json_encode($i);
                return;
            }
        }
        http_response_code(404);
        echo json_encode(['message' => 'Irrigation not found']);
    }

    public function create($data, $userId) {
        if (!isset($data['pivotId'], $data['applicationAmount'], $data['irrigationDate'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Missing fields']);
            return;
        }

        $pivots = file_exists($this->pivotFile) ? json_decode(file_get_contents($this->pivotFile), true) : [];
        $validPivot = array_filter($pivots, fn($p) => $p['id'] === $data['pivotId'] && $p['userId'] === $userId);
        if (!$validPivot) {
            http_response_code(403);
            echo json_encode(['message' => 'Unauthorized pivotId']);
            return;
        }

        $irrigation = [
            'id' => uniqid(),
            'pivotId' => $data['pivotId'],
            'applicationAmount' => (float)$data['applicationAmount'],
            'irrigationDate' => $data['irrigationDate'],
            'userId' => $userId
        ];
        $list = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        $list[] = $irrigation;
        file_put_contents($this->file, json_encode($list));
        http_response_code(201);
        echo json_encode(['message' => 'Irrigation created', 'irrigation' => $irrigation]);
    }

    public function delete($id, $userId) {
        $list = file_exists($this->file) ? json_decode(file_get_contents($this->file), true) : [];
        $newList = array_filter($list, fn($i) => $i['id'] !== $id || $i['userId'] !== $userId);
        file_put_contents($this->file, json_encode(array_values($newList)));
        echo json_encode(['message' => 'Irrigation deleted']);
    }
}
