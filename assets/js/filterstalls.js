function filterStalls(status) {
    const openStalls = document.querySelectorAll('.col.open-stall');
    const closedStalls = document.querySelectorAll('.col.closed-stall');

    if (status === 'open') {
        openStalls.forEach(stall => {
            stall.hidden = false;
        });
        closedStalls.forEach(stall => {
            stall.hidden = true;
        });
    } else if (status === 'closed') {
        closedStalls.forEach(stall => {
            stall.hidden = false;
        });
        openStalls.forEach(stall => {
            stall.hidden = true;
        });
    }
}