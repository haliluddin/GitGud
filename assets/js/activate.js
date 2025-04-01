document.addEventListener("DOMContentLoaded", () => {
    const table = document.querySelector("table");
    let currentRow = null; // Variable to track the current row being acted upon

    // Deactivate User
    table.addEventListener("click", (event) => {
        if (event.target.closest("[data-bs-target='#deactivateuser']")) {
            currentRow = event.target.closest("tr"); // Save the current row
        }
    });

    const deactivateModal = document.getElementById("deactivateuser");
    const submitDeactivation = document.getElementById("submitDeactivation");
    
    if (submitDeactivation) {
        submitDeactivation.addEventListener("click", () => {
            // Get form data
            const userId = document.getElementById("deactivateUserId").value;
            const duration = deactivateModal.querySelector("input[name='deactivation_duration']:checked");
            const reason = document.getElementById("deactivation_reason").value;
            
            // Validate form
            if (!duration) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please select a deactivation duration',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            if (!reason.trim()) {
                Swal.fire({
                    title: 'Error',
                    text: 'Please provide a reason for deactivation',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Send AJAX request
            fetch("classes/deactivate_user.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `deactivate_user_id=${userId}&deactivation_duration=${duration.value}&deactivation_reason=${encodeURIComponent(reason)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(deactivateModal).hide();
                    Swal.fire({
                        title: 'User Deactivated',
                        text: 'The user has been deactivated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to deactivate user: ' + (data.message || ''),
                        icon: 'error',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    title: 'Error',
                    text: 'An error occurred while processing your request.',
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            });
        });
    }

    // Activate User
    const activateModal = document.getElementById("activateuser");
    table.addEventListener("click", (event) => {
        if (event.target.closest("[data-bs-target='#activateuser']")) {
            currentRow = event.target.closest("tr"); // Save the current row
        }
    });

    activateModal.querySelector(".btn-primary").addEventListener("click", () => {
        if (currentRow) {
            const userId = currentRow.querySelector("[data-user-id]").getAttribute("data-user-id");

            fetch("classes/activate_user.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `action=activateUser&user_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'User Activated',
                        text: 'The user has been activated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to activate user.',
                        icon: 'failed',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});