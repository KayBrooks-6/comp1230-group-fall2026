<article class="card shadow-sm mb-4">
    <header class="card-header bg-white d-flex justify-content-between align-items-start gap-2">
        <section>
            <h1 class="h3 mb-1"><?= e($group['title']) ?></h1>
            <p class="mb-0 text-secondary"><strong>Course:</strong> <?= e($group['course_code']) ?></p>
        </section>
        <?php if ($isOwner): ?>
            <a href="<?= e(url('/groups/' . (int) $group['id'] . '/members.csv')) ?>" class="btn btn-sm btn-outline-secondary">Download members (CSV)</a>
        <?php endif; ?>
    </header>

    <section class="card-body">
        <p class="mb-3"><?= e($group['description']) ?></p>

        <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item px-0"><strong>Owner:</strong> <?= e($group['owner_name']) ?></li>
            <li class="list-group-item px-0"><strong>Time:</strong> <?= e($group['meets_at']) ?></li>
            <li class="list-group-item px-0"><strong>Place:</strong> <?= e($group['location']) ?></li>
            <li class="list-group-item px-0"><strong>Capacity:</strong> <?= e((string) $group['capacity']) ?></li>
            <li class="list-group-item px-0"><strong>Places left:</strong> <?= e((string) $placesLeft) ?></li>
        </ul>

        <section aria-labelledby="members-heading" class="mb-4">
            <h2 id="members-heading" class="h5">Members List</h2>
            <?php if (empty($members)): ?>
                <p class="text-secondary mb-0">No members</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($members as $member): ?>
                        <li class="list-group-item"><?= e($member['display_name']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <?php if (!empty($success)): ?>
            <p class="alert alert-success" role="status"><?= e($success) ?></p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <p class="error alert alert-danger" role="alert"><?= e($error) ?></p>
        <?php endif; ?>

        <section aria-label="Membership actions">
            <?php if ($currentUserId !== null): ?>
                <?php if ($isOwner): ?>
                    <p class="mb-0 text-secondary"><strong>Owner of the group</strong></p>
                <?php elseif ($isMember): ?>
                    <form action="<?= e(url('/groups/' . (int) $group['id'] . '/leave')) ?>" method="POST" class="d-inline">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-warning">Leave</button>
                    </form>
                <?php elseif ($placesLeft > 0): ?>
                    <form action="<?= e(url('/groups/' . (int) $group['id'] . '/join')) ?>" method="POST" class="d-inline">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-success">Join</button>
                    </form>
                <?php else: ?>
                    <p class="error alert alert-danger mb-0" role="alert">This study group is full.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="mb-0"><a href="<?= e(url('/login')) ?>" class="btn btn-primary btn-sm">Log in</a></p>
            <?php endif; ?>
        </section>
    </section>
</article>