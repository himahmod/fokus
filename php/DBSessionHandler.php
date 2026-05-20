<?php
declare(strict_types=1);

class DBSessionHandler implements SessionHandlerInterface
{
    private ?PDO $db = null;

    public function open(string $path, string $name): bool
    {
        try {
            $this->db = getConnexion();
            $this->db->exec(
                "CREATE TABLE IF NOT EXISTS sessions (
                    id      VARCHAR(128) PRIMARY KEY,
                    data    TEXT         NOT NULL DEFAULT '',
                    expires TIMESTAMPTZ  NOT NULL
                )"
            );
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        if (!$this->db) return '';
        try {
            $stmt = $this->db->prepare(
                "SELECT data FROM sessions WHERE id = :id AND expires > NOW()"
            );
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ? ($row['DATA'] ?? $row['data'] ?? '') : '';
        } catch (Throwable $e) {
            return '';
        }
    }

    public function write(string $id, string $data): bool
    {
        if (!$this->db) return false;
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO sessions (id, data, expires)
                 VALUES (:id, :data, NOW() + INTERVAL '24 hours')
                 ON CONFLICT (id) DO UPDATE
                 SET data = EXCLUDED.data, expires = NOW() + INTERVAL '24 hours'"
            );
            $stmt->execute([':id' => $id, ':data' => $data]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function destroy(string $id): bool
    {
        if (!$this->db) return false;
        try {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = :id");
            $stmt->execute([':id' => $id]);
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function gc(int $maxlifetime): int|false
    {
        if (!$this->db) return 0;
        try {
            $stmt = $this->db->prepare("DELETE FROM sessions WHERE expires < NOW()");
            $stmt->execute();
            return $stmt->rowCount();
        } catch (Throwable $e) {
            return 0;
        }
    }
}
