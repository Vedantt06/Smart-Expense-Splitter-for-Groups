<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group - SmartSplit</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/ui.js" defer></script>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="nav-brand">💸 SmartSplit</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
        </div>
    </nav>

    <div class="container centered-container">
        <div class="card card-sm fade-in">
            <h2>Create a New Group</h2>
            <p>Setup a new group and add members to get started.</p>

            <form action="dashboard.php" method="GET">
                <div class="form-group">
                    <label for="group_name">Group Name</label>
                    <input type="text" id="group_name" name="group_name" placeholder="E.g. Europe Trip 2026" required>
                </div>

                <div class="form-group">
                    <label>Members</label>
                    <div id="members-container" class="members-container">
                        <div class="member-field">
                            <input type="text" name="member[]" placeholder="Your Name" value="You" readonly style="background: #f8fafc;">
                        </div>
                        <div class="member-field">
                            <input type="text" name="member[]" placeholder="Member Name" required>
                        </div>
                    </div>
                    
                    <button type="button" id="add-member-btn" class="btn btn-outline btn-sm">+ Add Member</button>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 1rem; width: 100%;">Create Group & Continue</button>
            </form>
        </div>
    </div>

</body>
</html>
