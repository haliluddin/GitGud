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
function isNameValid(name) {
    const namePattern = /^[a-zA-Z-' ]+$/;
    return namePattern.test(name);
}
function validateCurrentStep() {
    let valid = true;
    const currentStep = formSteps[formStepsNum];
    const firstNameInput = currentStep.querySelector("input[name='firstname']");
    const lastNameInput = currentStep.querySelector("input[name='lastname']");
    const phoneInput = currentStep.querySelector("input[name='phone']");
    const emailInput = currentStep.querySelector("input[name='email']");
    const dobInput = currentStep.querySelector("input[name='dob']");
    const sexSelect = currentStep.querySelector("select[name='sex']");
    const passwordInput = currentStep.querySelector("input[name='password']");
    const confirmPasswordInput = currentStep.querySelector("input[name='confirm_password']");
    if(firstNameInput){
        if(firstNameInput.value.trim() === "" || !isNameValid(firstNameInput.value.trim())){
            valid = false;
            firstNameInput.nextElementSibling.textContent = "Only letters and white space allowed";
        } else {
            firstNameInput.nextElementSibling.textContent = "";
        }
    }
    if(lastNameInput){
        if(lastNameInput.value.trim() === "" || !isNameValid(lastNameInput.value.trim())){
            valid = false;
            lastNameInput.nextElementSibling.textContent = "Only letters and white space allowed";
        } else {
            lastNameInput.nextElementSibling.textContent = "";
        }
    }
    if(phoneInput){
        if(phoneInput.value.trim() === "" || !isPhoneNumberValid(phoneInput.value.trim())){
            valid = false;
            phoneInput.parentElement.querySelector(".text-danger").textContent = "Enter a valid 10-digit phone number";
        } else {
            phoneInput.parentElement.querySelector(".text-danger").textContent = "";
        }
    }
    if(emailInput){
        if(emailInput.value.trim() === "" || !isEmailValid(emailInput.value.trim())){
            valid = false;
            emailInput.nextElementSibling.textContent = "Enter a valid email";
        } else {
            emailInput.nextElementSibling.textContent = "";
        }
    }
    if(dobInput){
        if(dobInput.value.trim() === ""){
            valid = false;
            dobInput.nextElementSibling.textContent = "Date of birth is required";
        } else {
            dobInput.nextElementSibling.textContent = "";
        }
    }
    if(sexSelect){
        if(sexSelect.value === ""){
            valid = false;
            sexSelect.nextElementSibling.textContent = "Sex is required";
        } else {
            sexSelect.nextElementSibling.textContent = "";
        }
    }
    if(passwordInput){
        if(passwordInput.value.trim() === "" || passwordInput.value.trim().length < 8){
            valid = false;
            passwordInput.nextElementSibling.textContent = "Password must be at least 8 characters";
        } else {
            passwordInput.nextElementSibling.textContent = "";
        }
    }
    if(confirmPasswordInput){
        if(confirmPasswordInput.value.trim() === "" || passwordInput.value.trim() !== confirmPasswordInput.value.trim()){
            valid = false;
            confirmPasswordInput.nextElementSibling.textContent = "Passwords do not match";
        } else {
            confirmPasswordInput.nextElementSibling.textContent = "";
        }
    }
    return valid;
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
    btn.addEventListener("click", (e) => {
        if (validateCurrentStep()) {
            formStepsNum++;
            updateFormSteps();
            updateProgressbar();
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