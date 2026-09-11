<?php
class UseConditionController extends Controller {
    public function index() {
        try {
            $this->view('usecondition', ['title' => "MILLE SABORDS - Condition d'utilisation"]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
