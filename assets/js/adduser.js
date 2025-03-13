document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("adduser"); // Scope to the modal
    const prevBtns = modal.querySelectorAll(".btn-prev");
    const nextBtns = modal.querySelectorAll(".btn-next");
    const progress = modal.querySelector("#progress");
    const formSteps = modal.querySelectorAll(".form-step");
    const progressSteps = modal.querySelectorAll(".progress-step");
  
    let formStepsNum = 0;
  
    nextBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        formStepsNum = Math.min(formStepsNum + 1, formSteps.length - 1); // Prevent overflow
        updateFormSteps();
        updateProgressbar();
      });
    });
  
    prevBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        formStepsNum = Math.max(formStepsNum - 1, 0); // Prevent underflow
        updateFormSteps();
        updateProgressbar();
      });
    });
  
    function updateFormSteps() {
      formSteps.forEach((formStep, idx) => {
        formStep.classList.toggle("form-step-active", idx === formStepsNum);
      });
    }
  
    function updateProgressbar() {
      progressSteps.forEach((progressStep, idx) => {
        progressStep.classList.toggle("progress-step-active", idx <= formStepsNum);
      });
  
      const progressActive = modal.querySelectorAll(".progress-step-active");
      progress.style.width =
        ((progressActive.length - 1) / (progressSteps.length - 1)) * 100 + "%";
    }
  });
  