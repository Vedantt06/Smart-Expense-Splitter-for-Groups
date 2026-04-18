<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SmartSplit</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/ui.js" defer></script>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="nav-brand">💸 SmartSplit</a>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="transactions.php">Transactions</a>
        </div>
    </nav>

    <div class="container fade-in">
        <div class="dash-header">
            <div>
                <p style="margin-bottom: 0.25rem; font-weight: 600; color: var(--primary);">Active Group</p>
                <h1 style="margin-bottom: 0;">Europe Trip 2026</h1>
            </div>
            <a href="add_expense.php" class="btn btn-primary">➕ New Expense</a>
        </div>

        <div class="dashboard-grid">
            <!-- Left Column: Balances -->
            <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                <div class="balance-card" style="margin: 0; border-radius: 16px 16px 0 0;">
                    <p style="margin-bottom: 0; color: rgba(255,255,255,0.8); font-weight: 500;">Total Group Expense</p>
                    <div class="balance-amount">$840.00</div>
                    <p style="margin-bottom: 0; font-size: 0.875rem; color: rgba(255,255,255,0.9);">You owe <strong>$45.00</strong> overall</p>
                </div>
                <div style="padding: 1.5rem; flex: 1;">
                    <h3 style="font-size: 1rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">Who owes who?</h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                            <span style="font-weight: 500;">You</span>
                            <span style="color: var(--text-muted);">owe</span>
                            <span style="font-weight: 600;">Alice</span>
                            <span class="amount-negative">$45.00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 500;">Bob</span>
                            <span style="color: var(--text-muted);">owes</span>
                            <span style="font-weight: 600;">You</span>
                            <span class="amount-positive">$20.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Members and Overview -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="card">
                    <h3 style="margin-bottom: 1.5rem;">Group Members</h3>
                    <div class="chips-container">
                        <span class="chip"><img src="https://ui-avatars.com/api/?name=You&background=random" alt="Avatar"> You</span>
                        <span class="chip"><img src="https://ui-avatars.com/api/?name=Alice&background=random" alt="Avatar"> Alice</span>
                        <span class="chip"><img src="https://ui-avatars.com/api/?name=Bob&background=random" alt="Avatar"> Bob</span>
                        <span class="chip"><img src="https://ui-avatars.com/api/?name=Charlie&background=random" alt="Avatar"> Charlie</span>
                    </div>
                </div>

                <div class="card" style="padding: 0; display: flex; flex-direction: column;">
                    <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0;">Recent Activity</h3>
                        <a href="transactions.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    <div class="table-container" style="border: none; border-radius: 0 0 16px 16px;">
                        <table>
                            <thead style="display: none;">
                                <tr><th>Desc</th><th>Amount</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div style="font-weight: 500;">Dinner at Luigi's</div>
                                        <div style="font-size: 0.85rem; color: var(--text-muted);">Paid by Alice</div>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="font-weight: 600;">$120.00</div>
                                        <div style="font-size: 0.85rem; color: var(--danger);">You borrowed $30.00</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div style="font-weight: 500;">Uber Ride</div>
                                        <div style="font-size: 0.85rem; color: var(--text-muted);">Paid by You</div>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="font-weight: 600;">$35.00</div>
                                        <div style="font-size: 0.85rem; color: var(--success);">You lent $26.25</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
