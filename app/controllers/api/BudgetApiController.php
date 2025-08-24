<?php

class BudgetApiController {
    public function search() {
        header('Content-Type: application/json');

        try {
            $query = $_GET['q'] ?? '';

            if (empty($query)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing search query parameter.'
                ]);
                return;
            }

            $budgetModel = new Budget();
            $results = $budgetModel->search($query);

            if (empty($results)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No records found for the given query.'
                ]);
                return;
            }

            echo json_encode([
                'status' => 'success',
                'data' => $results
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function addBudget() {
        header('Content-Type: application/json');

        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (empty($input['sport_id']) || empty($input['year']) || empty($input['allocated_amount']) || empty($input['description'])) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'All fields are required: sport_id, year, allocated_amount, description.'
                ]);
                return;
            }

            $budgetModel = new Budget();
            $budgetId = $budgetModel->addBudget(
                $input['sport_id'],
                $input['year'],
                $input['allocated_amount'],
                $input['description']
            );

            echo json_encode([
                'status' => 'success',
                'message' => 'Budget added successfully.',
                'budget_id' => $budgetId
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
    }
}
