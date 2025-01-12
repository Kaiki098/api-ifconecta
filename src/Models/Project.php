<?php

namespace App\Models;

use App\Models\Database;
use PDOException;
use PDO;
use Exception;

class Project extends Database
{
    public static function exists(string $title, string $description): bool
    {
        $pdo = self::getConnection();
        $sql = "SELECT COUNT(*) FROM project WHERE title = :title AND description = :description";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':title' => $title, ':description' => $description]);
        return $stmt->fetchColumn() > 0;
    }

    public static function save(array $data)
    {
        $pdo = self::getConnection();
        try {
            $pdo->beginTransaction();
            // Insert into project table
            $sqlProject = "INSERT INTO project (title, description) VALUES (:title, :description)";
            $stmtProject = $pdo->prepare($sqlProject);
            $stmtProject->execute([
                ':title' => $data['title'],
                ':description' => $data['description']
            ]);
            $projectId = $pdo->lastInsertId();

            // Insert into ThematicArea table
            $sqlThematicArea = "INSERT INTO thematic_area (name, project_id) VALUES (:name, :project_id)";
            $stmtThematicArea = $pdo->prepare($sqlThematicArea);
            foreach ($data['thematicAreas'] as $thematicArea) {
                $stmtThematicArea->execute([
                    ':name' => $thematicArea,
                    ':project_id' => $projectId
                ]);
            }

            // Insert into Beneficiary table
            $sqlBeneficiary = "INSERT INTO beneficiary (group_type, name, project_id) VALUES (:group_type, :name, :project_id)";
            $stmtBeneficiary = $pdo->prepare($sqlBeneficiary);
            $stmtBeneficiary->execute([
                ':group_type' => $data['beneficiary']['group'],
                ':name' => $data['beneficiary']['name'],
                ':project_id' => $projectId
            ]);
            $beneficiaryId = $pdo->lastInsertId();

            // Insert into Contact table
            $sqlContact = "INSERT INTO contact (email, phone, only_whatsapp, beneficiary_id) VALUES (:email, :phone, :only_whatsapp, :beneficiary_id)";
            $stmtContact = $pdo->prepare($sqlContact);
            $stmtContact->execute([
                ':email' => $data['beneficiary']['contact']['email'],
                ':phone' => $data['beneficiary']['contact']['phone'],
                ':only_whatsapp' => $data['beneficiary']['contact']['onlyWhatsapp'] ? true : false,
                ':beneficiary_id' => $beneficiaryId
            ]);

            $pdo->commit();

            return $projectId > 0 ? true : false;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw new Exception("Failed to save project: " . $e->getMessage());
        }
    }

    public static function fetchAll()
    {
        $pdo = self::getConnection();
        $sql = "
            SELECT 
                p.id AS project_id,
                p.title,
                p.description,
                ta.name AS thematic_area,
                b.group_type AS beneficiary_group,
                b.name AS beneficiary_name,
                c.email AS contact_email,
                c.phone AS contact_phone,
                c.only_whatsapp
            FROM 
                project p
            LEFT JOIN thematic_area ta ON p.id = ta.project_id
            LEFT JOIN beneficiary b ON p.id = b.project_id
            LEFT JOIN contact c ON b.id = c.beneficiary_id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $projects = [];
        foreach ($results as $row) {
            $projectId = $row['project_id'];
            if (!isset($projects[$projectId])) {
                $projects[$projectId] = [
                    'id' => $projectId,
                    'title' => $row['title'],
                    'description' => $row['description'],
                    'thematicAreas' => [],
                    'beneficiary' => [
                        'group' => $row['beneficiary_group'],
                        'name' => $row['beneficiary_name'],
                        'contact' => [
                            'email' => $row['contact_email'],
                            'phone' => $row['contact_phone'],
                            'onlyWhatsapp' => $row['only_whatsapp'] ? true : false,
                        ]
                    ]
                ];
            }
            $projects[$projectId]['thematicAreas'][] = $row['thematic_area'];
        }

        return array_values($projects);
    }
}