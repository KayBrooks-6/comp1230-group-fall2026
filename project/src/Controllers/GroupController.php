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
        $group = $this->groups->findWithOwnerAndCounts($id);
        if (!$group) {
            notFound('Group not found.');
        }

        $members = $this->groups->members($id);
        $currentUserId = isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
        $isOwner = $currentUserId !== null && (int) $group['owner_id'] === $currentUserId;
        $isMember = $currentUserId !== null && $this->groups->isMember($id, $currentUserId);
        $placesLeft = max(0, (int) $group['places_left']);

        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view->show('groups/show', [
            'title' => $group['title'] . ' - Study Group',
            'group' => $group,
            'members' => $members,
            'currentUserId' => $currentUserId,
            'isOwner' => $isOwner,
            'isMember' => $isMember,
            'placesLeft' => $placesLeft,
            'success' => $success,
            'error' => $error,
        ]);
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
        if (!isset($_SESSION['user']['id'])) {
            redirect('/login');
        }

        $group = $this->groups->findWithOwnerAndCounts($id);
        if (!$group) {
            notFound('Group not found.');
        }

        $userId = (int) $_SESSION['user']['id'];
        $placesLeft = (int) $group['places_left'];

        if ($this->groups->isMember($id, $userId)) {
            redirect('/groups/' . $id);
        }

        if ($placesLeft <= 0) {
            $_SESSION['error'] = 'This study group is full.';
            redirect('/groups/' . $id);
        }

        $this->groups->addMember($id, $userId);
        $_SESSION['success'] = 'You joined the study group.';
        redirect('/groups/' . $id);
    }

    // POST /groups/{id}/leave
    public function leave(int $id): void
    {
        if (!isset($_SESSION['user']['id'])) {
            redirect('/login');
        }

        $group = $this->groups->findWithOwnerAndCounts($id);
        if (!$group) {
            notFound('Group not found.');
        }

        $userId = (int) $_SESSION['user']['id'];
        if ((int) $group['owner_id'] !== $userId && $this->groups->isMember($id, $userId)) {
            $this->groups->removeMember($id, $userId);
            $_SESSION['success'] = 'You left the study group.';
        }

        redirect('/groups/' . $id);
    }

    // GET /groups/{id}/members.csv
    public function membersCsv(int $id): void
    {
        if (!isset($_SESSION['user']['id'])) {
            redirect('/login');
        }

        $group = $this->groups->findWithOwnerAndCounts($id);
        if (!$group) {
            notFound('Group not found.');
        }

        $userId = (int) $_SESSION['user']['id'];
        if ((int) $group['owner_id'] !== $userId) {
            http_response_code(403);
            exit('You can only change your own study groups.');
        }

        $rows = $this->groups->membersWithJoinedAt($id);

        http_response_code(200);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="group-' . $id . '-members.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['display_name', 'joined_at']);

        foreach ($rows as $row) {
            fputcsv($out, [
                (string) $row['display_name'],
                (string) $row['joined_at'],
            ]);
        }

        fclose($out);
        exit;
    }

    // GET /my-groups
    public function myGroups(): void
    {
        // TODO: require login (redirect to /login), fetch owned & joined groups, render 'groups/my-groups'
    }

    // GET /api/groups/upcoming
    public function upcomingJson(): void
    {
        $rows = $this->groups->upcoming();
        $data = [];

        foreach ($rows as $row) {
            $data[] = [
                'id' => (int) $row['id'],
                'course_code' => (string) $row['course_code'],
                'title' => (string) $row['title'],
                'meets_at' => (string) $row['meets_at'],
                'location' => (string) $row['location'],
                'capacity' => (int) $row['capacity'],
                'places_left' => max(0, (int) $row['places_left']),
                'owner' => (string) $row['owner'],
            ];
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        exit;
    }
}
