<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense - SmartSplit</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/ui.js" defer></script>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="nav-brand">💸 SmartSplit</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="transactions.php">Transactions</a>
        </div>
    </nav>

    <div class="container centered-container" style="align-items: flex-start; margin-top: 3rem;">
        <div class="card card-sm fade-in" style="margin: 0 auto;">
            <h2>Add New Expense</h2>
            <p>Record a new expense and easily split the cost.</p>

            <form action="dashboard.php" method="GET">
                <div class="form-group">
                    <label for="description">What was this for?</label>
                    <input type="text" id="description" name="description" placeholder="E.g. Dinner, Taxi, Tickets" required>
                </div>

                <div class="form-group">
                    <label for="amount">Total Amount</label>
                    <div style="position: relative;">
                        <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-weight: 600; color: var(--text-muted);">$</span>
                        <input type="number" id="amount" name="amount" placeholder="0.00" step="0.01" min="0.01" style="padding-left: 2rem;" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="paid_by">Who Paid?</label>
                    <select id="paid_by" name="paid_by" required>
                        <option value="you">You</option>
                        <option value="alice">Alice</option>
                        <option value="bob">Bob</option>
                        <option value="charlie">Charlie</option>
                    </select>
                </div>

                <div class="form-group" style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <label style="margin-bottom: 1rem;">Split Between</label>
                    <div class="checkbox-list">
                        <label class="checkbox-item">
                            <input type="checkbox" name="split[]" value="you" checked> You
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="split[]" value="alice" checked> Alice
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="split[]" value="bob" checked> Bob
                        </label>
                        <label class="checkbox-item">
                            <input type="checkbox" name="split[]" value="charlie" checked> Charlie
                        </label>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <a href="dashboard.php" class="btn btn-outline" style="flex: 1;">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="flex: 2;">Save Expense</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
