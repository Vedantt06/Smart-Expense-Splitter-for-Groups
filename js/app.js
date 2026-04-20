// js/app.js

let currentUser = null;
let currentGroupId = null;

$(document).ready(function () {
    // Global AJAX setup to prevent repeating code
    $.ajaxSetup({
        dataType: 'json',
        error: function (xhr) {
            alert("Error: " + (xhr.responseJSON ? xhr.responseJSON.message : xhr.responseText));
        }
    });

    checkSession();
    fetchDailyQuote();

    $('#login-form').submit(e => handleAuth(e, 'login'));
    $('#register-form').submit(e => handleAuth(e, 'register'));
    $('#create-group-form').submit(handleCreateGroup);
    $('#add-expense-form').submit(handleAddExpense);
    $('#search-user').on('keyup', debounce(searchUsers, 500));
});

// --- UI Navigation & Effects ---
function showSection(id) { $('.section').hide(); $('#' + id).fadeIn(300); }
function switchTab(name) {
    $('.tab').removeClass('active');
    $(`.tab:contains('${name === 'expenses' ? 'Expenses' : 'Balances'}')`).addClass('active');
    $('.tab-content').hide();
    $(`#tab-${name}`).fadeIn(300);
}
function showLoading(id) { $(`#${id}`).html('<div class="spinner"></div>'); }

function fetchDailyQuote() {
    fetch('https://type.fit/api/quotes').then(r => r.json()).then(d => {
        let q = d[Math.floor(Math.random() * d.length)];
        $('#daily-quote').text(`"${q.text}" - ${q.author ? q.author.split(',')[0] : 'Unknown'}`);
    }).catch(() => $('#daily-quote').text('"A penny saved is a penny earned."'));
}

// --- Authentication ---
function checkSession() {
    $.get('api/auth.php?action=check', res => {
        if (res.status === 'success') loginSuccess(res.user);
        else showSection('login-section');
    });
}

function handleAuth(e, action) {
    e.preventDefault();
    let prefix = action === 'login' ? 'login' : 'reg';
    let data = { username: $(`#${prefix}-username`).val(), password: $(`#${prefix}-password`).val() };

    $.post(`api/auth.php?action=${action}`, data, res => {
        if (res.status === 'success') {
            e.target.reset();
            loginSuccess(res.user);
        } else alert(res.message);
    });
}

function loginSuccess(user) {
    currentUser = user;
    $('#welcome-msg').text(`Hello, ${user.username}!`);
    loadGroups();
    showSection('dashboard-section');
}

function logout() {
    $.get('api/auth.php?action=logout', () => { currentUser = currentGroupId = null; showSection('login-section'); });
}

// --- Groups Management ---
function loadGroups() {
    showLoading('groups-list');
    $.get('api/groups.php?action=list', res => {
        let html = res.groups.length ? '' : '<div style="text-align:center; padding: 20px; color: #777;">No groups yet.</div>';
        res.groups.forEach(g => {
            html += `<div class="list-item" style="display:none" onclick="openGroup(${g.id}, '${g.name}')">
                        <div><div class="list-item-title">${g.name}</div><div class="list-item-sub">Created by ${g.creator}</div></div>
                        <i class="fas fa-chevron-right" style="color: #ccc;"></i>
                     </div>`;
        });
        $('#groups-list').html(html).find('.list-item').each((i, el) => $(el).delay(i * 100).slideDown(200));
    });
}

function handleCreateGroup(e) {
    e.preventDefault();
    $.post('api/groups.php?action=create', { name: $('#new-group-name').val() }, res => {
        if (res.status === 'success') { e.target.reset(); loadGroups(); showSection('dashboard-section'); }
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

function debounce(func, wait) { let t; return function (...args) { clearTimeout(t); t = setTimeout(() => func.apply(this, args), wait); }; }

function searchUsers() {
    let term = $('#search-user').val();
    if (term.length < 2) return $('#search-results').empty();

    $.get(`api/auth.php?action=users&q=${term}`, res => {
        let html = res.users.map(u => `<div class="list-item"><span>${u.username}</span><button onclick="addMember(${u.id})" class="btn btn-primary" style="padding: 5px 10px; width: auto; font-size:12px;">Add</button></div>`).join('');
        $('#search-results').html(html);
    });
}

function addMember(userId) {
    $.post('api/groups.php?action=add_member', { group_id: currentGroupId, user_id: userId }, res => {
        if (res.status === 'success') {
            alert("Member added!"); $('#search-user').val(''); $('#search-results').empty();
            showSection('group-details-section'); loadGroupDetails(); loadBalances();
        } else alert(res.message);
    });
}

// --- Expenses & Settlements ---
function loadGroupDetails() {
    $.get(`api/groups.php?action=details&id=${currentGroupId}`, res => {
        let html = res.members.map(m => `<label class="member-label"><input type="checkbox" class="member-checkbox split-user-cb" value="${m.id}" checked> ${m.username} ${m.id == currentUser.id ? '(You)' : ''}</label>`).join('');
        $('#split-members-list').html(html);
    });
}

function handleAddExpense(e) {
    e.preventDefault();
    let splits = $('.split-user-cb:checked').map(function () { return $(this).val(); }).get();
    if (!splits.length) return alert("Select at least one member.");

    let data = { group_id: currentGroupId, description: $('#exp-desc').val(), amount: parseFloat($('#exp-amount').val()), split_with: JSON.stringify(splits) };

    $.post('api/expenses.php?action=add', data, res => {
        if (res.status === 'success') {
            e.target.reset(); showSection('group-details-section');
            loadExpenses(); loadBalances();
        } else alert(res.message);
    });
}

function loadExpenses() {
    showLoading('expenses-list');
    $.get(`api/expenses.php?action=list&group_id=${currentGroupId}`, res => {
        let html = res.expenses.length ? '' : '<div style="text-align:center; padding: 20px; color: #777;">No expenses added yet.</div>';
        res.expenses.forEach(e => {
            html += `<div class="expense-item"><div class="expense-icon"><i class="fas fa-receipt"></i></div>
                        <div class="expense-details"><div style="font-weight:700;">${e.description}</div><div style="font-size:12px; color:#777;">Paid by ${e.paid_by_name === currentUser.username ? 'You' : e.paid_by_name}</div></div>
                        <div class="expense-amount">$${parseFloat(e.amount).toFixed(2)}</div>
                     </div>`;
        });
        $('#expenses-list').html(html);
    });
}

function loadBalances() {
    showLoading('balances-list'); showLoading('settlements-list');
    $.get(`api/settle.php?group_id=${currentGroupId}`, res => {
        if (res.status !== 'success') return;

        let balHtml = res.balances.map(b => `<div class="expense-item">
                <div class="expense-details"><div style="font-weight:600;">${b.name === currentUser.username ? 'You' : b.name}</div></div>
                <div class="expense-amount ${b.balance > 0 ? 'amount-positive' : (b.balance < 0 ? 'amount-negative' : '')}">${b.balance > 0 ? '+' : ''}${b.balance.toFixed(2)}</div>
            </div>`).join('');
        $('#balances-list').html(balHtml);

        let setHtml = res.settlements.length ? res.settlements.map(s => `<div class="settle-item">
                <div class="expense-icon" style="background:#FFE3E3; color:var(--danger);"><i class="fas fa-arrow-right"></i></div>
                <div class="expense-details"><div style="font-weight:700;">${s.from_name === currentUser.username ? 'You' : s.from_name} <span style="font-weight:400; color:#777;">owes</span> ${s.to_name === currentUser.username ? 'You' : s.to_name}</div></div>
                <div class="expense-amount amount-negative">$${s.amount.toFixed(2)}</div>
            </div>`).join('') : '<div style="text-align:center; padding: 20px; color: #777;">Everyone is settled up!</div>';
        $('#settlements-list').html(setHtml);
    });
}
