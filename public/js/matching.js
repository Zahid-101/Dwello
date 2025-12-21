
// Tab switching functionality
function switchTab(tabName) {
    // Remove active class from all tabs and content
    document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

    // Add active class to clicked tab and corresponding content
    document.getElementById(tabName + 'Tab').classList.add('active');
    document.getElementById(tabName + 'Content').classList.add('active');
}

// Save/unsave functionality
// Save/unsave functionality
function toggleSaved(element) {
    // Standard visual feedback immediately for responsiveness
    element.classList.toggle('saved');
    element.style.transform = 'scale(1.2)';
    setTimeout(() => { element.style.transform = 'scale(1)'; }, 200);

    // Get Profile ID (assuming parent has data-id or similar, or we pass it)
    // The element is usually the .saved-indicator div inside .profile-card
    // Let's find the profile ID from the card context
    const card = element.closest('.profile-card');
    // We didn't explicitly add data-id to profile-card in index.blade.php, let's fix that too.
    // Assuming data-id is there or we can find it.
    // Wait, the index loop is likely needed to be updated to pass ID to this function or set on element.

    // Better strategy: Expect the element to have data-id
    const profileId = element.dataset.id;

    if (!profileId) {
        console.warn('No profile ID found for favorite toggle');
        return;
    }

    // Call API
    fetch(`/favorites/${profileId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
        .then(res => res.json())
        .then(data => {
            // Sync state if server disagrees (optional, but good for robustness)
            if (data.saved) {
                element.classList.add('saved');
            } else {
                element.classList.remove('saved');
            }
        })
        .catch(err => console.error('Error toggling favorite:', err));
}

// Show comparison results
function showComparison() {
    const selectedProfileId = document.getElementById('profileBSelect').value;
    const emptyState = document.getElementById('emptyState');
    const comparisonResults = document.getElementById('comparisonResults');

    if (!selectedProfileId) {
        emptyState.style.display = 'block';
        comparisonResults.style.display = 'none';
        return;
    }

    // Find profile in standard window.serverProfiles
    const profile = window.serverProfiles.find(p => p.id == selectedProfileId);

    // Fallback or self if defined
    // const myProfile = window.userProfile || { budget_max: 30000, is_smoker: 0, has_pets: 0, preferred_city: 'Colombo' };

    // For "You", we can just use a placeholder if the user isn't logged in, 
    // BUT the requirement was to compare available profiles.
    // If the user IS logged in, great. If not, maybe we just show the score relative to a standard "ideal"?
    // Or we assume the user's preferences are somewhat default.
    const myProfile = window.userProfile || {};

    if (!profile) {
        console.warn('Profile data not found for comparison');
        return;
    }

    // Perform dynamic calculation
    const calculated = calculateCompatibility(myProfile, profile);

    emptyState.style.display = 'none';
    comparisonResults.style.display = 'block';

    // Update compatibility score and circle
    document.getElementById('compatibilityScore').textContent = calculated.total + '%';
    const circle = document.getElementById('compatibilityCircle');
    const circumference = 339.3;
    const offset = circumference - (calculated.total / 100) * circumference;
    circle.style.strokeDashoffset = offset;

    // Update circle color based on compatibility
    if (calculated.total >= 90) {
        circle.style.stroke = '#10B981';
    } else if (calculated.total >= 80) {
        circle.style.stroke = '#F59E0B';
    } else {
        circle.style.stroke = '#EF4444';
    }

    // Update category scores and bars
    updateCategoryBar('budget', calculated.budget);
    updateCategoryBar('location', calculated.location);
    updateCategoryBar('lifestyle', calculated.lifestyle);
    // Hide others or mock them if data missing
    // updateCategoryBar('schedule', 85); 

    // Update match details
    const matchDetails = document.getElementById('matchDetails');
    matchDetails.innerHTML = '';

    calculated.details.forEach(detail => {
        const detailDiv = document.createElement('div');
        detailDiv.style.marginBottom = '12px';

        const chipClass = detail.type === 'match' ? 'chip-match' :
            detail.type === 'partial' ? 'chip-partial' : 'chip-conflict';

        detailDiv.innerHTML = `
            <span class="compatibility-chip ${chipClass}" style="margin-right: 8px;">●</span>
            <span style="font-size: 14px;">${detail.text}</span>
        `;

        matchDetails.appendChild(detailDiv);
    });
}

function calculateCompatibility(me, them) {
    // 1. Budget (30%)
    let budgetScore = 50; // default neutral
    // If ranges overlap or are close -> high score
    // Simplification for demo:
    if (me.budget_max && them.budget_max) {
        const diff = Math.abs(me.budget_max - them.budget_max);
        if (diff < 5000) budgetScore = 100;
        else if (diff < 10000) budgetScore = 80;
        else budgetScore = 40;
    } else {
        budgetScore = 75; // unknown
    }

    // 2. Location (20%)
    let locationScore = 50;
    if (me.preferred_city && them.preferred_city) {
        if (me.preferred_city.toLowerCase() === them.preferred_city.toLowerCase()) locationScore = 100;
        else locationScore = 30;
    } else {
        locationScore = 70;
    }

    // 3. Lifestyle (Smoking, Pets) (50%)
    let lifestyleScore = 100;
    const details = [];

    // Smoking
    if (me.is_smoker === them.is_smoker) {
        lifestyleScore += 0;
        details.push({ type: 'match', text: me.is_smoker ? 'Both accept smoking' : 'Both non-smokers' });
    } else {
        // If I am non-smoker and they are smoker -> conflict
        if (!me.is_smoker && them.is_smoker) {
            lifestyleScore -= 30;
            details.push({ type: 'conflict', text: 'Smoking preference mismatch' });
        } else {
            // I am smoker, they are not -> compatible if they tolerate it?
            lifestyleScore -= 10;
            details.push({ type: 'partial', text: 'Mixed smoking habits' });
        }
    }

    // Pets
    if (me.has_pets && them.has_pets) {
        details.push({ type: 'match', text: 'Both have pets' });
    } else if (me.has_pets && !them.has_pets) {
        // I have pets, do they like pets? (Missing field in simple model, assume conflict risk)
        lifestyleScore -= 20;
        details.push({ type: 'partial', text: 'You have pets' });
    } else {
        details.push({ type: 'match', text: 'No pet conflicts' });
    }

    // Cleanliness (if available) - mocking for now since backend field might be null
    if (them.cleanliness && me.cleanliness) {
        if (Math.abs(them.cleanliness - me.cleanliness) <= 1) {
            details.push({ type: 'match', text: 'Similar cleanliness levels' });
        }
    }

    // Aggregate Total
    // Weighted avg: Budget 30%, Loc 20%, Lifestyle 50%
    // Ensure lifestyle doesn't drop below 0
    lifestyleScore = Math.max(0, Math.min(100, lifestyleScore));

    const total = Math.round((budgetScore * 0.3) + (locationScore * 0.2) + (lifestyleScore * 0.5));

    // Add other details for the UI cards
    if (budgetScore > 80) details.push({ type: 'match', text: 'Budget matches well' });
    else if (budgetScore < 50) details.push({ type: 'partial', text: 'Budget gap' });

    if (locationScore > 80) details.push({ type: 'match', text: 'Same location' });

    return {
        total: Math.max(10, Math.min(100, total)), // clamp
        budget: budgetScore,
        location: locationScore,
        lifestyle: lifestyleScore,
        details: details
    };
}

// Update category bars
function updateCategoryBar(category, percentage) {
    const scoreEl = document.getElementById(category + 'Score');
    const barEl = document.getElementById(category + 'Bar');

    if (scoreEl) scoreEl.textContent = percentage + '%';
    if (barEl) {
        barEl.style.width = percentage + '%';

        // Update color based on percentage
        if (percentage >= 90) {
            barEl.style.background = '#10B981';
        } else if (percentage >= 80) {
            barEl.style.background = '#F59E0B';
        } else {
            barEl.style.background = '#EF4444';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Add click animations to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', function () {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
});
