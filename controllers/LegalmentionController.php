<?php
class LegalMentionController extends Controller {
    public function index() {
        try {
            $this->view('legalmention', ['title' => "MILLE SABORDS - Mention Légal"]);
        } catch (Throwable $e) {
            http_response_code(500);
            echo "View Rendering Error: " . htmlspecialchars($e->getMessage());
        }
    }
}
