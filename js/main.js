console.log("MAIN JS LOADED");

$(document).ready(function () {

    console.log("AJAX Mode Activated 🚀");

    loadUsers();
    loadExpenses();
    loadBalances();

    $("#addExpenseBtn").click(function () {
        console.log("Save Expense button clicked");

        let amount = $("#amount").val();
        let paid_by = $("#paid_by").val();
        let split_between = [];

        $(".split-user:checked").each(function () {
            split_between.push($(this).val());
        });

        console.log("Amount:", amount, "Paid by:", paid_by, "Split between:", split_between);

        if (amount === "" || split_between.length === 0) {
            console.log("Please fill all fields!");
            return;
        }

        console.log("Sending AJAX request");

        $.ajax({
            url: "save_expense.php",
            type: "POST",
            dataType: "json",   
            data: {
                amount: amount,
                paid_by: paid_by,
                split_between: split_between
            },
            success: function (response) {

                console.log("Response:", response);

                let res = response;

                if (res.status === "success") {

                    console.log("Expense Added!");

                    $("#amount").val("");
                    $(".split-user").prop("checked", false);

                    loadExpenses();
                    loadBalances();

                } else {
                    console.log("Failed to add expense");
                }
            },
            error: function () {
                console.log("Server error");
            }
        });

    });

    function loadUsers() {

        $.ajax({
            url: "get_users.php",
            type: "GET",
            dataType: "json",   
            success: function (response) {

                let users = response;  

                let dropdown = "";
                let checkboxes = "";

                users.forEach(function (user) {

                    dropdown += `<option value="${user.id}">${user.name}</option>`;

                    checkboxes += `
                        <label>
                            <input type="checkbox" class="split-user" value="${user.id}">
                            ${user.name}
                        </label><br>
                    `;
                });

                $("#paid_by").html(dropdown);
                $("#split_users").html(checkboxes);
            },
            error: function () {
                alert("Failed to load users");
            }
        });

    }

    function loadExpenses() {

        $.ajax({
            url: "get_expenses.php",
            type: "GET",
            dataType: "json",   
            success: function (response) {

                let expenses = response;  

                let html = "";

                if (expenses.length === 0) {
                    html = "<p>No expenses yet</p>";
                } else {

                    expenses.forEach(function (exp) {
                        html += `
                            <li>
                                <strong>${exp.paid_by}</strong> paid ₹${exp.amount}
                            </li>
                        `;
                    });
                }

                $("#expenseList").html(html);
            },
            error: function () {
                alert("Failed to load expenses");
            }
        });

    }

    function loadBalances() {

        $.ajax({
            url: "get_balances.php",
            type: "GET",
            dataType: "json",   
            success: function (response) {

                let data = response;  

                let html = "";

                if (data.length === 0) {
                    html = "<p>No balances yet</p>";
                } else {

                    data.forEach(function (item) {
                        html += `
                            <p>
                                <strong>${item.from}</strong> pays 
                                <strong>${item.to}</strong> ₹${item.amount}
                            </p>
                        `;
                    });
                }

                $("#balanceSection").html(html);
            },
            error: function () {
                alert("Failed to load balances");
            }
        });

    }

});