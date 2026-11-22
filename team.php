<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Group Finder - Team Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php include __DIR__ . '/header.php'; ?>

    <main class="container py-4">
        <section class="card shadow-sm mb-4">
            <header class="card-header bg-white">
                <h1 class="h3 mb-0">Team Information</h1>
            </header>
            <section class="card-body">
                <p class="mb-2"><strong>Team Number:</strong> 9</p>
                <p class="mb-0 text-secondary"><strong>Section:</strong> 104 (CRN: 15556)</p>
            </section>
        </section>

        <section class="card shadow-sm">
            <header class="card-header bg-white">
                <h2 class="h5 mb-0">Team Members &amp; Deliverables</h2>
            </header>
            <section class="card-body p-0">
                <section class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Member</th>
                                <th scope="col">Student ID</th>
                                <th scope="col">Copy</th>
                                <th scope="col">AI Declarations</th>
                                <th scope="col">Video</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row" class="fw-semibold">Rachel Bryant</th>
                                <td>100827445</td>
                                <td><a href="https://f6827445.gblearn.com/comp1230/assignments/project/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">Visit Copy</a></td>
                                <td>
                                    <a href="aud-phase1.pdf" target="_blank" rel="noopener noreferrer" class="me-2">Phase 1 PDF</a>
                                    <a href="aud-phase2.pdf" target="_blank" rel="noopener noreferrer">Phase 2 PDF</a>
                                </td>
                                <td>Phase 1</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-semibold">Kay Brooks</th>
                                <td>101641010</td>
                                <td><a href="https://f6641010.gblearn.com/comp1230/assignments/project/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">Visit Copy</a></td>
                                <td>
                                    <a href="aud-phase1.pdf" target="_blank" rel="noopener noreferrer" class="me-2">Phase 1 PDF</a>
                                    <a href="aud-phase2.pdf" target="_blank" rel="noopener noreferrer">Phase 2 PDF</a>
                                </td>
                                <td>Phase 1</td>
                            </tr>
                            <tr>
                                <th scope="row" class="fw-semibold">Kyle Ramharack</th>
                                <td>101462870</td>
                                <td><a href="https://f6462870.gblearn.com/comp1230/assignments/project/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">Visit Copy</a></td>
                                <td>
                                    <a href="aud-phase1.pdf" target="_blank" rel="noopener noreferrer" class="me-2">Phase 1 PDF</a>
                                    <a href="aud-phase2.pdf" target="_blank" rel="noopener noreferrer">Phase 2 PDF</a>
                                </td>
                                <td>Phase 1</td>
                            </tr>
                        </tbody>
                    </table>
                </section>
            </section>
        </section>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>