<?php
// Start session if you want to show login state in the header
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Study Group Finder - Team Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include __DIR__ . '/header.php'; ?>

    <main>
        <h1>Team Information</h1>

        <p><strong>Team Number: </strong> 9</p>
        <p>Section 104 (CRN:15556):</p>

        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Student ID</th>
                    <th>Copy</th>
                    <th>AI Declarations</th>
                    <th>Video</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Rachel Bryant</td>
                    <td>100827445</td>
                    <td><a href="https://f827445.gblearn.com/comp1230/assignments/project/" target="_blank">Visit Copy</a></td>
                    <td><a href="aud-phase1.pdf" target="_blank">Phase 1 PDF</a></td>
                    <td>Phase 1</td>
                </tr>
                <tr>
                    <td>Kay Brooks</td>
                    <td>101641010</td>
                    <td><a href="https://f6641010.gblearn.com/comp1230/assignments/project/" target="_blank">Visit Copy</a></td>
                    <td><a href="aud-phase1.pdf" target="_blank">Phase 1 PDF</a></td>
                    <td>Phase 1</td>
                </tr>
                <tr>
                    <td>Kyle Ramharack</td>
                    <td>101462870</td>
                    <td><a href="https://f462870.gblearn.com/comp1230/assignments/project/" target="_blank">Visit Copy</a></td>
                    <td><a href="aud-phase1.pdf" target="_blank">Phase 1 PDF</a></td>
                    <td>Phase 1</td>
                </tr>
            </tbody>
        </table>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>
</body>

</html>