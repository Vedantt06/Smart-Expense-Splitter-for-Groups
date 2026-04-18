<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - SmartSplit</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/ui.js" defer></script>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="nav-brand">💸 SmartSplit</a>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="transactions.php" class="active">Transactions</a>
        </div>
    </nav>

    <div class="container fade-in">
        <div class="dash-header">
            <div>
                <p style="margin-bottom: 0.25rem; font-weight: 600; color: var(--primary);">Europe Trip 2026</p>
                <h1 style="margin-bottom: 0;">Transaction History</h1>
            </div>
            <a href="add_expense.php" class="btn btn-primary">➕ New Expense</a>
        </div>

        <div class="card" style="padding: 0;">
            <div class="table-container" style="border: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Paid By</th>
                            <th>Total Amount</th>
                            <th>Your Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">Oct 15</div>
                                <div style="color: var(--text-muted); font-size: 0.8rem;">2026</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">Flight Tickets</div>
                                <div class="split-details">Split equally (4)</div>
                            </td>
                            <td>
                                <span class="chip" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><img src="https://ui-avatars.com/api/?name=You&background=random" style="width: 16px; height: 16px;" alt="Avatar"> You</span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-main);">$450.00</td>
                            <td class="amount-positive">Lent $337.50</td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">Oct 16</div>
                                <div style="color: var(--text-muted); font-size: 0.8rem;">2026</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">Dinner at Luigi's</div>
                                <div class="split-details">Split equally (4)</div>
                            </td>
                            <td>
                                <span class="chip" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><img src="https://ui-avatars.com/api/?name=Alice&background=random" style="width: 16px; height: 16px;" alt="Avatar"> Alice</span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-main);">$120.00</td>
                            <td class="amount-negative">Borrowed $30.00</td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">Oct 17</div>
                                <div style="color: var(--text-muted); font-size: 0.8rem;">2026</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">Museum Tickets</div>
                                <div class="split-details">Split equally (3)</div>
                            </td>
                            <td>
                                <span class="chip" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><img src="https://ui-avatars.com/api/?name=Bob&background=random" style="width: 16px; height: 16px;" alt="Avatar"> Bob</span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-main);">$60.00</td>
                            <td class="amount-negative">Borrowed $20.00</td>
                        </tr>
                        <tr>
                            <td>
                                <div style="font-weight: 500;">Oct 18</div>
                                <div style="color: var(--text-muted); font-size: 0.8rem;">2026</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">Uber Ride</div>
                                <div class="split-details">Split equally (4)</div>
                            </td>
                            <td>
                                <span class="chip" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;"><img src="https://ui-avatars.com/api/?name=You&background=random" style="width: 16px; height: 16px;" alt="Avatar"> You</span>
                            </td>
                            <td style="font-weight: 600; color: var(--text-main);">$35.00</td>
                            <td class="amount-positive">Lent $26.25</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
