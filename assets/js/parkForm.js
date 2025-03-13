/* PROGRESS WITH STEPS */

const prevBtns = document.querySelectorAll(".btn-prev");
const nextBtns = document.querySelectorAll(".btn-next");
const progress = document.getElementById("progress");
const formSteps = document.querySelectorAll(".form-step");
const progressSteps = document.querySelectorAll(".progress-step");

let formStepsNum = 0;

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
    btn.addEventListener("click", (event) => {
        event.preventDefault();
        formStepsNum++;
        updateFormSteps();
        updateProgressbar();
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