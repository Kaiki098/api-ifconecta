<?php

namespace App\Services;

use App\Http\JWT;
use App\Models\Project;
use App\Utils\Validator;
use Exception;
use PDOException;

class ProjectService
{
    public static function getAllProjects(mixed $authorization)
    {
        try {
            if (isset($authorization['error'])) {
                return ['error' => $authorization['error']];
            }

            $userFromJWT = JWT::verify($authorization);

            if (!$userFromJWT) return ['error' => "Please, login to access this resource."];

            $projects = Project::fetchAll();

            return $projects;
        } catch (PDOException $e) {
            if ($e->errorInfo[0] == 'HY000')
                return ['error' => 'Sorry, we could not connect to the database.'];
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public static function create(array $data)
    {
        try {
            // Verifica se todos os campos estão preenchidos
            $fields = Validator::validate([
                'title' => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
                'thematicAreas' => $data['thematicAreas'] ?? '',
                'beneficiary' => [
                    'group' => $data['beneficiary']['group'] ?? '',
                    'name' => $data['beneficiary']['name'] ?? '',
                    'contact' => [
                        'email' => $data['beneficiary']['contact']['email'] ?? '',
                        'phone' => $data['beneficiary']['contact']['phone'] ?? '',
                        'onlyWhatsapp' => $data['beneficiary']['contact']['onlyWhatsapp'] ?? '',
                    ] ?? '',
                ] ?? '',
            ]);

            // Check for duplicate project
            if (Project::exists($fields['title'], $fields['description'])) {
                return ['error' => 'A project with the same title and description already exists.'];
            }

            $project = Project::save($fields);

            if (!$project) {
                return ['error' => 'Sorry, we could not create your project.'];
            }

            return "Project created succesfully!";

        } catch (PDOException $e) {
            if ($e->errorInfo[0] == 'HY000')
                return ['error' => 'Sorry, we could not connect to the database.'];
            return ['error' => $e->getMessage()];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }

    }
}