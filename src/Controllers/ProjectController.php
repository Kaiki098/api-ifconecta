<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\ProjectService;

class ProjectController 
{
    public function index(Request $request, Response $response) 
    {
        $authorization = $request::authorization();

        $projectService = ProjectService::getAllProjects($authorization);

        if (isset($projectService['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $projectService['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'data' => $projectService
        ], 200);
    }

    public function store(Request $request, Response $response)
    {
        $body = $request::body();

        $projectService = ProjectService::create($body);

        if(isset($projectService['error'])) {
            return $response::json([
                'error' => true,
                'success' => false,
                'message' => $projectService['error']
            ], 400);
        }

        $response::json([
            'error' => false,
            'success' => true,
            'data' => $projectService
        ], 201);
    }
}