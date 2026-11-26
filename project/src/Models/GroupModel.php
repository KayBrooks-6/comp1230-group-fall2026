<?php
// src/Models/GroupModel.php

namespace App\Models;

use PDO;

class GroupModel
{
    public function __construct(private PDO $pdo) {}

    public function allWithPlacesLeft(): array
    {
        // TODO: SELECT study_groups + owner display_name + LEFT JOIN memberships + COUNT + GROUP BY
        return [];
    }

    public function findWithOwnerAndCounts(int $groupId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                study_groups.*,
                users.display_name AS owner_name,
                COUNT(memberships.user_id) AS member_count,
                (study_groups.capacity - COUNT(memberships.user_id)) AS places_left
            FROM study_groups
            LEFT JOIN users ON study_groups.owner_id = users.id
            LEFT JOIN memberships ON study_groups.id = memberships.group_id
            WHERE study_groups.id = ?
            GROUP BY study_groups.id, users.display_name
        ");
        $stmt->execute([$groupId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function members(int $groupId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT users.display_name
            FROM memberships
            JOIN users ON memberships.user_id = users.id
            WHERE memberships.group_id = ?
            ORDER BY users.display_name ASC
        ");
        $stmt->execute([$groupId]);

        return $stmt->fetchAll();
    }

    public function membersWithJoinedAt(int $groupId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT users.display_name, memberships.joined_at
            FROM memberships
            JOIN users ON memberships.user_id = users.id
            WHERE memberships.group_id = ?
            ORDER BY memberships.joined_at ASC, users.display_name ASC
        ");
        $stmt->execute([$groupId]);

        return $stmt->fetchAll();
    }

    public function isMember(int $groupId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            SELECT 1
            FROM memberships
            WHERE group_id = ? AND user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$groupId, $userId]);

        return (bool) $stmt->fetchColumn();
    }

    public function validate(array $input): array
    {
        // TODO: validate course_code, title, description, meets_at, location, capacity; return array of errors ([] if valid)
        return [];
    }

    public function create(int $ownerId, array $data): int
    {
        // TODO: INSERT into study_groups + INSERT owner into memberships; return new group id
        return 0;
    }

    public function update(int $groupId, int $ownerId, array $data): bool
    {
        // TODO: UPDATE study_groups WHERE id = ? AND owner_id = ?
        return false;
    }

    public function delete(int $groupId, int $ownerId): bool
    {
        // TODO: DELETE from study_groups WHERE id = ? AND owner_id = ?
        return false;
    }

    public function addMember(int $groupId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO memberships (user_id, group_id)
            VALUES (?, ?)
        ");

        return $stmt->execute([$userId, $groupId]);
    }

    public function removeMember(int $groupId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("
            DELETE FROM memberships
            WHERE group_id = ? AND user_id = ?
        ");

        return $stmt->execute([$groupId, $userId]);
    }

    public function upcoming(): array
    {
        $stmt = $this->pdo->query("
            SELECT
                study_groups.id,
                study_groups.course_code,
                study_groups.title,
                study_groups.meets_at,
                study_groups.location,
                study_groups.capacity,
                (study_groups.capacity - COUNT(memberships.user_id)) AS places_left,
                users.display_name AS owner
            FROM study_groups
            JOIN users ON study_groups.owner_id = users.id
            LEFT JOIN memberships ON study_groups.id = memberships.group_id
            WHERE study_groups.meets_at > NOW()
            GROUP BY
                study_groups.id,
                study_groups.course_code,
                study_groups.title,
                study_groups.meets_at,
                study_groups.location,
                study_groups.capacity,
                users.display_name
            ORDER BY study_groups.meets_at ASC
        ");

        return $stmt->fetchAll();
    }
}
