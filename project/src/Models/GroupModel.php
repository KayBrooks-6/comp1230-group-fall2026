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
        // TODO: prepared SELECT for a single group with owner name and places_left, or null if not found
        return null;
    }

    public function members(int $groupId): array
    {
        // TODO: prepared SELECT of member display_names for a group
        return [];
    }

    public function membersWithJoinedAt(int $groupId): array
    {
        // TODO: prepared SELECT of display_name, joined_at ordered by earliest joined_at first (for CSV)
        return [];
    }

    public function isMember(int $groupId, int $userId): bool
    {
        // TODO: prepared SELECT check whether $userId is in memberships for $groupId
        return false;
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
        // TODO: INSERT into memberships (user_id, group_id)
        return false;
    }

    public function removeMember(int $groupId, int $userId): bool
    {
        // TODO: DELETE from memberships WHERE group_id = ? AND user_id = ?
        return false;
    }

    public function upcoming(): array
    {
        // TODO: prepared SELECT with LEFT JOIN memberships, COUNT, GROUP BY, WHERE study_groups.meets_at > NOW(), ORDER BY study_groups.meets_at ASC
        return [];
    }
}