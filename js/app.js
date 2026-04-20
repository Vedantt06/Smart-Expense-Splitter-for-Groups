// js/app.js

let currentUser = null;
let currentGroupId = null;

$(document).ready(function() {
    // Check if user is logged in
    checkSession();

    // Fetch quote using Fetch API (Requirement)
    fetchDailyQuote();

    // Form Submissions
    $('#login-form').submit(handleLogin);
    $('#register-form').submit(handleRegister);
    $('#create-group-form').submit(handleCreateGroup);
    $('#add-expense-form').submit(handleAddExpense);
    
    // Search user input event
    $('#search-user').on('keyup', debounce(searchUsers, 500));
});

// --- UI Navigation & Effects (jQuery) ---
function showSection(sectionId) {
    // jQuery fade effect for smooth transitions
    $('.section').hide();
    $('#' + sectionId).fadeIn(300);
}

function switchTab(tabName) {
    $('.tab').removeClass('active');
    $('.tab-content').hide();
    
    // Find the tab that was clicked and activate
    $(`.tab:contains('${tabName === 'expenses' ? 'Expenses' : 'Balances'}')`).addClass('active');
    $(`#tab-${tabName}`).fadeIn(300);
}

function showLoading(containerId) {
    $(`#${containerId}`).html('<div class="spinner"></div>');
}

// --- Fetch API Requirement ---
function fetchDailyQuote() {
    // Using a public API for quotes to fulfill the specific lab requirement
    fetch('https://type.fit/api/quotes')
        .then(response => response.json())
        .then(data => {
            if(data && data.length > 0) {
                // Get random quote
                const randomQuote = data[Math.floor(Math.random() * data.length)];
                let text = randomQuote.text;
                let author = randomQuote.author ? randomQuote.author.split(',')[0] : 'Unknown';
                document.getElementById('daily-quote').innerText = `"${text}" - ${author}`;
            }
        })
        .catch(err => {
            console.error('Fetch API Error:', err);
            document.getElementById('daily-quote').innerText = '"A penny saved is a penny earned." - Benjamin Franklin';
        });
}

// --- Authentication ---
function checkSession() {
    $.ajax({
        url: 'api/auth.php?action=check',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                currentUser = res.user;
                $('#welcome-msg').text(`Hello, ${currentUser.username}!`);
                loadGroups();
                showSection('dashboard-section');
            } else {
                showSection('login-section');
            }
        },
        error: function(xhr, status, error) {
            console.error("Session check error:", error);
            showSection('login-section');
        }
    });
}

function handleLogin(e) {
    e.preventDefault();
    const username = $('#login-username').val();
    const password = $('#login-password').val();

    // JS Form Validation
    if(username.trim() === '' || password.trim() === '') {
        alert("Please fill in all fields.");
        return;
    }

    $.ajax({
        url: 'api/auth.php?action=login',
        type: 'POST',
        data: { username, password },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                currentUser = res.user;
                $('#welcome-msg').text(`Hello, ${currentUser.username}!`);
                $('#login-form')[0].reset();
                loadGroups();
                showSection('dashboard-section');
            } else {
                alert(res.message);
            }
        },
        error: function(xhr, status, error) {
            let msg = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
            alert("Login error: " + msg);
        }
    });
}

function handleRegister(e) {
    e.preventDefault();
    const username = $('#reg-username').val();
    const password = $('#reg-password').val();

    $.ajax({
        url: 'api/auth.php?action=register',
        type: 'POST',
        data: { username, password },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                currentUser = res.user;
                $('#welcome-msg').text(`Hello, ${currentUser.username}!`);
                $('#register-form')[0].reset();
                loadGroups();
                showSection('dashboard-section');
            } else {
                alert(res.message);
            }
        },
        error: function(xhr, status, error) {
            let msg = xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText;
            alert("Registration error: " + msg);
        }
    });
}

function logout() {
    $.ajax({
        url: 'api/auth.php?action=logout',
        type: 'GET',
        success: function() {
            currentUser = null;
            currentGroupId = null;
            showSection('login-section');
        }
    });
}

// --- Groups Management ---
function loadGroups() {
    showLoading('groups-list');
    $.ajax({
        url: 'api/groups.php?action=list',
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                let html = '';
                if (res.groups.length === 0) {
                    html = '<div style="text-align:center; padding: 20px; color: #777;">You are not in any groups yet.</div>';
                } else {
                    res.groups.forEach(g => {
                        // Using jQuery animation effect on list rendering
                        html += `
                            <div class="list-item" style="display:none" onclick="openGroup(${g.id}, '${g.name}')">
                                <div>
                                    <div class="list-item-title">${g.name}</div>
                                    <div class="list-item-sub">Created by ${g.creator}</div>
                                </div>
                                <i class="fas fa-chevron-right" style="color: #ccc;"></i>
                            </div>
                        `;
                    });
                }
                $('#groups-list').html(html);
                // jQuery slideDown effect
                $('#groups-list .list-item').each(function(i) {
                    $(this).delay(i * 100).slideDown(200);
                });
            }
        }
    });
}

function handleCreateGroup(e) {
    e.preventDefault();
    const name = $('#new-group-name').val();
    if(name.trim() === '') return;

    $.ajax({
        url: 'api/groups.php?action=create',
        type: 'POST',
        data: { name },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                $('#create-group-form')[0].reset();
                loadGroups();
                showSection('dashboard-section');
            } else {
                alert(res.message);
            }
        }
    });
}

function openGroup(id, name) {
    currentGroupId = id;
    $('#detail-group-name').text(name);
    showSection('group-details-section');
    switchTab('expenses');
    loadGroupDetails();
    loadExpenses();
    loadBalances();
}

// --- Add Member ---
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

function searchUsers() {
    const term = $('#search-user').val();
    if (term.length < 2) {
        $('#search-results').empty();
        return;
    }

    $.ajax({
        url: `api/auth.php?action=users&q=${term}`,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                let html = '';
                res.users.forEach(u => {
                    html += `
                        <div class="list-item">
                            <span>${u.username}</span>
                            <button onclick="addMember(${u.id})" class="btn btn-primary" style="padding: 5px 10px; width: auto; font-size:12px;">Add</button>
                        </div>
                    `;
                });
                $('#search-results').html(html);
            }
        }
    });
}

function addMember(userId) {
    $.ajax({
        url: 'api/groups.php?action=add_member',
        type: 'POST',
        data: { group_id: currentGroupId, user_id: userId },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                alert("Member added!");
                $('#search-user').val('');
                $('#search-results').empty();
                showSection('group-details-section');
                loadGroupDetails(); // refresh members list internally
                loadBalances(); // balances might change if we want to show all members
            } else {
                alert(res.message);
            }
        }
    });
}

// --- Expenses & Settlements ---
let groupMembers = [];

function loadGroupDetails() {
    $.ajax({
        url: `api/groups.php?action=details&id=${currentGroupId}`,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                groupMembers = res.members;
                renderSplitMembersList();
            }
        }
    });
}

function renderSplitMembersList() {
    let html = '';
    groupMembers.forEach(m => {
        // Default everyone to be checked
        html += `
            <label class="member-label">
                <input type="checkbox" class="member-checkbox split-user-cb" value="${m.id}" checked>
                ${m.username} ${m.id == currentUser.id ? '(You)' : ''}
            </label>
        `;
    });
    $('#split-members-list').html(html);
}

function handleAddExpense(e) {
    e.preventDefault();
    const desc = $('#exp-desc').val();
    const amount = parseFloat($('#exp-amount').val());
    
    let split_with = [];
    $('.split-user-cb:checked').each(function() {
        split_with.push($(this).val());
    });

    if (split_with.length === 0) {
        alert("Select at least one member to split with.");
        return;
    }

    $.ajax({
        url: 'api/expenses.php?action=add',
        type: 'POST',
        data: {
            group_id: currentGroupId,
            description: desc,
            amount: amount,
            split_with: JSON.stringify(split_with)
        },
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                $('#add-expense-form')[0].reset();
                renderSplitMembersList(); // reset checkboxes
                showSection('group-details-section');
                loadExpenses();
                loadBalances();
            } else {
                alert(res.message);
            }
        }
    });
}

function loadExpenses() {
    showLoading('expenses-list');
    $.ajax({
        url: `api/expenses.php?action=list&group_id=${currentGroupId}`,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                let html = '';
                if (res.expenses.length === 0) {
                    html = '<div style="text-align:center; padding: 20px; color: #777;">No expenses added yet.</div>';
                } else {
                    res.expenses.forEach(e => {
                        let isYou = e.paid_by_name === currentUser.username;
                        html += `
                            <div class="expense-item">
                                <div class="expense-icon"><i class="fas fa-receipt"></i></div>
                                <div class="expense-details">
                                    <div style="font-weight:700;">${e.description}</div>
                                    <div style="font-size:12px; color:#777;">Paid by ${isYou ? 'You' : e.paid_by_name}</div>
                                </div>
                                <div class="expense-amount">$${parseFloat(e.amount).toFixed(2)}</div>
                            </div>
                        `;
                    });
                }
                $('#expenses-list').html(html);
            }
        }
    });
}

function loadBalances() {
    showLoading('balances-list');
    showLoading('settlements-list');
    $.ajax({
        url: `api/settle.php?group_id=${currentGroupId}`,
        type: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                // Render Net Balances
                let balHtml = '';
                res.balances.forEach(b => {
                    let isYou = b.name === currentUser.username;
                    let nameDisplay = isYou ? 'You' : b.name;
                    let cls = b.balance > 0 ? 'amount-positive' : (b.balance < 0 ? 'amount-negative' : '');
                    balHtml += `
                        <div class="expense-item">
                            <div class="expense-details">
                                <div style="font-weight:600;">${nameDisplay}</div>
                            </div>
                            <div class="expense-amount ${cls}">
                                ${b.balance > 0 ? '+' : ''}${b.balance.toFixed(2)}
                            </div>
                        </div>
                    `;
                });
                $('#balances-list').html(balHtml);

                // Render Settlements
                let setHtml = '';
                if (res.settlements.length === 0) {
                    setHtml = '<div style="text-align:center; padding: 20px; color: #777;">Everyone is settled up!</div>';
                } else {
                    res.settlements.forEach(s => {
                        let fromName = s.from_name === currentUser.username ? 'You' : s.from_name;
                        let toName = s.to_name === currentUser.username ? 'You' : s.to_name;
                        setHtml += `
                            <div class="settle-item">
                                <div class="expense-icon" style="background:#FFE3E3; color:var(--danger);"><i class="fas fa-arrow-right"></i></div>
                                <div class="expense-details">
                                    <div style="font-weight:700;">${fromName} <span style="font-weight:400; color:#777;">owes</span> ${toName}</div>
                                </div>
                                <div class="expense-amount amount-negative">$${s.amount.toFixed(2)}</div>
                            </div>
                        `;
                    });
                }
                $('#settlements-list').html(setHtml);
            }
        }
    });
}
