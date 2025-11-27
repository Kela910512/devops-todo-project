<?php

class MetricsController {
    private $db;
    private $conn;

    public function __construct(Database $db) {
        $this->db = $db;
        $this->conn = $db->getConnection();
    }

    public function export() {
        header('Content-Type: text/plain; version=0.0.4');

        try {
            // Get total todos
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM todos");
            $totalTodos = $stmt->fetch()['count'];

            // Get active todos (not completed)
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM todos WHERE completed = 0");
            $activeTodos = $stmt->fetch()['count'];

            // Get completed todos
            $stmt = $this->conn->query("SELECT COUNT(*) as count FROM todos WHERE completed = 1");
            $completedTodos = $stmt->fetch()['count'];

            // Get todos by priority
            $stmt = $this->conn->query("SELECT priority, COUNT(*) as count FROM todos GROUP BY priority");
            $priorities = $stmt->fetchAll();

            // Output Prometheus metrics
            echo "# HELP total_todos Total number of todos\n";
            echo "# TYPE total_todos gauge\n";
            echo "total_todos $totalTodos\n\n";

            echo "# HELP active_todos Number of active (incomplete) todos\n";
            echo "# TYPE active_todos gauge\n";
            echo "active_todos $activeTodos\n\n";

            echo "# HELP completed_todos Number of completed todos\n";
            echo "# TYPE completed_todos gauge\n";
            echo "completed_todos $completedTodos\n\n";

            echo "# HELP todos_by_priority Number of todos by priority level\n";
            echo "# TYPE todos_by_priority gauge\n";
            foreach ($priorities as $priority) {
                $label = $priority['priority'];
                $count = $priority['count'];
                echo "todos_by_priority{priority=\"$label\"} $count\n";
            }
            echo "\n";

            // PHP metrics
            echo "# HELP php_memory_usage_bytes Current PHP memory usage in bytes\n";
            echo "# TYPE php_memory_usage_bytes gauge\n";
            echo "php_memory_usage_bytes " . memory_get_usage(true) . "\n\n";

            echo "# HELP php_memory_peak_bytes Peak PHP memory usage in bytes\n";
            echo "# TYPE php_memory_peak_bytes gauge\n";
            echo "php_memory_peak_bytes " . memory_get_peak_usage(true) . "\n\n";

            // HTTP request counter
            echo "# HELP http_requests_total Total HTTP requests\n";
            echo "# TYPE http_requests_total counter\n";
            echo "http_requests_total 1\n\n";

        } catch (PDOException $e) {
            http_response_code(500);
            echo "# Error collecting metrics: " . $e->getMessage() . "\n";
        }
    }
}
