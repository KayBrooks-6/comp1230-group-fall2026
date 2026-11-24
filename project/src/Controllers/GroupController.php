<?php
// src/Controllers/GroupController.php

namespace App\Controllers;

use App\Models\GroupModel;
use App\View;

class GroupController
{
    public function __construct(
        private GroupModel $groups,
        private View $view
    ) {}

    // GET /
    public function index(): void
    {
        // TODO: fetch groups with places_left via $this->groups and render 'groups/index'
    }

    // GET /groups/add
    public function createForm(): void
    {
        // TODO: require login (redirect to /login), render create form view
    }

    // POST /groups/add
    public function create(): void
    {
        // TODO: require login, validate input, insert group + owner membership, PRG redirect to /
    }

    // GET /groups/{id}
    public function show(int $id): void
    {
        // TODO: find group + members (404 if missing), render 'groups/show'
    }

    // GET /groups/{id}/edit
    public function editForm(int $id): void
    {
        // TODO: require login, find group (404 if missing), check owner (403 if not owner), render edit form
    }

    // POST /groups/{id}/edit
    public function update(int $id): void
    {
        // TODO: require login, find group (404 if missing), check owner (403 if not owner), validate & update, redirect to /
    }

    // POST /groups/{id}/delete
    public function delete(int $id): void
    {
        // TODO: require login, find group (404 if missing), check owner (403 if not owner), delete group, redirect to /
    }

    // POST /groups/{id}/join
    public function join(int $id): void
    {
        // TODO: require login (redirect to /login), find group (404 if missing), check full/membership rules, insert membership, redirect to /groups/{id}
    }

    // POST /groups/{id}/leave
    public function leave(int $id): void
    {
        // TODO: require login (redirect to /login), find group (404 if missing), check owner/membership rules, delete membership, redirect to /groups/{id}
    }

    // GET /groups/{id}/members.csv
    public function membersCsv(int $id): void
    {
        // TODO: require login (redirect to /login), find group (404 if missing), check owner (403 if not owner), stream CSV via fputcsv()
    }

    // GET /my-groups
    public function myGroups(): void
    {
        // TODO: require login (redirect to /login), fetch owned & joined groups, render 'groups/my-groups'
    }

    // GET /api/groups/upcoming
    public function upcomingJson(): void
    {
        // TODO: fetch upcoming groups via $this->groups, output Content-Type: application/json and json_encode(['data' => ...])
    }
}