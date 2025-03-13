/* PROGRESS WITH STEPS */

const prevBtns = document.querySelectorAll(".btn-prev");
const nextBtns = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".progress-step");

let formStepsNum = 0;

function isEmailValid(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
}

function isPhoneNumberValid(phone) {
    const phonePattern = /^[0-9]{10}$/;
    return phonePattern.test(phone);
}

function isDateofBirthValid(dateofBirth) {
    const dateofBirthPattern = /^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/;
    return dateofBirthPattern.test(dateofBirth);
}

function areFieldsFilled() {
    const currentStep = formSteps[formStepsNum];
    const inputs = currentStep.querySelectorAll("input[required], textarea[required], select[required]");
    
    const allFilled = Array.from(inputs).every(input => input.value.trim() !== "");
    
    const emailInput = currentStep.querySelector("input[type='email']");
    const emailValid = emailInput ? isEmailValid(emailInput.value) : true;

    const phoneInput = currentStep.querySelector("input[name='phone']");
    const phoneValid = phoneInput ? isPhoneNumberValid(phoneInput.value) : true;

    const passwordInput = currentStep.querySelector("input[name='password']");
    const confirmPasswordInput = currentStep.querySelector("input[name='confirmPassword']");

    const isPasswordValid = passwordInput ? passwordInput.value.length >= 8 : true;
    const passwordsMatch = passwordInput && confirmPasswordInput ? passwordInput.value === confirmPasswordInput.value : true;

    return allFilled && emailValid && phoneValid && passwordsMatch && isPasswordValid;
}

function updateFormSteps() {
    formSteps.forEach((formStep, index) => {
        formStep.classList.toggle("form-step-active", index === formStepsNum);
    });
}

function updateProgressbar() {
    progressSteps.forEach((progressStep, idx) => {
        progressStep.classList.toggle("progress-step-active", idx < formStepsNum + 1);
    });

    const progressActive = document.querySelectorAll(".progress-step-active");
    progress.style.width = ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%";
}

nextBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        if (areFieldsFilled()) {
            formStepsNum++;
            updateFormSteps();
            updateProgressbar();
        } else {
            alert("Please fill in all required fields correctly before proceeding.");
        }
    });
});

prevBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
        if (formStepsNum > 0) {
            formStepsNum--;
            updateFormSteps();
            updateProgressbar();
        }
    });
});

function setMaxDate() {
    const today = new Date();
    const minDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    const maxDate = minDate.toISOString().split('T')[0];
    document.getElementById('dob').setAttribute('max', maxDate);
}

window.onload = function() {
    setMaxDate();
};