function addOperatingHours() {
    const openHour = String(document.getElementById('open_hour').value).padStart(2, '0');
    const openMinute = String(document.getElementById('open_minute').value).padStart(2, '0');
    const openAmpm = document.getElementById('open_ampm').value;
    const closeHour = String(document.getElementById('close_hour').value).padStart(2, '0');
    const closeMinute = String(document.getElementById('close_minute').value).padStart(2, '0');
    const closeAmpm = document.getElementById('close_ampm').value;

    const days = Array.from(document.querySelectorAll('input[name="days"]:checked'))
                    .map(checkbox => checkbox.value)
                    .join(', ');

    if (days === '') {
        alert("Please select at least one day.");
        return;
    }

    const scheduleText = `${days} <br> ${openHour}:${openMinute} ${openAmpm} - ${closeHour}:${closeMinute} ${closeAmpm}`;
    const scheduleContainer = document.getElementById("scheduleContainer");

    const scheduleItem = document.createElement("p");
    scheduleItem.innerHTML = scheduleText;

    const deleteButton = document.createElement("button");
    deleteButton.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>'; 
    deleteButton.classList.add("delete-btn");
    deleteButton.onclick = function() {
        scheduleContainer.removeChild(scheduleItem);
    };

    scheduleItem.insertBefore(deleteButton, scheduleItem.firstChild);
    scheduleContainer.appendChild(scheduleItem);

    // Reset each input field manually
    document.getElementById('open_hour').selectedIndex = 0;
    document.getElementById('open_minute').selectedIndex = 0;
    document.getElementById('open_ampm').selectedIndex = 0;
    document.getElementById('close_hour').selectedIndex = 0;
    document.getElementById('close_minute').selectedIndex = 0;
    document.getElementById('close_ampm').selectedIndex = 0;

    document.querySelectorAll('input[name="days"]').forEach(checkbox => checkbox.checked = false);
}