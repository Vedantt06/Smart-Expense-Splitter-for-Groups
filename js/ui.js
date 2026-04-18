document.addEventListener('DOMContentLoaded', () => {

    // Simple Form Validation with shake effect
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const requiredInputs = form.querySelectorAll('input[required], select[required]');
            let valid = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = 'var(--danger)';
                    input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                    
                    input.classList.remove('shake');
                    void input.offsetWidth; // trigger reflow
                    input.classList.add('shake');
                    
                    setTimeout(() => input.classList.remove('shake'), 400);
                } else {
                    input.style.borderColor = 'var(--border-color)';
                    input.style.boxShadow = 'none';
                }
            });

            if (!valid) {
                e.preventDefault();
            }
        });
    });

    // Reset styles on input
    const allInputs = document.querySelectorAll('input, select');
    allInputs.forEach(input => {
        input.addEventListener('input', () => {
            input.style.borderColor = '';
            input.style.boxShadow = '';
        });
    });

    // Dynamic Member Fields Logic
    const addMemberBtn = document.getElementById('add-member-btn');
    const membersContainer = document.getElementById('members-container');

    if (addMemberBtn && membersContainer) {
        addMemberBtn.addEventListener('click', () => {
            // Create field container
            const fieldDiv = document.createElement('div');
            fieldDiv.className = 'member-field fade-in';
            fieldDiv.innerHTML = `
                <input type="text" name="member[]" placeholder="Enter member name" required>
                <button type="button" class="btn btn-danger remove-member-btn" style="padding: 0.75rem 1rem;">✕</button>
            `;
            
            membersContainer.appendChild(fieldDiv);
            
            // Focus new input
            const newInput = fieldDiv.querySelector('input');
            newInput.focus();

            // Attach remove event natively
            const removeBtn = fieldDiv.querySelector('.remove-member-btn');
            removeBtn.addEventListener('click', () => {
                fieldDiv.style.transform = 'scale(0.95)';
                fieldDiv.style.opacity = '0';
                fieldDiv.style.transition = 'all 0.3s ease';
                setTimeout(() => fieldDiv.remove(), 300); // Wait for transition
            });
        });

        // Initialize remove buttons already present
        document.querySelectorAll('.remove-member-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const parent = this.closest('.member-field');
                parent.style.transform = 'scale(0.95)';
                parent.style.opacity = '0';
                parent.style.transition = 'all 0.3s ease';
                setTimeout(() => parent.remove(), 300);
            });
        });
    }
});
