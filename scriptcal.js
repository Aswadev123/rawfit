// Navigation functionality
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
    }
    
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Macro Calculator Functions
function calculateBMR(weight, height, age, gender) {
    // Mifflin-St Jeor Equation
    if (gender === 'male') {
        return (10 * weight) + (6.25 * height) - (5 * age) + 5;
    } else {
        return (10 * weight) + (6.25 * height) - (5 * age) - 161;
    }
}

function calculateTDEE(bmr, activityLevel) {
    return bmr * parseFloat(activityLevel);
}

function getCalorieAdjustment(goal) {
    const adjustments = {
        'cut': -0.2,      // 20% deficit
        'maintain': 0,     // No change
        'bulk': 0.15,      // 15% surplus
        'recomp': -0.1     // 10% deficit
    };
    return adjustments[goal] || 0;
}

function getMacroRatios(goal) {
    const ratios = {
        'cut': { protein: 0.35, carbs: 0.35, fats: 0.30 },
        'maintain': { protein: 0.25, carbs: 0.45, fats: 0.30 },
        'bulk': { protein: 0.25, carbs: 0.50, fats: 0.25 },
        'recomp': { protein: 0.35, carbs: 0.35, fats: 0.30 }
    };
    return ratios[goal] || ratios['maintain'];
}

function getTipsForGoal(goal, protein, carbs, fats) {
    const tips = {
        'cut': [
            'Focus on high-protein foods to maintain muscle mass during fat loss',
            'Include plenty of vegetables for fiber and satiety',
            'Time your carbs around workouts for better performance',
            'Stay hydrated and consider intermittent fasting',
            'Track your food intake for better adherence'
        ],
        'maintain': [
            'Eat a balanced diet with variety from all food groups',
            'Focus on whole, minimally processed foods',
            'Listen to your hunger and fullness cues',
            'Stay consistent with your eating patterns',
            'Include regular physical activity'
        ],
        'bulk': [
            'Eat in a moderate surplus to minimize fat gain',
            'Focus on nutrient-dense, calorie-rich foods',
            'Include healthy fats like nuts, oils, and avocados',
            'Time your meals around workouts for optimal recovery',
            'Be patient - muscle gain takes time'
        ],
        'recomp': [
            'Prioritize protein to support muscle growth and fat loss',
            'Combine strength training with your nutrition plan',
            'Be patient - body recomposition is a slow process',
            'Focus on progressive overload in your workouts',
            'Consider cycling calories based on training days'
        ]
    };
    return tips[goal] || tips['maintain'];
}

function calculateMacros() {
    // Get form values
    const gender = document.querySelector('input[name="gender"]:checked').value;
    const age = parseInt(document.getElementById('age').value);
    const height = parseInt(document.getElementById('height').value);
    const weight = parseFloat(document.getElementById('weight').value);
    const bodyFat = parseFloat(document.getElementById('body-fat').value) || null;
    const activityLevel = document.getElementById('activity').value;
    const goal = document.getElementById('goal').value;
    
    // Validate inputs
    if (!age || !height || !weight) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Calculate BMR and TDEE
    const bmr = calculateBMR(weight, height, age, gender);
    const tdee = calculateTDEE(bmr, activityLevel);
    
    // Adjust calories based on goal
    const calorieAdjustment = getCalorieAdjustment(goal);
    const targetCalories = Math.round(tdee * (1 + calorieAdjustment));
    
    // Calculate macros
    const macroRatios = getMacroRatios(goal);
    const proteinCalories = targetCalories * macroRatios.protein;
    const carbsCalories = targetCalories * macroRatios.carbs;
    const fatsCalories = targetCalories * macroRatios.fats;
    
    const proteinGrams = Math.round(proteinCalories / 4);
    const carbsGrams = Math.round(carbsCalories / 4);
    const fatsGrams = Math.round(fatsCalories / 9);
    
    // Update UI
    updateResults(targetCalories, proteinGrams, carbsGrams, fatsGrams, bmr, tdee, goal);
    
    // Show results panel
    const resultsPanel = document.getElementById('results-panel');
    resultsPanel.classList.add('show');
    
    // Smooth scroll to results
    resultsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function updateResults(calories, protein, carbs, fats, bmr, tdee, goal) {
    // Update calorie display
    document.getElementById('total-calories').textContent = calories.toLocaleString();
    
    // Update macro cards
    document.getElementById('protein-grams').textContent = protein;
    document.getElementById('protein-calories').textContent = `${protein * 4} calories`;
    document.getElementById('protein-percentage').textContent = `${Math.round((protein * 4 / calories) * 100)}%`;
    
    document.getElementById('carbs-grams').textContent = carbs;
    document.getElementById('carbs-calories').textContent = `${carbs * 4} calories`;
    document.getElementById('carbs-percentage').textContent = `${Math.round((carbs * 4 / calories) * 100)}%`;
    
    document.getElementById('fats-grams').textContent = fats;
    document.getElementById('fats-calories').textContent = `${fats * 9} calories`;
    document.getElementById('fats-percentage').textContent = `${Math.round((fats * 9 / calories) * 100)}%`;
    
    // Update additional info
    document.getElementById('bmr-value').textContent = `${Math.round(bmr).toLocaleString()} calories/day`;
    document.getElementById('tdee-value').textContent = `${Math.round(tdee).toLocaleString()} calories/day`;
    
    // Update tips
    const tips = getTipsForGoal(goal, protein, carbs, fats);
    const tipsContent = document.getElementById('tips-content');
    tipsContent.innerHTML = `
        <ul>
            ${tips.map(tip => `<li>${tip}</li>`).join('')}
        </ul>
    `;
    
    // Update chart
    updateMacroChart(protein * 4, carbs * 4, fats * 9);
}

function updateMacroChart(proteinCals, carbsCals, fatsCals) {
    const canvas = document.getElementById('macroChart');
    const ctx = canvas.getContext('2d');
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = 100;
    
    const total = proteinCals + carbsCals + fatsCals;
    
    // Calculate angles
    const proteinAngle = (proteinCals / total) * 2 * Math.PI;
    const carbsAngle = (carbsCals / total) * 2 * Math.PI;
    const fatsAngle = (fatsCals / total) * 2 * Math.PI;
    
    let currentAngle = -Math.PI / 2; // Start from top
    
    // Draw protein slice
    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + proteinAngle);
    ctx.closePath();
    ctx.fillStyle = '#f59e0b';
    ctx.fill();
    
    currentAngle += proteinAngle;
    
    // Draw carbs slice
    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + carbsAngle);
    ctx.closePath();
    ctx.fillStyle = '#3b82f6';
    ctx.fill();
    
    currentAngle += carbsAngle;
    
    // Draw fats slice
    ctx.beginPath();
    ctx.moveTo(centerX, centerY);
    ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + fatsAngle);
    ctx.closePath();
    ctx.fillStyle = '#10b981';
    ctx.fill();
    
    // Add center circle
    ctx.beginPath();
    ctx.arc(centerX, centerY, 40, 0, 2 * Math.PI);
    ctx.fillStyle = 'white';
    ctx.fill();
    
    // Add text in center
    ctx.fillStyle = '#1e293b';
    ctx.font = 'bold 14px Inter';
    ctx.textAlign = 'center';
    ctx.fillText('Macros', centerX, centerY - 5);
    ctx.font = '12px Inter';
    ctx.fillText('Breakdown', centerX, centerY + 10);
}

// Input validation and formatting
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea in message input (if exists)
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
    
    // Number input validation
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.value < 0) this.value = 0;
        });
    });
});

// Smooth animations for form interactions
document.addEventListener('DOMContentLoaded', function() {
    const formInputs = document.querySelectorAll('input, select');
    
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});